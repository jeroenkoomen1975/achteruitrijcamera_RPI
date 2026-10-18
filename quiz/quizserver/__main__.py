"""Start de quizserver op de Raspberry Pi.

    python -m quizserver

- http://<ip>:8080 is het quizscherm (open dit in Chromium in kioskmodus)
- UDP-poort 4210 ontvangt de knoppen van de twee ESP32's
"""

import argparse
import asyncio
import json
import logging
from pathlib import Path

from aiohttp import WSMsgType, web

from . import config, protocol
from .spel import SPELER_NUMMERS, Spel
from .vragen import STANDAARD_BESTAND, laad_vragen

STATIC = Path(__file__).with_name("static")
log = logging.getLogger("quiz")


class QuizServer:
    def __init__(self, spel: Spel):
        self.spel = spel
        self.schermen: set[web.WebSocketResponse] = set()
        self.kasten: dict[int, tuple] = {}       # speler -> (ip, poort) van de ESP32
        self.laatste_volgnr: dict[int, int] = {}
        self.udp: asyncio.DatagramTransport | None = None
        self.verstuurde_versie = -1
        self.wekker = asyncio.Event()

    # --- ESP32-knoppen via UDP ----------------------------------------------

    def udp_ontvangen(self, data: bytes, adres):
        bericht = protocol.lees_bericht(data)
        if bericht is None or bericht.speler not in SPELER_NUMMERS:
            return
        nr = bericht.speler
        if self.kasten.get(nr) != adres:
            log.info("Knoppenkast speler %d gevonden op %s:%d", nr, *adres)
        self.kasten[nr] = adres
        self.spel.heartbeat(nr)

        if isinstance(bericht, protocol.Knop):
            self.udp.sendto(protocol.ack(bericht.volgnr), adres)
            if self.laatste_volgnr.get(nr) != bericht.volgnr:
                self.laatste_volgnr[nr] = bericht.volgnr
                telt = self.spel.druk(nr, bericht.knop)
                log.info("Speler %d drukt knop %d%s", nr, bericht.knop, "" if telt else " (genegeerd)")
                self.wekker.set()
        # Antwoord altijd met het ledpatroon: zo herstelt een gemist pakket zich vanzelf.
        self.udp.sendto(protocol.led(self.spel.led_patroon(nr)), adres)

    # --- quizscherm via WebSocket -------------------------------------------

    async def websocket(self, request):
        ws = web.WebSocketResponse(heartbeat=10)
        await ws.prepare(request)
        self.schermen.add(ws)
        log.info("Scherm verbonden (%d totaal)", len(self.schermen))
        await ws.send_str(json.dumps(self.spel.toestand()))
        try:
            async for msg in ws:
                if msg.type != WSMsgType.TEXT:
                    continue
                try:
                    data = json.loads(msg.data)
                    soort = data.get("type")
                    if soort == "druk":
                        speler, knop = int(data["speler"]), int(data["knop"])
                except (json.JSONDecodeError, AttributeError, KeyError, TypeError, ValueError):
                    continue
                # Toetsenbord als reserve voor de knoppen (handig om te testen).
                if soort == "druk":
                    self.spel.druk(speler, knop)
                elif soort == "reset":
                    log.info("Spel gereset vanaf het scherm")
                    self.spel.naar_lobby()
                self.wekker.set()
        finally:
            self.schermen.discard(ws)
        return ws

    async def index(self, request):
        return web.FileResponse(STATIC / "index.html")

    # --- hoofdlus -----------------------------------------------------------

    async def verstuur_als_gewijzigd(self):
        if self.spel.versie == self.verstuurde_versie:
            return
        self.verstuurde_versie = self.spel.versie
        tekst = json.dumps(self.spel.toestand())
        for ws in list(self.schermen):
            try:
                await ws.send_str(tekst)
            except ConnectionError:
                self.schermen.discard(ws)
        for nr, adres in self.kasten.items():
            self.udp.sendto(protocol.led(self.spel.led_patroon(nr)), adres)

    async def lus(self):
        while True:
            self.spel.tik()
            await self.verstuur_als_gewijzigd()
            try:
                await asyncio.wait_for(self.wekker.wait(), timeout=0.05)
            except asyncio.TimeoutError:
                pass
            self.wekker.clear()


class _UdpProtocol(asyncio.DatagramProtocol):
    def __init__(self, server: QuizServer):
        self.server = server

    def datagram_received(self, data, addr):
        self.server.udp_ontvangen(data, addr)


async def start(args):
    spel = Spel(laad_vragen(args.vragen))
    server = QuizServer(spel)

    loop = asyncio.get_running_loop()
    server.udp, _ = await loop.create_datagram_endpoint(
        lambda: _UdpProtocol(server), local_addr=("0.0.0.0", args.udp_poort), allow_broadcast=True)

    app = web.Application()
    app.router.add_get("/", server.index)
    app.router.add_get("/ws", server.websocket)
    app.router.add_static("/static", STATIC)
    runner = web.AppRunner(app)
    await runner.setup()
    await web.TCPSite(runner, "0.0.0.0", args.poort).start()

    log.info("%d vragen geladen", len(spel.bank))
    log.info("Quizscherm: http://localhost:%d  -  knoppen op UDP-poort %d", args.poort, args.udp_poort)
    await server.lus()


def main():
    parser = argparse.ArgumentParser(description="Quizserver voor twee ESP32-knoppenkasten")
    parser.add_argument("--poort", type=int, default=config.HTTP_POORT, help="HTTP-poort van het scherm")
    parser.add_argument("--udp-poort", type=int, default=config.UDP_POORT, help="UDP-poort voor de knoppen")
    parser.add_argument("--vragen", type=Path, default=STANDAARD_BESTAND, help="pad naar vragen.json")
    args = parser.parse_args()
    logging.basicConfig(level=logging.INFO, format="%(asctime)s %(levelname)s %(message)s", datefmt="%H:%M:%S")
    try:
        asyncio.run(start(args))
    except KeyboardInterrupt:
        pass


if __name__ == "__main__":
    main()
