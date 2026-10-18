"""Records die bewaard blijven, ook als de Pi opnieuw opstart.

- hoogste score ooit in één spel
- snelste goede antwoord ooit (in seconden)

Ze staan in een klein JSON-bestand (standaard quiz/data/records.json).
Wil je de records wissen? Verwijder dat bestand en herstart de server.
"""

import json
import logging
import os
from datetime import datetime
from pathlib import Path

STANDAARD_BESTAND = Path(__file__).resolve().parent.parent / "data" / "records.json"
log = logging.getLogger("quiz")


class Records:
    def __init__(self, pad: Path | None = STANDAARD_BESTAND):
        """Met pad=None wordt niets opgeslagen (handig voor tests)."""
        self.pad = Path(pad) if pad is not None else None
        self.hoogste_score: dict | None = None     # {"score", "speler", "datum"}
        self.snelste_antwoord: dict | None = None  # {"tijd", "speler", "datum"}
        self._laad()

    def _laad(self):
        if self.pad is None or not self.pad.exists():
            return
        try:
            data = json.loads(self.pad.read_text(encoding="utf-8"))
            self.hoogste_score = data.get("hoogste_score")
            self.snelste_antwoord = data.get("snelste_antwoord")
        except (OSError, ValueError, AttributeError) as e:
            log.warning("Records niet te lezen (%s), begin opnieuw", e)

    def _bewaar(self):
        if self.pad is None:
            return
        try:
            self.pad.parent.mkdir(parents=True, exist_ok=True)
            tijdelijk = self.pad.with_suffix(".tmp")
            tijdelijk.write_text(json.dumps(self.als_dict(), indent=2), encoding="utf-8")
            os.replace(tijdelijk, self.pad)  # in één keer, zodat het bestand nooit half is
        except OSError as e:
            log.warning("Records niet op te slaan: %s", e)

    @staticmethod
    def _datum() -> str:
        return datetime.now().strftime("%Y-%m-%d %H:%M")

    def nieuwe_score(self, score: int, speler: int) -> bool:
        """Geeft True als dit de hoogste score ooit is."""
        if score <= 0:
            return False
        if self.hoogste_score is not None and score <= self.hoogste_score["score"]:
            return False
        self.hoogste_score = {"score": score, "speler": speler, "datum": self._datum()}
        self._bewaar()
        return True

    def nieuw_antwoord(self, tijd: float, speler: int) -> bool:
        """Een goed antwoord na `tijd` seconden. True als dat het snelste ooit is."""
        tijd = round(tijd, 2)
        if self.snelste_antwoord is not None and tijd >= self.snelste_antwoord["tijd"]:
            return False
        self.snelste_antwoord = {"tijd": tijd, "speler": speler, "datum": self._datum()}
        self._bewaar()
        return True

    def als_dict(self) -> dict:
        return {"hoogste_score": self.hoogste_score, "snelste_antwoord": self.snelste_antwoord}
