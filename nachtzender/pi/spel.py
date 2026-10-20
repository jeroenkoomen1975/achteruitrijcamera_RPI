"""Spelregels van de Nachtzender: rondes, sleutels, de kluis en de scores.

Het bericht is een korte tekst ("KLUIS 4271") die met een codeboek in cijfers
wordt omgezet (A=01 ... Z=26, spatie=00, cijfer 0-9 = 30-39). Dat geeft
20 cijfers, verdeeld over 4 groepen van 5, zoals echte number stations doen.

Die cijfers worden versleuteld met een one-time pad:

    cijfertekst = (klaartekst + sleutel) mod 10     (per cijfer)
    klaartekst  = (cijfertekst - sleutel) mod 10

De zender roept de cijfertekst om. De 4 sleutelgroepen liggen verspreid over
de posten 1-4 (netwerken, embedded, linux, database). Op post 5 rekenen de
bezoekers het bericht terug en lezen ze de kluiscode af.
"""

import json
import random
import threading
import time
from dataclasses import dataclass, field
from pathlib import Path

GROEPEN = 4
GROEP = 5
CODE_LENGTE = 4
AGENT = "UIL"
ZENDER = "NACHTZENDER"
CIJFERS_NL = ["nul", "een", "twee", "drie", "vier", "vijf", "zes", "zeven", "acht", "negen"]

POSTEN = [
    ("netwerken", "Netwerken", "Bouw het netwerk en luister het verkeer af"),
    ("embedded", "Embedded / IoT", "Sluit de onderdelen aan en ontcijfer de morse"),
    ("linux", "Linux", "Doorzoek de computer van de zender"),
    ("database", "Databases", "Bouw een SQL-query en vind het bericht"),
    ("ontcijferen", "Programmeren", "Programmeer de ontcijferaar en lees de code"),
]

STIJLEN = ["nl", "de", "fa"]


def naar_codeboek(tekst):
    """'KLUIS 4271' -> '11122109190034323731'"""
    uit = []
    for teken in tekst.upper():
        if teken == " ":
            uit.append("00")
        elif teken.isdigit():
            uit.append("3" + teken)
        else:
            uit.append("%02d" % (ord(teken) - ord("A") + 1))
    return "".join(uit)


def uit_codeboek(cijfers):
    """'11122109190034323731' -> 'KLUIS 4271' (onbekende paren worden '?')"""
    uit = []
    for i in range(0, len(cijfers) - 1, 2):
        paar = int(cijfers[i:i + 2])
        if paar == 0:
            uit.append(" ")
        elif 1 <= paar <= 26:
            uit.append(chr(ord("A") + paar - 1))
        elif 30 <= paar <= 39:
            uit.append(str(paar - 30))
        else:
            uit.append("?")
    return "".join(uit)


def versleutel(klaar, sleutel):
    return "".join(str((int(k) + int(s)) % 10) for k, s in zip(klaar, sleutel))


def ontcijfer(cijfertekst, sleutel):
    return "".join(str((int(c) - int(s)) % 10) for c, s in zip(cijfertekst, sleutel))


def in_groepen(cijfers):
    return [cijfers[i:i + GROEP] for i in range(0, len(cijfers), GROEP)]


@dataclass
class Ronde:
    nummer: int
    team: str
    code: str
    sleutel: list          # 4 groepen van 5 cijfers, één per post
    roepcode: str
    stijl: str = "nl"
    start: float = field(default_factory=time.time)
    eind: float | None = None
    pogingen: int = 0
    gevonden: set = field(default_factory=set)

    @property
    def klaartekst(self):
        return naar_codeboek("KLUIS " + self.code)

    @property
    def cijfertekst(self):
        return in_groepen(versleutel(self.klaartekst, "".join(self.sleutel)))

    @property
    def opgelost(self):
        return self.eind is not None

    def duur(self, nu=None):
        return (self.eind or nu or time.time()) - self.start


