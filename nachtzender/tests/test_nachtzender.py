import json
import random
import sqlite3
import sys
import tempfile
import threading
import unittest
import urllib.request
from http.server import ThreadingHTTPServer
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parents[1] / "pi"))

from nachtzender import MORSE, maak_handler, voer_sql_uit  # noqa: E402
from omroeper import maak_melodie, uitzendtekst  # noqa: E402
from posten import maak_posten  # noqa: E402
from spel import Spel, ontcijfer, versleutel  # noqa: E402


class TestVersleuteling(unittest.TestCase):
    def test_heen_en_terug(self):
        rng = random.Random(1)
        for _ in range(200):
            code = [rng.randrange(10) for _ in range(4)]
            sleutel = [rng.randrange(10) for _ in range(4)]
            self.assertEqual(ontcijfer(versleutel(code, sleutel), sleutel), code)

    def test_voorbeeld_uit_opdracht(self):
        self.assertEqual(versleutel([3], [9]), [2])
        self.assertEqual(ontcijfer([2], [9]), [3])


class TestSpel(unittest.TestCase):
    def setUp(self):
        self.map = Path(tempfile.mkdtemp())
        self.spel = Spel(scores_pad=self.map / "scores.json", rng=random.Random(7))

    def test_kluis_zonder_ronde(self):
        self.assertFalse(self.spel.probeer_kluis("1234")["open"])

    def test_foute_en_goede_code(self):
        ronde = self.spel.nieuwe_ronde("De Uilen")
        goed = "".join(map(str, ronde.code))
        fout = "".join(str((c + 1) % 10) for c in ronde.code)
        self.assertFalse(self.spel.probeer_kluis(fout)["open"])
        self.assertIsNotNone(self.spel.actieve_ronde())
        self.assertTrue(self.spel.probeer_kluis(goed)["open"])
        self.assertIsNone(self.spel.actieve_ronde())
        self.assertEqual(self.spel.scores[0]["team"], "De Uilen")
        self.assertEqual(self.spel.scores[0]["pogingen"], 2)
        # Scores overleven een herstart.
        self.assertEqual(Spel(scores_pad=self.map / "scores.json").scores[0]["team"], "De Uilen")

    def test_status_verraadt_geen_geheimen(self):
        self.spel.nieuwe_ronde("x")
        publiek = json.dumps(self.spel.status())
        self.assertNotIn("sleutel", publiek)
        self.assertNotIn('"code"', publiek)
        self.assertIn("sleutel", self.spel.beheer_status()["ronde"])

    def test_scores_op_tijd(self):
        for team in ("A", "B"):
            r = self.spel.nieuwe_ronde(team)
            self.spel.probeer_kluis("".join(map(str, r.code)))
        tijden = [s["tijd"] for s in self.spel.scores]
        self.assertEqual(tijden, sorted(tijden))


class TestPosten(unittest.TestCase):
    def setUp(self):
        self.map = Path(tempfile.mkdtemp())
        self.spel = Spel(rng=random.Random(3))
        self.ronde = self.spel.nieuwe_ronde("t")
        maak_posten(self.map, self.ronde, random.Random(3))

    def test_linux_precies_een_geldig_sleuteldeel(self):
        geldig = []
        for pad in (self.map / "linux").rglob("*"):
            if pad.is_file():
                geldig += [r for r in pad.read_text().splitlines() if "SLEUTELDEEL_3" in r and "GELDIG" in r]
        self.assertEqual(len(geldig), 1)
        self.assertIn("SLEUTELDEEL_3=%d " % self.ronde.sleutel[2], geldig[0])

    def test_database_query_vindt_sleuteldeel(self):
        _, rijen = voer_sql_uit(self.map / "database" / "onderschept.db",
                                "SELECT bericht FROM uitzendingen WHERE agent='UIL' AND zender='NACHTZENDER'")
        self.assertEqual(rijen, [("SLEUTELDEEL 4 = %d" % self.ronde.sleutel[3],)])

    def test_database_alleen_lezen(self):
        db = self.map / "database" / "onderschept.db"
        with self.assertRaises(ValueError):
            voer_sql_uit(db, "DELETE FROM uitzendingen")
        with self.assertRaises(sqlite3.Error):
            voer_sql_uit(db, "WITH x AS (SELECT 1) DELETE FROM uitzendingen")

    def test_nieuwe_ronde_vervangt_materiaal(self):
        ronde2 = self.spel.nieuwe_ronde("t2")
        maak_posten(self.map, ronde2, random.Random(4))
        _, rijen = voer_sql_uit(self.map / "database" / "onderschept.db",
                                "SELECT bericht FROM uitzendingen WHERE agent='UIL' AND zender='NACHTZENDER'")
        self.assertEqual(rijen, [("SLEUTELDEEL 4 = %d" % ronde2.sleutel[3],)])


