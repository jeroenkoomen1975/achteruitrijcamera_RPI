import json
import random
from collections import Counter

import pytest

from quizserver import protocol
from quizserver.vragen import kies_vragen, laad_vragen, schud_antwoorden


def test_vragenbank_is_geldig():
    vragen = laad_vragen()
    per_categorie = Counter(v.categorie for v in vragen)
    assert set(per_categorie) == {
        "programmeren", "iot", "netwerken", "burgerschap",
        "nederlands", "engels", "windows", "hardware",
    }
    assert min(per_categorie.values()) >= 5


def test_alle_categorieen_komen_voor():
    bank = laad_vragen()
    for seed in range(20):
        gekozen = kies_vragen(bank, 10, random.Random(seed))
        assert len(gekozen) == 10
        assert len({v.categorie for v in gekozen}) == 8
        for a, b in zip(gekozen, gekozen[1:]):
            assert a.categorie != b.categorie


def test_schudden_houdt_goede_antwoord_goed():
    vraag = laad_vragen()[0]
    juist = vraag.antwoorden[vraag.goed]
    for seed in range(20):
        geschud = schud_antwoorden(vraag, random.Random(seed))
        assert geschud.antwoorden[geschud.goed] == juist
        assert sorted(geschud.antwoorden) == sorted(vraag.antwoorden)


def test_goede_antwoord_niet_altijd_a():
    gekozen = kies_vragen(laad_vragen(), 10, random.Random(1))
    assert len({v.goed for v in gekozen}) > 1


def test_foute_vragenbank_geeft_duidelijke_fout(tmp_path):
    pad = tmp_path / "vragen.json"
    pad.write_text(json.dumps({
        "categorieen": {"x": {"naam": "X"}},
        "vragen": [{"categorie": "x", "vraag": "?", "antwoorden": ["a", "b", "c"], "goed": 0}],
    }))
    with pytest.raises(ValueError, match="4 antwoorden"):
        laad_vragen(pad)


@pytest.mark.parametrize("data, verwacht", [
    (b"HELLO 1", protocol.Hello(1)),
    (b"BTN 2 3 1234", protocol.Knop(2, 3, 1234)),
    (b"BTN 2 x 1", None),
    (b"HELLO", None),
    (b"", None),
    (b"\xff\xfe", None),
    (b"ONZIN 1", None),
])
def test_protocol(data, verwacht):
    assert protocol.lees_bericht(data) == verwacht


def test_protocol_antwoorden():
    assert protocol.ack(7) == b"ACK 7"
    assert protocol.led("10B0") == b"LED 10B0"
