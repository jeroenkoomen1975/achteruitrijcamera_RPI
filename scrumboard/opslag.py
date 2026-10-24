"""Opslag van groepen, sprints en kaartjes in een SQLite-database.

Elke groep heeft een eigen pincode. Wie de pincode kent, krijgt een token
waarmee het bord van die groep gelezen en aangepast kan worden.
"""

import hashlib
import hmac
import secrets
import sqlite3
import threading
import time

KOLOMMEN = ("backlog", "todo", "bezig", "review", "klaar")
PRIORITEITEN = ("laag", "normaal", "hoog")

# Na zoveel foute pincodes moet je even wachten (tegen gokken).
MAX_POGINGEN = 5
WACHTTIJD = 60

MAX_TEKST = {"naam": 60, "titel": 120, "omschrijving": 2000, "doel": 300}

SCHEMA = """
CREATE TABLE IF NOT EXISTS instelling (
    sleutel TEXT PRIMARY KEY,
    waarde  TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS groep (
    id        INTEGER PRIMARY KEY,
    naam      TEXT NOT NULL UNIQUE COLLATE NOCASE,
    zout      TEXT NOT NULL,
    pin_hash  TEXT NOT NULL
);
CREATE TABLE IF NOT EXISTS lid (
    id        INTEGER PRIMARY KEY,
    groep_id  INTEGER NOT NULL REFERENCES groep(id) ON DELETE CASCADE,
    naam      TEXT NOT NULL,
    UNIQUE (groep_id, naam)
);
CREATE TABLE IF NOT EXISTS sprint (
    groep_id  INTEGER PRIMARY KEY REFERENCES groep(id) ON DELETE CASCADE,
    naam      TEXT NOT NULL DEFAULT 'Sprint 1',
    doel      TEXT NOT NULL DEFAULT '',
    start     TEXT NOT NULL DEFAULT '',
    eind      TEXT NOT NULL DEFAULT ''
);
CREATE TABLE IF NOT EXISTS kaart (
    id           INTEGER PRIMARY KEY,
    groep_id     INTEGER NOT NULL REFERENCES groep(id) ON DELETE CASCADE,
    titel        TEXT NOT NULL,
    omschrijving TEXT NOT NULL DEFAULT '',
    kolom        TEXT NOT NULL DEFAULT 'backlog',
    positie      REAL NOT NULL DEFAULT 0,
    punten       INTEGER,
    prioriteit   TEXT NOT NULL DEFAULT 'normaal',
    eigenaar     TEXT NOT NULL DEFAULT ''
);
"""


class OngeldigeInvoer(ValueError):
    """De gebruiker stuurde iets wat niet klopt."""


class GeenToegang(PermissionError):
    """Verkeerde pincode of token."""


class NietGevonden(LookupError):
    """Groep of kaartje bestaat niet."""


def _tekst(waarde, veld, verplicht=False):
    if waarde is None:
        waarde = ""
    if not isinstance(waarde, str):
        raise OngeldigeInvoer(f"{veld} moet tekst zijn")
    waarde = waarde.strip()
    if verplicht and not waarde:
        raise OngeldigeInvoer(f"{veld} is verplicht")
    if len(waarde) > MAX_TEKST.get(veld, 60):
        raise OngeldigeInvoer(f"{veld} is te lang")
    return waarde


def _pin(pin):
    if not isinstance(pin, str) or not pin.isdigit() or not 4 <= len(pin) <= 8:
        raise OngeldigeInvoer("pincode moet 4 tot 8 cijfers zijn")
    return pin


def _hash_pin(pin, zout):
    return hashlib.pbkdf2_hmac("sha256", pin.encode(), bytes.fromhex(zout), 100_000).hex()