class TestOmroeper(unittest.TestCase):
    def test_tekst_en_melodie(self):
        ronde = Spel(rng=random.Random(5)).nieuwe_ronde("t")
        tekst = uitzendtekst(ronde)
        self.assertIn("Nachtzender", tekst)
        self.assertEqual(tekst.count("Ik herhaal"), 1)
        pad = maak_melodie(Path(tempfile.mkdtemp()) / "m.wav")
        self.assertGreater(pad.stat().st_size, 1000)


class TestServer(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.map = Path(tempfile.mkdtemp())
        rng = random.Random(9)
        cls.spel = Spel(rng=rng, bij_nieuwe_ronde=lambda r: maak_posten(cls.map, r, rng))
        cls.server = ThreadingHTTPServer(("127.0.0.1", 0), maak_handler(cls.spel, cls.map))
        threading.Thread(target=cls.server.serve_forever, daemon=True).start()
        cls.url = "http://127.0.0.1:%d" % cls.server.server_port

    @classmethod
    def tearDownClass(cls):
        cls.server.shutdown()

    def get(self, pad):
        with urllib.request.urlopen(self.url + pad) as r:
            return r.headers, r.read()

    def post(self, pad, data):
        req = urllib.request.Request(self.url + pad, json.dumps(data).encode(),
                                     {"Content-Type": "application/json"})
        with urllib.request.urlopen(req) as r:
            return json.loads(r.read())

    def test_volledige_missie(self):
        self.post("/api/ronde", {"team": "Testteam"})
        ronde = self.spel.ronde

        headers, body = self.get("/api/radio")
        radio = json.loads(body)
        self.assertEqual(radio["groepen"], ["".join(map(str, ronde.cijfertekst))])
        sleutel = [int(headers["X-Sleuteldeel-1"])]

        morse = json.loads(self.get("/api/morse")[1])
        sleutel.append(int({v: k for k, v in MORSE.items()}[morse["morse"]]))

        linux = [r for p in (self.map / "linux").rglob("*.log") for r in p.read_text().splitlines()
                 if "SLEUTELDEEL_3" in r and "GELDIG" in r][0]
        sleutel.append(int(linux.split("SLEUTELDEEL_3=")[1][0]))

        sql = self.post("/api/sql", {"query": "SELECT bericht FROM uitzendingen "
                                              "WHERE agent='UIL' AND zender='NACHTZENDER'"})
        sleutel.append(int(sql["rijen"][0][0][-1]))

        self.assertEqual(sleutel, ronde.sleutel)
        code = "".join(map(str, ontcijfer(ronde.cijfertekst, sleutel)))
        self.assertTrue(self.post("/api/kluis", {"code": code})["open"])
        self.assertEqual(json.loads(self.get("/api/status")[1])["scores"][0]["team"], "Testteam")

    def test_paginas(self):
        for pad in ("/", "/beheer", "/radio", "/morse", "/sql", "/kluis", "/stijl.css"):
            headers, body = self.get(pad)
            self.assertGreater(len(body), 100, pad)


if __name__ == "__main__":
    unittest.main()
