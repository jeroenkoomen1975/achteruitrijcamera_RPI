import random

import pytest

from quizserver import config
from quizserver.spel import AFTELLEN, EINDE, LOBBY, UITSLAG, VRAAG, Spel
from quizserver.vragen import laad_vragen


class NepKlok:
    def __init__(self):
        self.nu = 1000.0

    def __call__(self):
        return self.nu

    def verder(self, seconden):
        self.nu += seconden


@pytest.fixture
def klok():
    return NepKlok()


@pytest.fixture
def spel(klok):
    return Spel(laad_vragen(), klok=klok, rng=random.Random(42))


def start_duel(spel, klok):
    spel.druk(1, 0)
    spel.druk(2, 0)
    klok.verder(config.AFTELLEN_S)
    spel.tik()
    assert spel.fase == VRAAG


def test_begint_in_lobby(spel):
    assert spel.fase == LOBBY
    assert spel.resterend() is None


def test_twee_spelers_klaar_start_aftellen(spel):
    assert spel.druk(1, 2)
    assert spel.fase == LOBBY
    assert spel.druk(2, 0)
    assert spel.fase == AFTELLEN
    assert len(spel.vragen) == config.AANTAL_VRAGEN
    assert {s.nummer for s in spel.deelnemers} == {1, 2}


def test_dubbel_drukken_in_lobby_telt_niet(spel):
    assert spel.druk(1, 0)
    assert not spel.druk(1, 1)


def test_solo_na_wachten(spel, klok):
    spel.druk(2, 0)
    klok.verder(config.WACHT_OP_TEGENSTANDER_S)
    spel.tik()
    assert spel.fase == AFTELLEN
    assert [s.nummer for s in spel.deelnemers] == [2]


def test_ongeldige_invoer(spel):
    assert not spel.druk(3, 0)
    assert not spel.druk(1, 4)
    assert not spel.druk(1, -1)


def test_vraag_heeft_tien_verschillende_vragen(spel, klok):
    start_duel(spel, klok)
    teksten = [v.tekst for v in spel.vragen]
    assert len(set(teksten)) == len(teksten)


def test_eerste_antwoord_telt(spel, klok):
    start_duel(spel, klok)
    assert spel.druk(1, 0)
    assert not spel.druk(1, 1)
    assert spel.spelers[1].antwoord == 0


def test_uitslag_als_iedereen_geantwoord_heeft(spel, klok):
    start_duel(spel, klok)
    spel.druk(1, 0)
    assert spel.fase == VRAAG
    spel.druk(2, 1)
    assert spel.fase == UITSLAG


def test_uitslag_na_tijd(spel, klok):
    start_duel(spel, klok)
    spel.druk(1, 0)
    klok.verder(config.VRAAG_S)
    spel.tik()
    assert spel.fase == UITSLAG
    assert spel.spelers[2].antwoord is None
    assert spel.spelers[2].punten == 0


def test_punten_sneller_is_meer(spel, klok):
    start_duel(spel, klok)
    goed = spel.huidige_vraag.goed
    spel.druk(1, goed)                 # direct
    klok.verder(config.VRAAG_S / 2)
    spel.druk(2, goed)                 # halverwege
    p1, p2 = spel.spelers[1].punten, spel.spelers[2].punten
    assert p1 == config.PUNTEN_GOED + config.PUNTEN_SNELHEID_MAX
    assert p2 == config.PUNTEN_GOED + config.PUNTEN_SNELHEID_MAX // 2
    assert spel.spelers[1].score == p1


def test_fout_antwoord_geen_punten(spel, klok):
    start_duel(spel, klok)
    fout = (spel.huidige_vraag.goed + 1) % 4
    spel.druk(1, fout)
    spel.druk(2, fout)
    assert spel.spelers[1].punten == 0
    assert spel.spelers[1].score == 0


def speel_alles(spel, klok, antwoord_speler1):
    start_duel(spel, klok)
    for _ in range(len(spel.vragen)):
        assert spel.fase == VRAAG
        goed = spel.huidige_vraag.goed
        spel.druk(1, goed if antwoord_speler1 else (goed + 1) % 4)
        spel.druk(2, (goed + 1) % 4)
        assert spel.fase == UITSLAG
        klok.verder(config.UITSLAG_S)
        spel.tik()


def test_heel_spel_tot_einde_en_terug(spel, klok):
    speel_alles(spel, klok, antwoord_speler1=True)
    assert spel.fase == EINDE
    assert spel.winnaar() == 1
    assert spel.spelers[1].aantal_goed == config.AANTAL_VRAGEN
    assert spel.toestand()["winnaar"] == 1
    klok.verder(config.EINDE_S)
    spel.tik()
    assert spel.fase == LOBBY
    assert spel.spelers[1].score == 0


def test_gelijkspel(spel, klok):
    speel_alles(spel, klok, antwoord_speler1=False)
    assert spel.winnaar() == 0


def test_knop_op_eindscherm_pas_na_blokkeertijd(spel, klok):
    speel_alles(spel, klok, antwoord_speler1=True)
    assert not spel.druk(2, 0)
    klok.verder(config.EINDE_BLOKKEER_S)
    assert spel.druk(2, 0)
    assert spel.fase == LOBBY
    assert spel.spelers[2].klaar


def test_antwoord_geheim_tot_uitslag(spel, klok):
    start_duel(spel, klok)
    spel.druk(1, 2)
    t = spel.toestand()
    assert t["vraag"]["goed"] is None
    assert t["spelers"][0]["heeft_geantwoord"] is True
    assert t["spelers"][0]["antwoord"] is None
    spel.druk(2, 1)
    t = spel.toestand()
    assert t["vraag"]["goed"] == spel.huidige_vraag.goed
    assert t["spelers"][0]["antwoord"] == 2


def test_verbinding_van_knoppen(spel, klok):
    assert not spel.is_verbonden(1)
    versie = spel.versie
    spel.heartbeat(1)
    assert spel.is_verbonden(1)
    assert spel.versie > versie  # scherm moet het bijwerken
    klok.verder(config.KNOP_TIMEOUT_S + 0.1)
    versie = spel.versie
    spel.tik()
    assert not spel.is_verbonden(1)
    assert spel.versie > versie


def test_led_patronen(spel, klok):
    assert spel.led_patroon(1) == "BBBB"
    spel.druk(1, 0)
    assert spel.led_patroon(1) == "1111"
    spel.druk(2, 0)
    klok.verder(config.AFTELLEN_S)
    spel.tik()
    assert spel.led_patroon(1) == "1111"
    spel.druk(1, 2)
    assert spel.led_patroon(1) == "0010"
    spel.druk(2, 0)
    goed = spel.huidige_vraag.goed
    assert spel.led_patroon(1) == "".join("B" if i == goed else "0" for i in range(4))
