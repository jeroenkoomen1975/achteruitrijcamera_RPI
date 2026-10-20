"""Leest de getallen voor via de speaker, zoals een echt number station.

Gebruikt espeak-ng (stem) en aplay (geluid). Ontbreken die, dan draait alles
gewoon door en verschijnt de tekst alleen in de terminal en op het scorebord.
"""

import math
import shutil
import struct
import subprocess
import threading
import time
import wave
from pathlib import Path

from spel import AGENT, CIJFERS_NL

# Herkenningsmelodie (frequentie in Hz, duur in seconden); 0 = stilte.
MELODIE = [(659, .25), (587, .25), (523, .5), (0, .1), (659, .25), (587, .25), (523, .5),
           (0, .1), (392, .25), (440, .25), (494, .25), (523, .75)]


def uitzendtekst(ronde):
    cijfers = ". ".join(CIJFERS_NL[c] for c in ronde.cijfertekst)
    return (f"Hier is de Nachtzender. Bericht voor agent {AGENT.lower()}. "
            f"{cijfers}. Ik herhaal. {cijfers}. Einde bericht.")


def maak_melodie(pad, rate=22050):
    pad = Path(pad)
    if pad.exists():
        return pad
    frames = bytearray()
    for freq, duur in MELODIE:
        n = int(rate * duur)
        for i in range(n):
            # Zachte in- en uitfade per toon voorkomt klikjes.
            omhulling = min(1, i / 400, (n - i) / 400)
            waarde = 0.35 * omhulling * math.sin(2 * math.pi * freq * i / rate) if freq else 0
            frames += struct.pack("<h", int(waarde * 32767))
    pad.parent.mkdir(parents=True, exist_ok=True)
    with wave.open(str(pad), "wb") as w:
        w.setnchannels(1)
        w.setsampwidth(2)
        w.setframerate(rate)
        w.writeframes(bytes(frames))
    return pad


class Omroeper(threading.Thread):
    def __init__(self, spel, melodie_pad, apparaat=None, stil=False, pauze=6):
        super().__init__(daemon=True)
        self.spel = spel
        self.melodie = maak_melodie(melodie_pad)
        self.apparaat = apparaat
        self.pauze = pauze
        self.geluid = not stil and shutil.which("espeak-ng") and shutil.which("aplay")
        if not stil and not self.geluid:
            print("[omroeper] espeak-ng/aplay niet gevonden: alleen tekst, geen geluid.")

    def _aplay(self):
        return ["aplay", "-q"] + (["-D", self.apparaat] if self.apparaat else [])

    def _wacht_op(self, ronde, processen):
        """Wacht tot de processen klaar zijn; stop ze als er een andere ronde loopt."""
        while any(p.poll() is None for p in processen):
            if self.spel.actieve_ronde() is not ronde:
                for p in processen:
                    p.terminate()
                    p.wait()
                return False
            time.sleep(0.1)
        return True

    def _speel(self, ronde, tekst):
        """Speel melodie + stem af; breek af zodra er een andere ronde loopt."""
        if not self.geluid:
            # Geen geluid: wacht ongeveer zo lang als het voorlezen zou duren.
            for _ in range(int(len(tekst) * 0.7)):
                if self.spel.actieve_ronde() is not ronde:
                    return
                time.sleep(0.1)
            return
        melodie = subprocess.Popen(self._aplay() + [str(self.melodie)])
        if not self._wacht_op(ronde, [melodie]):
            return
        stem = subprocess.Popen(["espeak-ng", "-v", "nl", "-s", "120", "-g", "15", "-p", "30",
                                 "--stdout", tekst], stdout=subprocess.PIPE)
        speaker = subprocess.Popen(self._aplay(), stdin=stem.stdout)
        stem.stdout.close()
        self._wacht_op(ronde, [stem, speaker])

    def run(self):
        while True:
            ronde = self.spel.actieve_ronde()
            if not ronde:
                time.sleep(1)
                continue
            tekst = uitzendtekst(ronde)
            print(f"[omroeper] ronde {ronde.nummer}: {tekst}")
            self._speel(ronde, tekst)
            # Wacht, maar reageer meteen als er een nieuwe ronde start.
            for _ in range(self.pauze * 10):
                if self.spel.actieve_ronde() is not ronde:
                    break
                time.sleep(0.1)
