"""De spelregels van de quiz.

Deze module weet niets van netwerk of scherm: de server roept `druk()` aan
als iemand op een knop drukt en `tik()` een paar keer per seconde. Na elke
wijziging gaat `versie` omhoog; de server stuurt dan `toestand()` naar het
scherm en `led_patroon()` naar de knoppenkasten.

Verloop:  LOBBY -> AFTELLEN -> (VRAAG -> UITSLAG) x 10 -> EINDE -> LOBBY
"""

import random
import time
from dataclasses import dataclass

from . import config
from .records import Records
from .vragen import Vraag, kies_vragen

LOBBY = "lobby"
AFTELLEN = "aftellen"
VRAAG = "vraag"
UITSLAG = "uitslag"
EINDE = "einde"

SPELER_NUMMERS = (1, 2)


@dataclass
class Speler:
    nummer: int
    laatst_gezien: float | None = None  # laatste bericht van de knoppenkast
    klaar: bool = False                 # heeft in de lobby op een knop gedrukt
    meedoen: bool = False               # speelt mee in het huidige spel
    score: int = 0
    aantal_goed: int = 0
    antwoord: int | None = None         # gekozen knop bij de huidige vraag
    antwoord_tijd: float | None = None  # seconden na het tonen van de vraag
    punten: int = 0                     # punten verdiend bij de huidige vraag

    def nieuw_spel(self):
        self.klaar = False
        self.meedoen = False
        self.score = 0
        self.aantal_goed = 0
        self.nieuwe_vraag()

    def nieuwe_vraag(self):
        self.antwoord = None
        self.antwoord_tijd = None
        self.punten = 0


