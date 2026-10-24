import json
import threading
import unittest
import urllib.error
import urllib.request

from scrumboard.opslag import GeenToegang, NietGevonden, OngeldigeInvoer, Opslag
from scrumboard.server import maak_server


class TestOpslag(unittest.TestCase):
    def setUp(self):
        self.opslag = Opslag()
        self.addCleanup(self.opslag.sluit)
        self.groep = self.opslag.maak_groep("Groep 1", "1234")

    def test_groep_maken_en_inloggen(self):
        self.assertEqual(self.opslag.groepen(), [{"id": self.groep["id"], "naam": "Groep 1"}])
        token = self.opslag.log_in(self.groep["id"], "1234")["token"]
        self.assertEqual(token, self.groep["token"])
        self.opslag.controleer(self.groep["id"], token)

    def test_ongeldige_groep(self):
        with self.assertRaises(OngeldigeInvoer):
            self.opslag.maak_groep("groep 1", "1234")  # naam bestaat al
        with self.assertRaises(OngeldigeInvoer):
            self.opslag.maak_groep("Groep 2", "12")
        with self.assertRaises(OngeldigeInvoer):
            self.opslag.maak_groep("  ", "1234")

    def test_verkeerde_pin_en_token(self):
        with self.assertRaises(GeenToegang):
            self.opslag.log_in(self.groep["id"], "0000")
        with self.assertRaises(GeenToegang):
            self.opslag.controleer(self.groep["id"], "nep")
        andere = self.opslag.maak_groep("Groep 2", "5678")
        with self.assertRaises(GeenToegang):
            self.opslag.controleer(andere["id"], self.groep["token"])
        with self.assertRaises(NietGevonden):
            self.opslag.log_in(999, "1234")

    def test_te_veel_pogingen(self):
        gid = self.groep["id"]
        for _ in range(5):
            with self.assertRaises(GeenToegang):
                self.opslag.log_in(gid, "0000", nu=100)
        with self.assertRaisesRegex(GeenToegang, "te veel"):
            self.opslag.log_in(gid, "1234", nu=101)
        self.opslag.log_in(gid, "1234", nu=200)

    def test_kaarten(self):
        gid = self.groep["id"]
        a = self.opslag.maak_kaart(gid, {"titel": "Inlogpagina", "punten": 3})
        b = self.opslag.maak_kaart(gid, {"titel": "Database"})
        self.opslag.wijzig_kaart(gid, b, {"kolom": "bezig", "eigenaar": "Sam", "prioriteit": "hoog"})
        kaarten = {k["id"]: k for k in self.opslag.bord(gid)["kaarten"]}
        self.assertEqual(kaarten[a]["kolom"], "backlog")
        self.assertEqual(kaarten[a]["punten"], 3)
        self.assertEqual(kaarten[b]["kolom"], "bezig")
        self.assertEqual(kaarten[b]["eigenaar"], "Sam")
        self.assertLess(kaarten[a]["positie"], 2)

        self.opslag.verwijder_kaart(gid, a)
        self.assertEqual(len(self.opslag.bord(gid)["kaarten"]), 1)
        with self.assertRaises(NietGevonden):
            self.opslag.verwijder_kaart(gid, a)

    def test_kaart_van_andere_groep_niet_aanpassen(self):
        andere = self.opslag.maak_groep("Groep 2", "5678")
        kaart = self.opslag.maak_kaart(self.groep["id"], {"titel": "Geheim"})
        with self.assertRaises(NietGevonden):
            self.opslag.wijzig_kaart(andere["id"], kaart, {"titel": "Gehackt"})

    def test_ongeldige_kaart(self):
        gid = self.groep["id"]
        for gegevens in ({}, {"titel": ""}, {"titel": "x", "kolom": "ergens"},
                         {"titel": "x", "punten": -1}, {"titel": "x", "punten": "veel"},
                         {"titel": "x", "prioriteit": "super"}, {"titel": "x" * 200}):
            with self.subTest(gegevens=gegevens), self.assertRaises(OngeldigeInvoer):
                self.opslag.maak_kaart(gid, gegevens)

    def test_sprint_en_leden(self):
        gid = self.groep["id"]
        self.opslag.wijzig_sprint(gid, {"naam": "Sprint 2", "doel": "Login af", "start": "2026-11-01"})
        self.opslag.voeg_lid_toe(gid, "Sam")
        self.opslag.voeg_lid_toe(gid, "Sam")
        self.opslag.voeg_lid_toe(gid, "Alex")
        bord = self.opslag.bord(gid)
        self.assertEqual(bord["sprint"]["naam"], "Sprint 2")
        self.assertEqual(bord["sprint"]["doel"], "Login af")
        self.assertEqual(bord["leden"], ["Alex", "Sam"])
        self.opslag.verwijder_lid(gid, "Sam")
        self.assertEqual(self.opslag.bord(gid)["leden"], ["Alex"])


class TestServer(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.opslag = Opslag()
        cls.server = maak_server("127.0.0.1", 0, cls.opslag)
        cls.basis = f"http://127.0.0.1:{cls.server.server_address[1]}"
        threading.Thread(target=cls.server.serve_forever, daemon=True).start()

    @classmethod
    def tearDownClass(cls):
        cls.server.shutdown()
        cls.server.server_close()
        cls.opslag.sluit()

    def verzoek(self, methode, pad, gegevens=None, token=None):
        data = None if gegevens is None else json.dumps(gegevens).encode()
        req = urllib.request.Request(self.basis + pad, data=data, method=methode)
        req.add_header("Content-Type", "application/json")
        if token:
            req.add_header("X-Token", token)
        try:
            with urllib.request.urlopen(req) as antwoord:
                return antwoord.status, json.loads(antwoord.read())
        except urllib.error.HTTPError as fout:
            return fout.code, json.loads(fout.read())

    def test_hele_flow(self):
        status, groep = self.verzoek("POST", "/api/groepen", {"naam": "Team API", "pin": "4321"})
        self.assertEqual(status, 200)
        gid, token = groep["id"], groep["token"]

        status, _ = self.verzoek("GET", f"/api/groepen/{gid}/bord")
        self.assertEqual(status, 403)

        status, inlog = self.verzoek("POST", f"/api/groepen/{gid}/login", {"pin": "4321"})
        self.assertEqual((status, inlog["token"]), (200, token))

        status, kaart = self.verzoek("POST", f"/api/groepen/{gid}/kaarten", {"titel": "Story"}, token)
        self.assertEqual(status, 200)
        status, _ = self.verzoek("PUT", f"/api/groepen/{gid}/kaarten/{kaart['id']}",
                                 {"kolom": "klaar"}, token)
        self.assertEqual(status, 200)
        status, bord = self.verzoek("GET", f"/api/groepen/{gid}/bord", token=token)
        self.assertEqual(bord["kaarten"][0]["kolom"], "klaar")

        status, fout = self.verzoek("PUT", f"/api/groepen/{gid}/kaarten/{kaart['id']}",
                                    {"kolom": "nergens"}, token)
        self.assertEqual(status, 400)
        self.assertIn("kolom", fout["fout"])

    def test_statische_bestanden(self):
        with urllib.request.urlopen(self.basis + "/") as antwoord:
            self.assertIn(b"Scrumboard", antwoord.read())
        with urllib.request.urlopen(self.basis + "/../opslag.py") as antwoord:
            self.assertNotIn(b"import sqlite3", antwoord.read())


if __name__ == "__main__":
    unittest.main()
