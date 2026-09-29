"""Tekent het beeld voor het kleurendisplay (GMT020-02, 320x240) met Pillow.

Deze module gebruikt geen GPIO of SPI, zodat de schermopbouw ook op een pc
getest en bekeken kan worden.
"""

import numpy as np
from PIL import Image, ImageDraw, ImageFont

from .logica import Zone, bepaal_zone

ZWART = (0, 0, 0)
WIT = (255, 255, 255)
GRIJS = (45, 45, 45)
KLEUR = {
    Zone.GROEN: (0, 200, 70),
    Zone.ORANJE: (255, 140, 0),
    Zone.ROOD: (230, 20, 20),
}
STATUS = {Zone.GROEN: "VRIJ", Zone.ORANJE: "LET OP", Zone.ROOD: "STOP!"}

FONT_PAD = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
BALK_SEGMENTEN = 10
BALK_BEREIK_CM = 100  # de balk loopt van 100 cm (leeg) tot 0 cm (vol)

_fonts = {}


def _font(grootte):
    if grootte not in _fonts:
        try:
            _fonts[grootte] = ImageFont.truetype(FONT_PAD, grootte)
        except OSError:
            _fonts[grootte] = ImageFont.load_default(size=grootte)
    return _fonts[grootte]


def _gecentreerd(teken, midden_x, midden_y, tekst, font, kleur):
    teken.text((midden_x, midden_y), tekst, font=font, fill=kleur, anchor="mm")


def teken_scherm(afstand_cm, zone, max_afstand_cm, oranje_grens_cm, rood_grens_cm,
                 breedte=320, hoogte=240):
    """Maak een Pillow-afbeelding met status, afstand in cm en een afstandsbalk."""
    beeld = Image.new("RGB", (breedte, hoogte), ZWART)
    teken = ImageDraw.Draw(beeld)
    kleur = KLEUR[zone]

    # Statusbalk bovenaan in de kleur van de zone
    balk_h = hoogte * 24 // 100
    teken.rectangle((0, 0, breedte - 1, balk_h), fill=kleur)
    tekst_kleur = WIT if zone is Zone.ROOD else ZWART
    _gecentreerd(teken, breedte // 2, balk_h // 2 + 2, STATUS[zone],
                 _font(hoogte * 16 // 100), tekst_kleur)

    # Grote afstand in het midden
    midden_y = balk_h + (hoogte * 30 // 100)
    if afstand_cm is None or afstand_cm >= max_afstand_cm:
        _gecentreerd(teken, breedte // 2, midden_y, f"> {max_afstand_cm:.0f} cm",
                     _font(hoogte * 18 // 100), WIT)
    else:
        getal = f"{afstand_cm:.0f}"
        groot, klein = _font(hoogte * 38 // 100), _font(hoogte * 14 // 100)
        getal_b = teken.textlength(getal, font=groot)
        eenheid_b = teken.textlength(" cm", font=klein)
        x = (breedte - getal_b - eenheid_b) / 2
        teken.text((x, midden_y), getal, font=groot, fill=kleur, anchor="lm")
        teken.text((x + getal_b, midden_y + hoogte * 8 // 100), " cm",
                   font=klein, fill=WIT, anchor="lm")

    # Afstandsbalk onderaan: hoe dichterbij, hoe meer blokjes
    marge = breedte * 4 // 100
    tussen = 4
    seg_b = (breedte - 2 * marge - (BALK_SEGMENTEN - 1) * tussen) / BALK_SEGMENTEN
    y0, y1 = hoogte * 80 // 100, hoogte * 94 // 100
    stap = BALK_BEREIK_CM / BALK_SEGMENTEN
    for i in range(BALK_SEGMENTEN):
        # segment i staat voor afstanden tussen BEREIK-(i+1)*stap en BEREIK-i*stap
        ondergrens = BALK_BEREIK_CM - (i + 1) * stap
        seg_zone = bepaal_zone(ondergrens + stap / 2, oranje_grens_cm, rood_grens_cm)
        aan = afstand_cm is not None and afstand_cm < BALK_BEREIK_CM - i * stap
        x0 = marge + i * (seg_b + tussen)
        teken.rectangle((round(x0), y0, round(x0 + seg_b), y1),
                        fill=KLEUR[seg_zone] if aan else GRIJS)

    return beeld


def naar_rgb565(beeld):
    """Zet een Pillow RGB-afbeelding om naar RGB565 (big-endian) bytes voor de ST7789."""
    a = np.asarray(beeld.convert("RGB"), dtype=np.uint16)
    rgb565 = ((a[..., 0] & 0xF8) << 8) | ((a[..., 1] & 0xFC) << 3) | (a[..., 2] >> 3)
    return rgb565.astype(">u2").tobytes()