class Spel:
    def __init__(self, vragenbank: list[Vraag], klok=time.monotonic, rng=None, records: Records | None = None):
        self.bank = vragenbank
        self.records = records or Records(pad=None)
        self.nieuwe_records = {"score": False, "snelste": False}  # verbroken in dit spel
        self.klok = klok
        self.rng = rng or random.Random()
        self.spelers = {n: Speler(n) for n in SPELER_NUMMERS}
        self._verbonden = {n: False for n in SPELER_NUMMERS}
        self.versie = 0
        self.naar_lobby()

    # --- hulpfuncties ---------------------------------------------------

    def _gewijzigd(self):
        self.versie += 1

    def _zet_fase(self, fase: str, duur: float | None):
        self.fase = fase
        self.fase_start = self.klok()
        self.fase_duur = duur  # None = wacht tot er iets gebeurt
        self._gewijzigd()

    def resterend(self) -> float | None:
        """Seconden tot de volgende fase, of None als er geen klok loopt."""
        if self.fase_duur is None:
            return None
        return max(0.0, self.fase_start + self.fase_duur - self.klok())

    @property
    def deelnemers(self) -> list[Speler]:
        return [s for s in self.spelers.values() if s.meedoen]

    @property
    def huidige_vraag(self) -> Vraag | None:
        if 0 <= self.vraag_index < len(self.vragen):
            return self.vragen[self.vraag_index]
        return None

    def is_verbonden(self, nummer: int) -> bool:
        gezien = self.spelers[nummer].laatst_gezien
        return gezien is not None and self.klok() - gezien < config.KNOP_TIMEOUT_S

    def winnaar(self) -> int | None:
        """Nummer van de winnaar, 0 bij gelijkspel, None als er alleen gespeeld is."""
        deelnemers = self.deelnemers
        if len(deelnemers) < 2:
            return None
        scores = sorted(deelnemers, key=lambda s: s.score, reverse=True)
        if scores[0].score == scores[1].score:
            return 0
        return scores[0].nummer

    # --- overgangen -----------------------------------------------------

    def naar_lobby(self):
        for speler in self.spelers.values():
            speler.nieuw_spel()
        self.vragen: list[Vraag] = []
        self.vraag_index = -1
        self._zet_fase(LOBBY, None)

    def _start(self, nummers: list[int]):
        for n in nummers:
            self.spelers[n].meedoen = True
        self.vragen = kies_vragen(self.bank, config.AANTAL_VRAGEN, self.rng)
        self.vraag_index = -1
        self.nieuwe_records = {"score": False, "snelste": False}
        self._zet_fase(AFTELLEN, config.AFTELLEN_S)

    def _volgende_vraag(self):
        self.vraag_index += 1
        for speler in self.spelers.values():
            speler.nieuwe_vraag()
        self._zet_fase(VRAAG, config.VRAAG_S)

    def _toon_uitslag(self):
        vraag = self.huidige_vraag
        for speler in self.deelnemers:
            if speler.antwoord == vraag.goed:
                snelheid = 1 - speler.antwoord_tijd / config.VRAAG_S
                snelheid = min(1.0, max(0.0, snelheid))
                speler.punten = config.PUNTEN_GOED + round(config.PUNTEN_SNELHEID_MAX * snelheid)
                speler.score += speler.punten
                speler.aantal_goed += 1
        # Snelste eerst, zodat bij twee goede antwoorden alleen de snelste het record pakt.
        goed = [s for s in self.deelnemers if s.antwoord == vraag.goed]
        for speler in sorted(goed, key=lambda s: s.antwoord_tijd):
            if self.records.nieuw_antwoord(speler.antwoord_tijd, speler.nummer):
                self.nieuwe_records["snelste"] = True
        self._zet_fase(UITSLAG, config.UITSLAG_S)

    def _naar_einde(self):
        for speler in sorted(self.deelnemers, key=lambda s: s.score, reverse=True):
            if self.records.nieuwe_score(speler.score, speler.nummer):
                self.nieuwe_records["score"] = True
        self._zet_fase(EINDE, config.EINDE_S)

    # --- invoer ---------------------------------------------------------

    def heartbeat(self, nummer: int):
        """De knoppenkast van `nummer` heeft iets van zich laten horen."""
        if nummer in self.spelers:
            self.spelers[nummer].laatst_gezien = self.klok()
            self._controleer_verbindingen()

    def druk(self, nummer: int, knop: int) -> bool:
        """Speler `nummer` drukt op knop 0-3. Geeft True als het meetelt."""
        if nummer not in self.spelers or not 0 <= knop < config.AANTAL_KNOPPEN:
            return False
        speler = self.spelers[nummer]

        if self.fase == LOBBY:
            if speler.klaar:
                return False
            speler.klaar = True
            klaar = [s.nummer for s in self.spelers.values() if s.klaar]
            if len(klaar) == len(self.spelers):
                self._start(klaar)
            else:
                # De klok loopt: komt er niemand bij, dan speel je alleen.
                self._zet_fase(LOBBY, config.WACHT_OP_TEGENSTANDER_S)
            return True

        if self.fase == VRAAG:
            if not speler.meedoen or speler.antwoord is not None:
                return False  # eerste keuze telt, daarna zit je vast
            speler.antwoord = knop
            speler.antwoord_tijd = self.klok() - self.fase_start
            self._gewijzigd()
            if all(s.antwoord is not None for s in self.deelnemers):
                self._toon_uitslag()
            return True

        if self.fase == EINDE:
            if self.klok() - self.fase_start < config.EINDE_BLOKKEER_S:
                return False  # voorkom dat je per ongeluk het eindscherm wegdrukt
            self.naar_lobby()
            return self.druk(nummer, knop)  # telt meteen als 'klaar'

        return False

    def tik(self):
        """Laat de tijd verstrijken; roep dit een paar keer per seconde aan."""
        self._controleer_verbindingen()
        rest = self.resterend()
        if rest is None or rest > 0:
            return

        if self.fase == LOBBY:
            klaar = [s.nummer for s in self.spelers.values() if s.klaar]
            if klaar:
                self._start(klaar)  # tegenstander kwam niet: solo
            else:
                self._zet_fase(LOBBY, None)
        elif self.fase == AFTELLEN:
            self._volgende_vraag()
        elif self.fase == VRAAG:
            self._toon_uitslag()
        elif self.fase == UITSLAG:
            if self.vraag_index + 1 < len(self.vragen):
                self._volgende_vraag()
            else:
                self._naar_einde()
        elif self.fase == EINDE:
            self.naar_lobby()

    def _controleer_verbindingen(self):
        for n in SPELER_NUMMERS:
            nu = self.is_verbonden(n)
            if nu != self._verbonden[n]:
                self._verbonden[n] = nu
                self._gewijzigd()

    # --- uitvoer --------------------------------------------------------

    def led_patroon(self, nummer: int) -> str:
        """Lampjes in de knoppen: per knop '0' uit, '1' aan, 'B' knipperen."""
        speler = self.spelers[nummer]
        alles = config.AANTAL_KNOPPEN
        if self.fase == LOBBY:
            return "1" * alles if speler.klaar else "B" * alles
        if not speler.meedoen:
            return "0" * alles
        if self.fase == VRAAG:
            if speler.antwoord is None:
                return "1" * alles
            return "".join("1" if i == speler.antwoord else "0" for i in range(alles))
        if self.fase == UITSLAG:
            goed = self.huidige_vraag.goed
            return "".join("B" if i == goed else "0" for i in range(alles))
        if self.fase == EINDE:
            gewonnen = self.winnaar() in (None, 0, nummer)
            return "B" * alles if gewonnen else "0" * alles
        return "0" * alles

    def toestand(self) -> dict:
        """Alles wat het scherm nodig heeft, als JSON-vriendelijke dict."""
        rest = self.resterend()
        vraag = self.huidige_vraag
        onthullen = self.fase in (UITSLAG, EINDE)

        vraag_data = None
        if vraag is not None and self.fase in (VRAAG, UITSLAG):
            vraag_data = {
                "categorie": vraag.categorie,
                "categorie_naam": vraag.categorie_naam,
                "icoon": vraag.icoon,
                "tekst": vraag.tekst,
                "antwoorden": list(vraag.antwoorden),
                "goed": vraag.goed if onthullen else None,
                "uitleg": vraag.uitleg if onthullen else None,
            }

        spelers = []
        for s in self.spelers.values():
            spelers.append({
                "nummer": s.nummer,
                "verbonden": self.is_verbonden(s.nummer),
                "klaar": s.klaar,
                "meedoen": s.meedoen,
                "score": s.score,
                "aantal_goed": s.aantal_goed,
                "heeft_geantwoord": s.antwoord is not None,
                # Het gekozen antwoord blijft geheim tot de uitslag.
                "antwoord": s.antwoord if onthullen else None,
                "antwoord_tijd": round(s.antwoord_tijd, 2) if onthullen and s.antwoord_tijd is not None else None,
                "punten": s.punten if onthullen else 0,
            })

        return {
            "fase": self.fase,
            "versie": self.versie,
            "resterend_ms": None if rest is None else round(rest * 1000),
            "duur_ms": None if self.fase_duur is None else round(self.fase_duur * 1000),
            "vraag_nr": self.vraag_index + 1,
            "aantal_vragen": len(self.vragen) or config.AANTAL_VRAGEN,
            "vraag": vraag_data,
            "spelers": spelers,
            "winnaar": self.winnaar() if self.fase == EINDE else None,
            "records": self.records.als_dict(),
            "nieuwe_records": self.nieuwe_records,
            "categorieen": sorted({(v.categorie_naam, v.icoon) for v in self.bank}),
        }
