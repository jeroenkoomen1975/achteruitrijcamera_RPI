#!/usr/bin/env python3
"""Nachtzender: number station voor de open dag (Raspberry Pi 5).

Start met:   python3 nachtzender.py
Daarna:      http://<pi>:8080/          scorebord (groot scherm)
             http://<pi>:8080/beheer    spelleider: nieuwe ronde starten
Alleen Python-standaardbibliotheek nodig; espeak-ng en aplay voor geluid.
"""

import argparse
import json
import random
import socket
import sqlite3
import time
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
from pathlib import Path
from urllib.parse import urlparse

from omroeper import Omroeper, uitzendtekst
from posten import maak_posten
from spel import Spel

HIER = Path(__file__).resolve().parent
WEB = HIER / "web"

MORSE = {"0": "-----", "1": ".----", "2": "..---", "3": "...--", "4": "....-",
         "5": ".....", "6": "-....", "7": "--...", "8": "---..", "9": "----."}

PAGINAS = {"/": "scorebord.html", "/beheer": "beheer.html", "/radio": "radio.html",
           "/morse": "morse.html", "/sql": "sql.html", "/kluis": "kluis.html"}
TYPES = {".html": "text/html; charset=utf-8", ".css": "text/css", ".js": "application/javascript"}


def voer_sql_uit(db_pad, query, max_rijen=200):
    """Alleen-lezen SQL voor de databasepost. Geeft (kolommen, rijen)."""
    if not query.strip().lower().startswith(("select", "with")):
        raise ValueError("Alleen SELECT-query's zijn toegestaan.")
    if not Path(db_pad).exists():
        raise ValueError("Er is nog geen database: start eerst een ronde.")
    db = sqlite3.connect(f"file:{db_pad}?mode=ro", uri=True, timeout=2)
    start = time.monotonic()
    db.set_progress_handler(lambda: time.monotonic() - start > 2, 10000)
    try:
        cur = db.execute(query)
        kolommen = [k[0] for k in cur.description or []]
        return kolommen, cur.fetchmany(max_rijen)
    finally:
        db.close()


def maak_handler(spel, posten_map):
    db_pad = Path(posten_map) / "database" / "onderschept.db"

    class Handler(BaseHTTPRequestHandler):
        server_version = "Nachtzender/1.0"

        def log_message(self, *args):
            pass  # de ESP32's pollen vaak; houd de terminal rustig

        def _json(self, data, status=200, headers=None):
            body = json.dumps(data).encode()
            self.send_response(status)
            self.send_header("Content-Type", "application/json")
            self.send_header("Content-Length", str(len(body)))
            self.send_header("Cache-Control", "no-store")
            self.send_header("Access-Control-Allow-Origin", "*")
            for k, v in (headers or {}).items():
                self.send_header(k, v)
            self.end_headers()
            self.wfile.write(body)

        def _bestand(self, pad):
            if not pad.is_file():
                return self._json({"fout": "niet gevonden"}, 404)
            body = pad.read_bytes()
            self.send_response(200)
            self.send_header("Content-Type", TYPES.get(pad.suffix, "application/octet-stream"))
            self.send_header("Content-Length", str(len(body)))
            self.end_headers()
            self.wfile.write(body)

        def _lees_json(self):
            lengte = min(int(self.headers.get("Content-Length") or 0), 10000)
            try:
                return json.loads(self.rfile.read(lengte) or b"{}")
            except ValueError:
                return {}

        def do_GET(self):
            pad = urlparse(self.path).path.rstrip("/") or "/"
            if pad in PAGINAS:
                return self._bestand(WEB / PAGINAS[pad])
            if pad == "/stijl.css":
                return self._bestand(WEB / "stijl.css")
            if pad == "/api/status":
                data = spel.status()
                ronde = spel.actieve_ronde()
                data["uitzending"] = uitzendtekst(ronde) if ronde else ""
                return self._json(data)
            if pad == "/api/beheer":
                return self._json(spel.beheer_status())
            if pad == "/api/radio":
                # POST NETWERKEN: sleuteldeel 1 reist mee in een HTTP-header.
                ronde = spel.actieve_ronde()
                if ronde:
                    groepen = ["".join(map(str, ronde.cijfertekst))]
                    headers = {"X-Sleuteldeel-1": str(ronde.sleutel[0])}
                else:
                    groepen = ["".join(random.choice("0123456789") for _ in range(5)) for _ in range(3)]
                    headers = {}
                return self._json({"zender": "NACHTZENDER", "live": bool(ronde), "groepen": groepen}, headers=headers)
            if pad == "/api/morse":
                # POST EMBEDDED: sleuteldeel 2 als morse voor de ESP32 met zoemer.
                cijfer = spel.sleuteldeel(2)
                teken = "" if cijfer is None else str(cijfer)
                return self._json({"teken": teken, "morse": MORSE.get(teken, "")})
            return self._json({"fout": "niet gevonden"}, 404)

        def do_POST(self):
            pad = urlparse(self.path).path.rstrip("/")
            data = self._lees_json()
            if pad == "/api/kluis":
                return self._json(spel.probeer_kluis(data.get("code", "")))
            if pad == "/api/sql":
                try:
                    kolommen, rijen = voer_sql_uit(db_pad, str(data.get("query", "")))
                    return self._json({"kolommen": kolommen, "rijen": rijen})
                except (ValueError, sqlite3.Error) as fout:
                    return self._json({"fout": str(fout)}, 400)
            if pad == "/api/ronde":
                ronde = spel.nieuwe_ronde(data.get("team", ""))
                return self._json({"ok": True, "ronde": ronde.nummer})
            if pad == "/api/stop":
                spel.stop_ronde()
                return self._json({"ok": True})
            if pad == "/api/wis-scores":
                spel.wis_scores()
                return self._json({"ok": True})
            return self._json({"fout": "niet gevonden"}, 404)

    return Handler


def eigen_ip():
    try:
        with socket.socket(socket.AF_INET, socket.SOCK_DGRAM) as s:
            s.connect(("10.255.255.255", 1))
            return s.getsockname()[0]
    except OSError:
        return "127.0.0.1"


def main():
    p = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    p.add_argument("--poort", type=int, default=8080)
    p.add_argument("--posten", default=str(HIER / "posten"),
                   help="map voor het materiaal van de posten (Linux-archief, database)")
    p.add_argument("--stil", action="store_true", help="geen geluid, alleen tekst")
    p.add_argument("--audio-apparaat", help="ALSA-apparaat voor aplay, bv. plughw:2,0 (zie 'aplay -l')")
    p.add_argument("--pauze", type=int, default=6, help="seconden stilte tussen twee uitzendingen")
    args = p.parse_args()

    rng = random.SystemRandom()
    posten_map = Path(args.posten)
    spel = Spel(scores_pad=posten_map / "scores.json", rng=rng,
                bij_nieuwe_ronde=lambda ronde: maak_posten(posten_map, ronde, rng))
    omroeper = Omroeper(spel, posten_map / "melodie.wav", apparaat=args.audio_apparaat,
                        stil=args.stil, pauze=args.pauze)
    omroeper.start()

    server = ThreadingHTTPServer(("0.0.0.0", args.poort), maak_handler(spel, posten_map))
    ip = eigen_ip()
    print(f"Nachtzender in de lucht!  Scorebord: http://{ip}:{args.poort}/")
    print(f"                          Beheer:    http://{ip}:{args.poort}/beheer")
    print(f"Materiaal posten in {posten_map.resolve()}")
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\nZender uit.")


if __name__ == "__main__":
    main()
