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
from omroeper import STIJLEN, maak_melodie, ssml, uitzend_zinnen  # noqa: E402
from posten import maak_posten  # noqa: E402
from spel import Spel, naar_codeboek, ontcijfer, uit_codeboek, versleutel  # noqa: E402
from terminal import voer_uit  # noqa: E402

GELDIG_SQL = "SELECT bericht FROM uitzendingen WHERE agent='UIL' AND zender='NACHTZENDER'"


def geldige_linux_regels(map_):
    return [r for p in Path(map_).rglob("*") if p.is_file()
            for r in p.read_text().splitlines() if "SLEUTELGROEP_3" in r and "GELDIG" in r]


class TestVersleuteling(unittest.TestCase):
    def test_heen_en_terug(self):
        rng = random.Random(1)
        for _ in range(200):
            klaar = "".join(str(rng.randrange(10)) for _ in range(20))
            sleutel = "".join(str(rng.randrange(10)) for _ in range(20))
            self.assertEqual(ontcijfer(versleutel(klaar, sleutel), sleutel), klaar)

    def test_voorbeeld_uit_opdracht(self):
        self.assertEqual(versleutel("3", "9"), "2")
        self.assertEqual(ontcijfer("2", "9"), "3")

    def test_codeboek(self):
        self.assertEqual(naar_codeboek("KLUIS 4271"), "11122109190034323731")
        self.assertEqual(uit_codeboek("11122109190034323731"), "KLUIS 4271")
        self.assertEqual(uit_codeboek("9927"), "??")


class TestSpel(unittest.TestCase):
    def setUp(self):
        self.map = Path(tempfile.mkdtemp())
        self.spel = Spel(scores_pad=self.map / "scores.json", rng=random.Random(7))

    def test_ronde_vorm(self):
        r = self.spel.nieuwe_ronde("x", "de")
        self.assertEqual(r.stijl, "de")
        self.assertEqual(len(r.cijfertekst), 4)
        self.assertTrue(all(len(g) == 5 for g in r.cijfertekst + r.sleutel))
        self.assertEqual(uit_codeboek(ontcijfer("".join(r.cijfertekst), "".join(r.sleutel))), "KLUIS " + r.code)
        self.assertEqual(self.spel.nieuwe_ronde("y", "onzin").stijl, "nl")

    def test_kluis_zonder_ronde(self):
        self.assertFalse(self.spel.probeer_kluis("1234")["open"])

    def test_foute_en_goede_code(self):
        ronde = self.spel.nieuwe_ronde("De Uilen")
        fout = "".join(str((int(c) + 1) % 10) for c in ronde.code)
        self.assertFalse(self.spel.probeer_kluis(fout)["open"])
        self.assertIsNotNone(self.spel.actieve_ronde())
        self.assertTrue(self.spel.probeer_kluis(ronde.code)["open"])
        self.assertIsNone(self.spel.actieve_ronde())
        self.assertEqual(self.spel.scores[0]["team"], "De Uilen")
        self.assertEqual(self.spel.scores[0]["pogingen"], 2)
        self.assertEqual(Spel(scores_pad=self.map / "scores.json").scores[0]["team"], "De Uilen")

    def test_controleer(self):
        r = self.spel.nieuwe_ronde("x")
        self.assertFalse(self.spel.controleer(1, "00000" if r.sleutel[0] != "00000" else "11111"))
        self.assertTrue(self.spel.controleer(2, r.sleutel[1]))
        self.assertFalse(self.spel.controleer(5, r.sleutel[0]))
        self.assertEqual(self.spel.status()["ronde"]["gevonden"], [2])

    def test_status_verraadt_geen_geheimen(self):
        r = self.spel.nieuwe_ronde("x")
        publiek = json.dumps(self.spel.status())
        self.assertNotIn("sleutel", publiek)
        self.assertNotIn('"code"', publiek)
        self.assertNotIn(r.sleutel[0], publiek.replace(json.dumps(r.cijfertekst), ""))
        self.assertIn("sleutel", self.spel.beheer_status()["ronde"])


class TestPosten(unittest.TestCase):
    def setUp(self):
        self.map = Path(tempfile.mkdtemp())
        self.spel = Spel(rng=random.Random(3))
        self.ronde = self.spel.nieuwe_ronde("t")
        maak_posten(self.map, self.ronde, random.Random(3))

    def test_linux_precies_een_geldige_sleutelgroep(self):
        geldig = geldige_linux_regels(self.map / "linux")
        self.assertEqual(len(geldig), 1)
        self.assertIn("SLEUTELGROEP_3=%s " % self.ronde.sleutel[2], geldig[0])

    def test_database_query_vindt_sleutelgroep(self):
        _, rijen = voer_sql_uit(self.map / "database" / "onderschept.db", GELDIG_SQL)
        self.assertEqual(rijen, [("SLEUTELGROEP 4 = %s" % self.ronde.sleutel[3],)])

    def test_database_alleen_lezen(self):
        db = self.map / "database" / "onderschept.db"
        with self.assertRaises(ValueError):
            voer_sql_uit(db, "DELETE FROM uitzendingen")
        with self.assertRaises(sqlite3.Error):
            voer_sql_uit(db, "WITH x AS (SELECT 1) DELETE FROM uitzendingen")

    def test_nieuwe_ronde_vervangt_materiaal(self):
        ronde2 = self.spel.nieuwe_ronde("t2")
        maak_posten(self.map, ronde2, random.Random(4))
        _, rijen = voer_sql_uit(self.map / "database" / "onderschept.db", GELDIG_SQL)
        self.assertEqual(rijen, [("SLEUTELGROEP 4 = %s" % ronde2.sleutel[3],)])


