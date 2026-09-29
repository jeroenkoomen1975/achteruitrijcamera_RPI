import sys
import types
import unittest
from unittest import mock

from PIL import Image

from parkeersensor.logica import Zone
from parkeersensor.scherm import KLEUR, naar_rgb565, teken_scherm


class TestScherm(unittest.TestCase):
    def test_formaat_en_statuskleur(self):
        for zone in Zone:
            beeld = teken_scherm(15, zone, 200, 20, 10)
            self.assertEqual(beeld.size, (320, 240))
            self.assertEqual(beeld.getpixel((2, 2)), KLEUR[zone])

    def test_staand_formaat(self):
        beeld = teken_scherm(None, Zone.GROEN, 200, 20, 10, breedte=240, hoogte=320)
        self.assertEqual(beeld.size, (240, 320))

    def test_rgb565(self):
        beeld = Image.new("RGB", (2, 1))
        beeld.putpixel((0, 0), (255, 0, 0))
        beeld.putpixel((1, 0), (0, 0, 255))
        self.assertEqual(naar_rgb565(beeld), bytes([0xF8, 0x00, 0x00, 0x1F]))


class NepSpi:
    def __init__(self):
        self.geschreven = []

    def open(self, bus, device):
        pass

    def writebytes(self, data):
        self.geschreven.append(bytes(data))

    def writebytes2(self, data):
        self.geschreven.append(bytes(data))

    def close(self):
        pass


class TestST7789(unittest.TestCase):
    def setUp(self):
        self.spi = NepSpi()
        nep_spidev = types.SimpleNamespace(SpiDev=lambda: self.spi)
        nep_gpiozero = types.SimpleNamespace(DigitalOutputDevice=mock.MagicMock())
        self.modules = mock.patch.dict(
            sys.modules, {"spidev": nep_spidev, "gpiozero": nep_gpiozero}
        )
        self.modules.start()
        sys.modules.pop("parkeersensor.st7789", None)

    def tearDown(self):
        self.modules.stop()
        sys.modules.pop("parkeersensor.st7789", None)

    def test_spi_mode_3_en_volledig_beeld(self):
        from parkeersensor.st7789 import ST7789

        with mock.patch("time.sleep"):
            tft = ST7789(dc_pin=25, rst_pin=5, bl_pin=6, rotatie=90)
        self.assertEqual(self.spi.mode, 3)
        self.assertEqual((tft.breedte, tft.hoogte), (320, 240))

        tft.toon(Image.new("RGB", (320, 240)))
        self.assertEqual(len(self.spi.geschreven[-1]), 320 * 240 * 2)
        # kolommen 0..319, rijen 0..239
        self.assertIn(bytes([0, 0, 0x01, 0x3F]), self.spi.geschreven)
        self.assertIn(bytes([0, 0, 0x00, 0xEF]), self.spi.geschreven)


if __name__ == "__main__":
    unittest.main()
