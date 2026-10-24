"""Webserver voor het scrumboard.

Starten:  python -m scrumboard.server --poort 8000
Daarna openen in de browser: http://<ip-van-de-pi>:8000
"""

import argparse
import json
import mimetypes
import re
from http import HTTPStatus
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
from pathlib import Path

from .opslag import GeenToegang, NietGevonden, OngeldigeInvoer, Opslag

STATISCH = Path(__file__).parent / "static"
MAX_BODY = 64 * 1024

ROUTES = []


def route(methode, patroon, beveiligd=True):
    def registreer(functie):
        ROUTES.append((methode, re.compile(f"^{patroon}$"), beveiligd, functie))
        return functie

    return registreer


@route("GET", "/api/groepen", beveiligd=False)
def lijst_groepen(opslag, gegevens):
    return opslag.groepen()


@route("POST", "/api/groepen", beveiligd=False)
def nieuwe_groep(opslag, gegevens):
    return opslag.maak_groep(gegevens.get("naam"), gegevens.get("pin"))


@route("POST", r"/api/groepen/(\d+)/login", beveiligd=False)
def log_in(opslag, gegevens, groep_id):
    return opslag.log_in(groep_id, gegevens.get("pin"))


@route("GET", r"/api/groepen/(\d+)/bord")
def bord(opslag, gegevens, groep_id):
    return opslag.bord(groep_id)


@route("PUT", r"/api/groepen/(\d+)/sprint")
def sprint(opslag, gegevens, groep_id):
    opslag.wijzig_sprint(groep_id, gegevens)


@route("POST", r"/api/groepen/(\d+)/leden")
def nieuw_lid(opslag, gegevens, groep_id):
    opslag.voeg_lid_toe(groep_id, gegevens.get("naam"))


@route("DELETE", r"/api/groepen/(\d+)/leden")
def weg_lid(opslag, gegevens, groep_id):
    opslag.verwijder_lid(groep_id, gegevens.get("naam"))


@route("POST", r"/api/groepen/(\d+)/kaarten")
def nieuwe_kaart(opslag, gegevens, groep_id):
    return {"id": opslag.maak_kaart(groep_id, gegevens)}


@route("PUT", r"/api/groepen/(\d+)/kaarten/(\d+)")
def wijzig_kaart(opslag, gegevens, groep_id, kaart_id):
    opslag.wijzig_kaart(groep_id, kaart_id, gegevens)


@route("DELETE", r"/api/groepen/(\d+)/kaarten/(\d+)")
def weg_kaart(opslag, gegevens, groep_id, kaart_id):
    opslag.verwijder_kaart(groep_id, kaart_id)


class Handler(BaseHTTPRequestHandler):
    opslag = None  # wordt in maak_server gezet

    def do_GET(self):
        if self.path.startswith("/api/"):
            self._api("GET")
        else:
            self._statisch()

    def do_POST(self):
        self._api("POST")

    def do_PUT(self):
        self._api("PUT")

    def do_DELETE(self):
        self._api("DELETE")

    def log_message(self, *args):
        pass

    def _api(self, methode):
        pad = self.path.split("?", 1)[0]
        for route_methode, patroon, beveiligd, functie in ROUTES:
            treffer = patroon.match(pad)
            if not treffer or route_methode != methode:
                continue
            try:
                ids = [int(x) for x in treffer.groups()]
                gegevens = self._lees_json()
                if beveiligd:
                    self.opslag.controleer(ids[0], self.headers.get("X-Token", ""))
                antwoord = functie(self.opslag, gegevens, *ids)
            except OngeldigeInvoer as fout:
                return self._json(HTTPStatus.BAD_REQUEST, {"fout": str(fout)})
            except GeenToegang as fout:
                return self._json(HTTPStatus.FORBIDDEN, {"fout": str(fout)})
            except NietGevonden as fout:
                return self._json(HTTPStatus.NOT_FOUND, {"fout": str(fout)})
            return self._json(HTTPStatus.OK, {"ok": True} if antwoord is None else antwoord)
        self._json(HTTPStatus.NOT_FOUND, {"fout": "onbekende pagina"})

    def _lees_json(self):
        lengte = int(self.headers.get("Content-Length") or 0)
        if lengte > MAX_BODY:
            raise OngeldigeInvoer("verzoek is te groot")
        if not lengte:
            return {}
        try:
            gegevens = json.loads(self.rfile.read(lengte))
        except (ValueError, UnicodeDecodeError):
            raise OngeldigeInvoer("ongeldige JSON") from None
        if not isinstance(gegevens, dict):
            raise OngeldigeInvoer("verwacht een JSON-object")
        return gegevens

    def _json(self, status, inhoud):
        data = json.dumps(inhoud).encode()
        self.send_response(status)
        self.send_header("Content-Type", "application/json; charset=utf-8")
        self.send_header("Content-Length", str(len(data)))
        self.send_header("Cache-Control", "no-store")
        self.end_headers()
        self.wfile.write(data)

    def _statisch(self):
        naam = self.path.split("?", 1)[0].lstrip("/") or "index.html"
        bestand = (STATISCH / naam).resolve()
        if STATISCH.resolve() not in bestand.parents or not bestand.is_file():
            bestand = STATISCH / "index.html"
        data = bestand.read_bytes()
        soort = mimetypes.guess_type(bestand.name)[0] or "application/octet-stream"
        self.send_response(HTTPStatus.OK)
        self.send_header("Content-Type", f"{soort}; charset=utf-8" if soort.startswith("text") or soort.endswith("javascript") else soort)
        self.send_header("Content-Length", str(len(data)))
        self.end_headers()
        self.wfile.write(data)


def maak_server(host, poort, opslag):
    handler = type("ScrumHandler", (Handler,), {"opslag": opslag})
    return ThreadingHTTPServer((host, poort), handler)


def main():
    parser = argparse.ArgumentParser(description="Scrumboard voor studentengroepen")
    parser.add_argument("--host", default="0.0.0.0")
    parser.add_argument("--poort", type=int, default=8000)
    parser.add_argument("--database", default="scrumboard.db")
    args = parser.parse_args()

    server = maak_server(args.host, args.poort, Opslag(args.database))
    print(f"Scrumboard draait op http://{args.host}:{args.poort}  (Ctrl+C om te stoppen)")
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        pass


if __name__ == "__main__":
    main()