class TestTerminal(unittest.TestCase):
    def setUp(self):
        self.map = Path(tempfile.mkdtemp())
        self.ronde = Spel(rng=random.Random(5)).nieuwe_ronde("t")
        maak_posten(self.map, self.ronde, random.Random(5))
        self.root = self.map / "linux"

    def test_ls_en_verborgen(self):
        uit, _ = voer_uit(self.root, "~", "ls")
        self.assertIn("LEESMIJ.txt", uit)
        uit, cwd = voer_uit(self.root, "~", "cd archief/nacht")
        self.assertEqual(cwd, "~/archief/nacht")
        self.assertEqual(voer_uit(self.root, cwd, "ls")[0], "")
        self.assertIn(".geheim.log", voer_uit(self.root, cwd, "ls -a")[0])

    def test_grep_recursief_vindt_geldige(self):
        uit, _ = voer_uit(self.root, "~", "grep -r SLEUTELGROEP .")
        self.assertIn("SLEUTELGROEP_3=%s STATUS=GELDIG" % self.ronde.sleutel[2], uit)
        self.assertIn("archief/nacht/.geheim.log:", uit)
        self.assertIn("VERLOPEN", uit)
        uit, _ = voer_uit(self.root, "~/archief", "grep SLEUTELGROEP *.log")
        self.assertNotIn("GELDIG", uit)   # die zit in de verborgen submap

    def test_niet_ontsnappen(self):
        for cmd in ("cd ..", "cat ../../../etc/passwd", "ls /", "cat /etc/passwd", "cd ~/../.."):
            uit, cwd = voer_uit(self.root, "~", cmd)
            self.assertEqual(cwd, "~", cmd)
            self.assertNotIn("root:", uit)
        self.assertIn("Toegang geweigerd", voer_uit(self.root, "~", "cat /etc/passwd")[0])
        self.assertEqual(voer_uit(self.root, "~/../..", "pwd")[0], "/home/agent")

    def test_overige(self):
        self.assertIn("ls", voer_uit(self.root, "~", "help")[0])
        self.assertIn("niet gevonden", voer_uit(self.root, "~", "rmdir x")[0])
        self.assertIn("Eén commando", voer_uit(self.root, "~", "ls | wc")[0])
        self.assertEqual(len(voer_uit(self.root, "~", "cat archief/zender_01.log")[0].splitlines()), 150)


class TestOmroeper(unittest.TestCase):
    def test_zinnen_alle_stijlen(self):
        for stijl in STIJLEN:
            ronde = Spel(rng=random.Random(5)).nieuwe_ronde("t", stijl)
            zinnen = uitzend_zinnen(ronde)
            cijferwoorden = set(STIJLEN[stijl]["cijfers"])
            # 4 groepen x 5 cijfers, twee keer + één herhaling = 60 cijfers
            self.assertEqual(sum(z in cijferwoorden for z in zinnen), 60, stijl)
            self.assertTrue(ssml(zinnen).startswith("<speak>"))

    def test_melodie(self):
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
        self.post("/api/ronde", {"team": "Testteam", "stijl": "fa"})
        ronde = self.spel.ronde
        status = json.loads(self.get("/api/status")[1])
        self.assertEqual(status["taal"], "fa-IR")
        self.assertEqual(status["ronde"]["cijfertekst"], ronde.cijfertekst)

        headers, body = self.get("/api/radio")
        self.assertEqual(json.loads(body)["groepen"], ronde.cijfertekst)
        sleutel = [headers["X-Sleutelgroep-1"]]

        morse = json.loads(self.get("/api/morse")[1])["morse"]
        terug = {v: k for k, v in MORSE.items()}
        sleutel.append("".join(terug[m] for m in morse.split(" ")))

        uit = self.post("/api/terminal", {"cwd": "~", "commando": "grep -r GELDIG ."})["uitvoer"]
        sleutel.append(uit.split("SLEUTELGROEP_3=")[1][:5])

        sql = self.post("/api/sql", {"query": GELDIG_SQL})
        sleutel.append(sql["rijen"][0][0][-5:])

        self.assertEqual(sleutel, ronde.sleutel)
        for post, groep in enumerate(sleutel, 1):
            self.assertTrue(self.post("/api/controleer", {"post": post, "waarde": groep})["goed"])
        self.assertEqual(json.loads(self.get("/api/status")[1])["ronde"]["gevonden"], [1, 2, 3, 4])

        tekst = uit_codeboek(ontcijfer("".join(ronde.cijfertekst), "".join(sleutel)))
        self.assertTrue(self.post("/api/kluis", {"code": tekst.split()[1]})["open"])
        self.assertEqual(json.loads(self.get("/api/status")[1])["scores"][0]["team"], "Testteam")

    def test_paginas(self):
        for pad in ("/", "/missie", "/beheer", "/netwerken", "/embedded", "/linux", "/database",
                    "/ontcijferen", "/kluis", "/radio", "/stijl.css", "/gemeen.js"):
            headers, body = self.get(pad)
            self.assertGreater(len(body), 100, pad)


if __name__ == "__main__":
    unittest.main()
