"""Vragen inlezen uit vragen.json en een gevarieerde quiz samenstellen."""

import json
import random
from dataclasses import dataclass
from pathlib import Path

STANDAARD_BESTAND = Path(__file__).with_name("vragen.json")
AANTAL_ANTWOORDEN = 4


@dataclass(frozen=True)
class Vraag:
    categorie: str
    categorie_naam: str
    icoon: str
    tekst: str
    antwoorden: tuple[str, ...]
    goed: int          # index van het goede antwoord in `antwoorden`
    uitleg: str = ""


def laad_vragen(pad=STANDAARD_BESTAND) -> list[Vraag]:
    """Lees en controleer de vragenbank. Geeft een ValueError bij fouten."""
    data = json.loads(Path(pad).read_text(encoding="utf-8"))
    categorieen = data["categorieen"]
    vragen = []
    for nr, v in enumerate(data["vragen"], start=1):
        waar = f"vraag {nr} ({v.get('vraag', '?')!r})"
        if v["categorie"] not in categorieen:
            raise ValueError(f"{waar}: onbekende categorie {v['categorie']!r}")
        if len(v["antwoorden"]) != AANTAL_ANTWOORDEN:
            raise ValueError(f"{waar}: moet precies {AANTAL_ANTWOORDEN} antwoorden hebben")
        if not 0 <= v["goed"] < AANTAL_ANTWOORDEN:
            raise ValueError(f"{waar}: 'goed' moet 0 t/m {AANTAL_ANTWOORDEN - 1} zijn")
        if len(set(v["antwoorden"])) != AANTAL_ANTWOORDEN:
            raise ValueError(f"{waar}: twee antwoorden zijn hetzelfde")
        cat = categorieen[v["categorie"]]
        vragen.append(Vraag(
            categorie=v["categorie"],
            categorie_naam=cat["naam"],
            icoon=cat.get("icoon", ""),
            tekst=v["vraag"],
            antwoorden=tuple(v["antwoorden"]),
            goed=v["goed"],
            uitleg=v.get("uitleg", ""),
        ))
    if not vragen:
        raise ValueError("de vragenbank is leeg")
    return vragen


def schud_antwoorden(vraag: Vraag, rng: random.Random) -> Vraag:
    """Zet de antwoorden in willekeurige volgorde, zodat 'A' niet altijd goed is."""
    volgorde = list(range(len(vraag.antwoorden)))
    rng.shuffle(volgorde)
    return Vraag(
        categorie=vraag.categorie,
        categorie_naam=vraag.categorie_naam,
        icoon=vraag.icoon,
        tekst=vraag.tekst,
        antwoorden=tuple(vraag.antwoorden[i] for i in volgorde),
        goed=volgorde.index(vraag.goed),
        uitleg=vraag.uitleg,
    )


def kies_vragen(bank: list[Vraag], aantal: int, rng: random.Random) -> list[Vraag]:
    """Kies `aantal` vragen, om de beurt uit elke categorie.

    Zo komt bij 10 vragen en 8 categorieën elke categorie minstens één keer
    voor, en staan twee vragen uit dezelfde categorie niet naast elkaar.
    """
    per_categorie: dict[str, list[Vraag]] = {}
    for vraag in bank:
        per_categorie.setdefault(vraag.categorie, []).append(vraag)
    for lijst in per_categorie.values():
        rng.shuffle(lijst)
    categorieen = list(per_categorie)
    rng.shuffle(categorieen)

    gekozen = []
    while len(gekozen) < aantal and any(per_categorie.values()):
        for cat in categorieen:
            if per_categorie[cat] and len(gekozen) < aantal:
                gekozen.append(per_categorie[cat].pop())
    return [schud_antwoorden(v, rng) for v in gekozen]