class Spel:
    def __init__(self, scores_pad=None, rng=None, bij_nieuwe_ronde=None):
        self.rng = rng or random.SystemRandom()
        self.scores_pad = Path(scores_pad) if scores_pad else None
        self.bij_nieuwe_ronde = bij_nieuwe_ronde
        self.lock = threading.Lock()
        self.ronde = None
        self.teller = 0
        self.scores = self._laad_scores()

    def _laad_scores(self):
        if self.scores_pad and self.scores_pad.exists():
            try:
                return json.loads(self.scores_pad.read_text(encoding="utf-8"))
            except (OSError, ValueError):
                pass
        return []

    def _bewaar_scores(self):
        if self.scores_pad:
            self.scores_pad.parent.mkdir(parents=True, exist_ok=True)
            self.scores_pad.write_text(json.dumps(self.scores, indent=2), encoding="utf-8")

    def _cijfers(self, n):
        return "".join(str(self.rng.randrange(10)) for _ in range(n))

    def nieuwe_ronde(self, team, stijl="nl"):
        team = (team or "").strip()[:30] or "Team zonder naam"
        with self.lock:
            self.teller += 1
            ronde = Ronde(
                nummer=self.teller,
                team=team,
                code=self._cijfers(CODE_LENGTE),
                sleutel=[self._cijfers(GROEP) for _ in range(GROEPEN)],
                roepcode=self._cijfers(3),
                stijl=stijl if stijl in STIJLEN else "nl",
            )
            self.ronde = ronde
        if self.bij_nieuwe_ronde:
            self.bij_nieuwe_ronde(ronde)
        return ronde

    def stop_ronde(self):
        with self.lock:
            self.ronde = None

    def actieve_ronde(self):
        """De ronde die nog loopt, of None als er niets (meer) te kraken valt."""
        ronde = self.ronde
        return ronde if ronde and not ronde.opgelost else None

    def sleutelgroep(self, post):
        """Sleutelgroep van post 1..4 in de lopende ronde, of None."""
        ronde = self.actieve_ronde()
        return ronde.sleutel[post - 1] if ronde else None

    def controleer(self, post, waarde):
        """Laat een team een gevonden sleutelgroep nakijken (voor het scorebord)."""
        waarde = "".join(ch for ch in str(waarde) if ch.isdigit())
        with self.lock:
            ronde = self.actieve_ronde()
            if not ronde or post not in range(1, GROEPEN + 1):
                return False
            goed = waarde == ronde.sleutel[post - 1]
            if goed:
                ronde.gevonden.add(post)
            return goed

    def probeer_kluis(self, invoer):
        invoer = "".join(ch for ch in str(invoer) if ch.isdigit())
        with self.lock:
            ronde = self.ronde
            if not ronde:
                return {"open": False, "melding": "Er loopt geen missie."}
            if ronde.opgelost:
                return {"open": True, "melding": "Deze kluis is al gekraakt.", "tijd": round(ronde.duur(), 1)}
            ronde.pogingen += 1
            if invoer != ronde.code:
                return {"open": False, "melding": "Onjuiste code.", "pogingen": ronde.pogingen}
            ronde.eind = time.time()
            score = {
                "team": ronde.team,
                "tijd": round(ronde.duur(), 1),
                "pogingen": ronde.pogingen,
                "ronde": ronde.nummer,
                "moment": time.strftime("%H:%M"),
            }
            self.scores.append(score)
            self.scores.sort(key=lambda s: s["tijd"])
            self._bewaar_scores()
            return {"open": True, "melding": "Kluis open! Missie geslaagd.", "tijd": score["tijd"]}

    def wis_scores(self):
        with self.lock:
            self.scores = []
            self._bewaar_scores()

    def status(self):
        """Wat iedereen mag zien (scorebord): geen sleutels of codes."""
        ronde = self.ronde
        return {
            "zender": ZENDER,
            "agent": AGENT,
            "ronde": None if not ronde else {
                "nummer": ronde.nummer,
                "team": ronde.team,
                "roepcode": ronde.roepcode,
                "stijl": ronde.stijl,
                "cijfertekst": ronde.cijfertekst,
                "gevonden": sorted(ronde.gevonden),
                "duur": round(ronde.duur(), 1),
                "opgelost": ronde.opgelost,
                "pogingen": ronde.pogingen,
            },
            "scores": self.scores[:10],
            "posten": [{"id": i, "naam": n, "opdracht": o} for i, n, o in POSTEN],
        }

    def beheer_status(self):
        """Voor de spelleider: inclusief code en sleutel om te kunnen helpen."""
        data = self.status()
        if self.ronde:
            data["ronde"]["code"] = self.ronde.code
            data["ronde"]["sleutel"] = self.ronde.sleutel
            data["ronde"]["klaartekst"] = in_groepen(self.ronde.klaartekst)
        data["scores"] = self.scores
        return data
