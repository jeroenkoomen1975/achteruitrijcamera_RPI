import unittest

from parkeersensor.logica import Zone, bepaal_zone, display_regels, mediaan, piep_pauze

ORANJE, ROOD, PIEP_START = 20, 10, 50


class TestZone(unittest.TestCase):
    def test_niets_gemeten_is_groen(self):
        self.assertIs(bepaal_zone(None, ORANJE, ROOD), Zone.GROEN)

    def test_grenzen(self):
        self.assertIs(bepaal_zone(100, ORANJE, ROOD), Zone.GROEN)
        self.assertIs(bepaal_zone(20.1, ORANJE, ROOD), Zone.GROEN)
        self.assertIs(bepaal_zone(20, ORANJE, ROOD), Zone.ORANJE)
        self.assertIs(bepaal_zone(10.1, ORANJE, ROOD), Zone.ORANJE)
        self.assertIs(bepaal_zone(10, ORANJE, ROOD), Zone.ROOD)
        self.assertIs(bepaal_zone(2, ORANJE, ROOD), Zone.ROOD)


class TestPiep(unittest.TestCase):
    def pauze(self, afstand):
        return piep_pauze(afstand, ORANJE, ROOD, PIEP_START)

    def test_ver_weg_stil(self):
        self.assertIsNone(self.pauze(None))
        self.assertIsNone(self.pauze(80))

    def test_rood_continu(self):
        self.assertEqual(self.pauze(5), 0)

    def test_sneller_als_dichterbij(self):
        afstanden = [50, 40, 30, 21, 20, 15, 11]
        pauzes = [self.pauze(a) for a in afstanden]
        self.assertEqual(pauzes, sorted(pauzes, reverse=True))
        self.assertTrue(all(p > 0 for p in pauzes))

    def test_groen_stil_als_piep_start_uit(self):
        self.assertIsNone(piep_pauze(30, ORANJE, ROOD, 0))


class TestHulpfuncties(unittest.TestCase):
    def test_mediaan(self):
        self.assertEqual(mediaan([3, 100, 5]), 5)
        self.assertEqual(mediaan([4, None, 6]), 5)
        self.assertIsNone(mediaan([None, None]))

    def test_display(self):
        r1, r2 = display_regels(15.34, Zone.ORANJE, 200)
        self.assertEqual(r1, "Afstand: 15.3 cm")
        self.assertEqual(r2.strip(), "LET OP")
        self.assertEqual(len(r1), 16)
        self.assertEqual(len(r2), 16)

    def test_display_buiten_bereik(self):
        r1, r2 = display_regels(None, Zone.GROEN, 200)
        self.assertEqual(r1.strip(), "Afstand: >200cm")
        self.assertEqual(r2.strip(), "VRIJ")


if __name__ == "__main__":
    unittest.main()