class Opslag:
    def __init__(self, pad=":memory:"):
        self._db = sqlite3.connect(pad, check_same_thread=False)
        self._db.row_factory = sqlite3.Row
        self._db.execute("PRAGMA foreign_keys = ON")
        self._db.executescript(SCHEMA)
        self._slot = threading.RLock()
        self._fout = {}
        rij = self._db.execute("SELECT waarde FROM instelling WHERE sleutel = 'geheim'").fetchone()
        if rij is None:
            self._geheim = secrets.token_hex(32)
            self._db.execute("INSERT INTO instelling VALUES ('geheim', ?)", (self._geheim,))
            self._db.commit()
        else:
            self._geheim = rij["waarde"]

    def sluit(self):
        self._db.close()

    def _lees(self, sql, parameters=()):
        with self._slot:
            return self._db.execute(sql, parameters).fetchall()

    # --- groepen en toegang -------------------------------------------------

    def groepen(self):
        rijen = self._lees("SELECT id, naam FROM groep ORDER BY naam COLLATE NOCASE")
        return [dict(r) for r in rijen]

    def maak_groep(self, naam, pin):
        naam = _tekst(naam, "naam", verplicht=True)
        pin = _pin(pin)
        zout = secrets.token_hex(16)
        with self._slot:
            try:
                cur = self._db.execute(
                    "INSERT INTO groep (naam, zout, pin_hash) VALUES (?, ?, ?)",
                    (naam, zout, _hash_pin(pin, zout)),
                )
            except sqlite3.IntegrityError:
                raise OngeldigeInvoer("er bestaat al een groep met deze naam") from None
            groep_id = cur.lastrowid
            self._db.execute("INSERT INTO sprint (groep_id) VALUES (?)", (groep_id,))
            self._db.commit()
        return {"id": groep_id, "naam": naam, "token": self._token(groep_id)}

    def log_in(self, groep_id, pin, nu=None):
        rij = self._groep(groep_id)
        nu = time.monotonic() if nu is None else nu
        pogingen = [t for t in self._fout.get(groep_id, []) if nu - t < WACHTTIJD]
        if len(pogingen) >= MAX_POGINGEN:
            raise GeenToegang("te veel foute pogingen, wacht een minuut")
        goed = isinstance(pin, str) and pin.isdigit() and hmac.compare_digest(
            _hash_pin(pin, rij["zout"]), rij["pin_hash"]
        )
        if not goed:
            self._fout[groep_id] = pogingen + [nu]
            raise GeenToegang("verkeerde pincode")
        self._fout.pop(groep_id, None)
        return {"id": rij["id"], "naam": rij["naam"], "token": self._token(groep_id)}

    def controleer(self, groep_id, token):
        self._groep(groep_id)
        if not token or not hmac.compare_digest(token, self._token(groep_id)):
            raise GeenToegang("log eerst in bij deze groep")

    def _token(self, groep_id):
        rij = self._groep(groep_id)
        bericht = f"{groep_id}:{rij['pin_hash']}".encode()
        return hmac.new(self._geheim.encode(), bericht, hashlib.sha256).hexdigest()

    def _groep(self, groep_id):
        rijen = self._lees("SELECT * FROM groep WHERE id = ?", (groep_id,))
        if not rijen:
            raise NietGevonden("groep bestaat niet")
        return rijen[0]

    # --- bord ---------------------------------------------------------------

    def bord(self, groep_id):
        groep = self._groep(groep_id)
        sprint = self._lees(
            "SELECT naam, doel, start, eind FROM sprint WHERE groep_id = ?", (groep_id,)
        )[0]
        leden = self._lees(
            "SELECT naam FROM lid WHERE groep_id = ? ORDER BY naam COLLATE NOCASE", (groep_id,)
        )
        kaarten = self._lees(
            "SELECT id, titel, omschrijving, kolom, positie, punten, prioriteit, eigenaar "
            "FROM kaart WHERE groep_id = ? ORDER BY positie, id",
            (groep_id,),
        )
        return {
            "groep": {"id": groep["id"], "naam": groep["naam"]},
            "sprint": dict(sprint),
            "leden": [r["naam"] for r in leden],
            "kaarten": [dict(r) for r in kaarten],
        }

    def wijzig_sprint(self, groep_id, gegevens):
        naam = _tekst(gegevens.get("naam"), "naam", verplicht=True)
        doel = _tekst(gegevens.get("doel"), "doel")
        start = _tekst(gegevens.get("start"), "start")
        eind = _tekst(gegevens.get("eind"), "eind")
        with self._slot:
            self._db.execute(
                "UPDATE sprint SET naam = ?, doel = ?, start = ?, eind = ? WHERE groep_id = ?",
                (naam, doel, start, eind, groep_id),
            )
            self._db.commit()

    def voeg_lid_toe(self, groep_id, naam):
        naam = _tekst(naam, "naam", verplicht=True)
        with self._slot:
            self._db.execute(
                "INSERT OR IGNORE INTO lid (groep_id, naam) VALUES (?, ?)", (groep_id, naam)
            )
            self._db.commit()

    def verwijder_lid(self, groep_id, naam):
        with self._slot:
            self._db.execute("DELETE FROM lid WHERE groep_id = ? AND naam = ?", (groep_id, naam))
            self._db.commit()

    # --- kaartjes -----------------------------------------------------------

    def maak_kaart(self, groep_id, gegevens):
        velden = self._kaartvelden(gegevens, nieuw=True)
        with self._slot:
            if "positie" not in velden:
                rij = self._db.execute(
                    "SELECT COALESCE(MAX(positie), 0) + 1 FROM kaart WHERE groep_id = ? AND kolom = ?",
                    (groep_id, velden.get("kolom", "backlog")),
                ).fetchone()
                velden["positie"] = rij[0]
            namen = ", ".join(velden)
            vraagtekens = ", ".join("?" for _ in velden)
            cur = self._db.execute(
                f"INSERT INTO kaart (groep_id, {namen}) VALUES (?, {vraagtekens})",
                (groep_id, *velden.values()),
            )
            self._db.commit()
        return cur.lastrowid

    def wijzig_kaart(self, groep_id, kaart_id, gegevens):
        velden = self._kaartvelden(gegevens, nieuw=False)
        if not velden:
            return
        toewijzing = ", ".join(f"{naam} = ?" for naam in velden)
        with self._slot:
            cur = self._db.execute(
                f"UPDATE kaart SET {toewijzing} WHERE id = ? AND groep_id = ?",
                (*velden.values(), kaart_id, groep_id),
            )
            self._db.commit()
        if cur.rowcount == 0:
            raise NietGevonden("kaartje bestaat niet")

    def verwijder_kaart(self, groep_id, kaart_id):
        with self._slot:
            cur = self._db.execute(
                "DELETE FROM kaart WHERE id = ? AND groep_id = ?", (kaart_id, groep_id)
            )
            self._db.commit()
        if cur.rowcount == 0:
            raise NietGevonden("kaartje bestaat niet")

    @staticmethod
    def _kaartvelden(gegevens, nieuw):
        velden = {}
        if nieuw or "titel" in gegevens:
            velden["titel"] = _tekst(gegevens.get("titel"), "titel", verplicht=True)
        if "omschrijving" in gegevens:
            velden["omschrijving"] = _tekst(gegevens["omschrijving"], "omschrijving")
        if "eigenaar" in gegevens:
            velden["eigenaar"] = _tekst(gegevens["eigenaar"], "naam")
        if "kolom" in gegevens:
            if gegevens["kolom"] not in KOLOMMEN:
                raise OngeldigeInvoer("onbekende kolom")
            velden["kolom"] = gegevens["kolom"]
        if "prioriteit" in gegevens:
            if gegevens["prioriteit"] not in PRIORITEITEN:
                raise OngeldigeInvoer("onbekende prioriteit")
            velden["prioriteit"] = gegevens["prioriteit"]
        if "punten" in gegevens:
            punten = gegevens["punten"]
            if punten in (None, ""):
                punten = None
            elif isinstance(punten, bool) or not isinstance(punten, int) or not 0 <= punten <= 100:
                raise OngeldigeInvoer("storypoints moeten een getal van 0 tot 100 zijn")
            velden["punten"] = punten
        if "positie" in gegevens:
            positie = gegevens["positie"]
            if isinstance(positie, bool) or not isinstance(positie, (int, float)):
                raise OngeldigeInvoer("positie moet een getal zijn")
            velden["positie"] = float(positie)
        return velden
