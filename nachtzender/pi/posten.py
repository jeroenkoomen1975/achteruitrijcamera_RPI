"""Maakt per ronde het materiaal voor de posten Linux en Databases.

Linux:     een map met logbestanden; sleutelgroep 3 zit in een verborgen bestand.
Databases: een SQLite-database met onderschepte berichten; sleutelgroep 4 is
           het bericht van zender NACHTZENDER aan agent UIL.
"""

import shutil
import sqlite3
import time
from pathlib import Path

from spel import AGENT, ZENDER

ZENDERS = [ZENDER, "KOEKOEK", "LINCOLNSHIRE", "SPECHT", "NEVEL", "POOLSTER"]
AGENTEN = [AGENT, "VOS", "DAS", "EKSTER", "MOL", "REIGER", "OTTER", "HAAS"]

LEESMIJ = """\
=== MISSIE: LINUX ==========================================================

Je bent ingelogd op de computer van de Nachtzender. Ergens in het archief
staat SLEUTELGROEP 3 (vijf cijfers). Vind hem!

Handige commando's:

  ls                 laat zien welke bestanden er in een map staan
  ls -a              ... ook de verborgen bestanden (die beginnen met een .)
  cd archief         ga de map 'archief' in   (cd ..  = een map terug)
  cat bestand.log    toon de inhoud van een bestand
  grep WOORD *.log   zoek WOORD in alle .log-bestanden
  grep -r WOORD .    zoek WOORD in alle bestanden, ook in submappen

Tip: niet elke sleutelgroep die je vindt is nog geldig...
"""


def _tijdstempel(rng):
    return time.strftime("%Y-%m-%d") + " %02d:%02d:%02d" % (
        rng.randrange(24), rng.randrange(60), rng.randrange(60))


def _groep(rng):
    return "".join(str(rng.randrange(10)) for _ in range(5))


def _logregel(rng):
    return "%s ZENDER=%s FREQ=%d GROEP=%s STATUS=%s" % (
        _tijdstempel(rng), rng.choice(ZENDERS), rng.randrange(3000, 9000),
        _groep(rng), rng.choice(["OK", "OK", "OK", "RUIS", "ZWAK"]))


def _logbestand(pad, rng, regels=150, extra=None):
    lijnen = [_logregel(rng) for _ in range(regels)]
    if extra:
        lijnen.insert(rng.randrange(len(lijnen)), extra)
    pad.parent.mkdir(parents=True, exist_ok=True)
    pad.write_text("\n".join(lijnen) + "\n", encoding="utf-8")


def maak_linux_post(map_, sleutelgroep, rng):
    map_ = Path(map_)
    if map_.exists():
        shutil.rmtree(map_)
    archief = map_ / "archief"
    archief.mkdir(parents=True)
    (map_ / "LEESMIJ.txt").write_text(LEESMIJ, encoding="utf-8")

    for i in range(1, 31):
        _logbestand(archief / ("zender_%02d.log" % i), rng)

    # Lokkertjes: oude sleuteldelen die verlopen zijn.
    for i in rng.sample(range(1, 31), 2):
        _logbestand(archief / ("zender_%02d.log" % i), rng,
                    extra="%s SLEUTELGROEP_3=%s STATUS=VERLOPEN" % (_tijdstempel(rng), _groep(rng)))

    _logbestand(archief / "nacht" / ".geheim.log", rng, regels=40,
                extra="%s SLEUTELGROEP_3=%s STATUS=GELDIG" % (_tijdstempel(rng), sleutelgroep))


def maak_database_post(pad, sleutelgroep, rng):
    pad = Path(pad)
    pad.parent.mkdir(parents=True, exist_ok=True)
    tijdelijk = pad.with_suffix(".tmp")
    tijdelijk.unlink(missing_ok=True)

    rijen = []
    for _ in range(300):
        rijen.append((_tijdstempel(rng), rng.choice(ZENDERS[1:]), rng.choice(AGENTEN),
                      rng.randrange(3000, 9000), " ".join(_groep(rng) for _ in range(3))))
    # Lokkertjes: wel agent UIL, maar een andere zender (of andersom).
    for zender in rng.sample(ZENDERS[1:], 3):
        rijen.append((_tijdstempel(rng), zender, AGENT, rng.randrange(3000, 9000),
                      "SLEUTELGROEP 4 = %s" % _groep(rng)))
    for agent in rng.sample(AGENTEN[1:], 3):
        rijen.append((_tijdstempel(rng), ZENDER, agent, rng.randrange(3000, 9000),
                      "SLEUTELGROEP 4 = %s" % _groep(rng)))
    rijen.append((_tijdstempel(rng), ZENDER, AGENT, 6250, "SLEUTELGROEP 4 = %s" % sleutelgroep))
    rng.shuffle(rijen)
    rijen.sort(key=lambda r: r[0])

    db = sqlite3.connect(tijdelijk)
    with db:
        db.execute("""CREATE TABLE uitzendingen (
            id INTEGER PRIMARY KEY,
            tijd TEXT,
            zender TEXT,
            agent TEXT,
            frequentie_khz INTEGER,
            bericht TEXT)""")
        db.executemany("INSERT INTO uitzendingen (tijd, zender, agent, frequentie_khz, bericht) "
                       "VALUES (?, ?, ?, ?, ?)", rijen)
    db.close()
    tijdelijk.replace(pad)


def maak_posten(map_, ronde, rng):
    map_ = Path(map_)
    maak_linux_post(map_ / "linux", ronde.sleutel[2], rng)
    maak_database_post(map_ / "database" / "onderschept.db", ronde.sleutel[3], rng)
