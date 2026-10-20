"""Spelregels van de Nachtzender: rondes, sleutels, de kluis en de scores.

De puzzel is een one-time pad op cijfers:

    cijfertekst = (kluiscode + sleutel) mod 10     (per cijfer)
    kluiscode   = (cijfertekst - sleutel) mod 10

De zender roept de cijfertekst om. De vier sleutelcijfers liggen verspreid
over de vier posten (netwerken, embedded, linux, database). Wie ze allemaal
heeft, kan op de post programmeren de kluiscode uitrekenen.
"""

import json
import random
import threading
import time
from dataclasses import dataclass, field
from pathlib import Path

LENGTE = 4
AGENT = "UIL"
ZENDER = "NACHTZENDER"
CIJFERS_NL = ["nul", "een", "twee", "drie", "vier", "vijf", "zes", "zeven", "acht", "negen"]

POSTEN = [
    ("netwerken", "Netwerken", "Vang het radioverkeer af met Wireshark"),
    ("embedded", "Embedded / IoT", "Bouw de schakeling en ontcijfer de morse"),
    ("linux", "Linux", "Log in op de zender en doorzoek de logbestanden"),
    ("database", "Databases", "Vind het juiste bericht met een SQL-query"),
    ("programmeren", "Programmeren", "Maak het ontcijferprogramma af"),
]


def versleutel(code, sleutel):
    return [(c + k) % 10 for c, k in zip(code, sleutel)]


def ontcijfer(cijfertekst, sleutel):
    return [(c - k) % 10 for c, k in zip(cijfertekst, sleutel)]


@dataclass
class Ronde:
    nummer: int
    team: str
    code: list
    sleutel: list
    start: float = field(default_factory=time.time)
    eind: float | None = None
    pogingen: int = 0

    @property
    def cijfertekst(self):
        return versleutel(self.code, self.sleutel)

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
                return json.loads(self.scores_pad.read_text())
            except (OSError, ValueError):
                pass
        return []

    def _bewaar_scores(self):
        if self.scores_pad:
            self.scores_pad.parent.mkdir(parents=True, exist_ok=True)
            self.scores_pad.write_text(json.dumps(self.scores, indent=2))

    def nieuwe_ronde(self, team):
        team = (team or "").strip()[:30] or "Team zonder naam"
        with self.lock:
            self.teller += 1
            ronde = Ronde(
                nummer=self.teller,
                team=team,
                code=[self.rng.randrange(10) for _ in range(LENGTE)],
                sleutel=[self.rng.randrange(10) for _ in range(LENGTE)],
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

    def sleuteldeel(self, positie):
        """Sleutelcijfer 1..4 van de lopende ronde, of None."""
        ronde = self.actieve_ronde()
        return ronde.sleutel[positie - 1] if ronde else None

    def probeer_kluis(self, invoer):
        invoer = "".join(ch for ch in str(invoer) if ch.isdigit())
        with self.lock:
            ronde = self.ronde
            if not ronde:
                return {"open": False, "melding": "Er loopt geen missie."}
            if ronde.opgelost:
                return {"open": True, "melding": "Deze kluis is al gekraakt.", "tijd": round(ronde.duur(), 1)}
            ronde.pogingen += 1
            if invoer != "".join(map(str, ronde.code)):
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
                "cijfertekst": ronde.cijfertekst,
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
        data["scores"] = self.scores
        return data
