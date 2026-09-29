"""Kleine driver voor ST7789-displays zoals de GMT020-02-7P (2.0", 240x320, SPI).

De 7-pins versie heeft geen CS-pin. Daarom moet SPI in mode 3 draaien;
in mode 0 blijft het scherm wit of zwart.
"""

import time

import spidev
from gpiozero import DigitalOutputDevice

from .scherm import naar_rgb565

# ST7789-commando's
SWRESET = 0x01
SLPOUT = 0x11
NORON = 0x13
INVON = 0x21
DISPON = 0x29
CASET = 0x2A
RASET = 0x2B
RAMWR = 0x2C
MADCTL = 0x36
COLMOD = 0x3A

# MADCTL-waarde per rotatie (MY=0x80, MX=0x40, MV=0x20)
_MADCTL_ROTATIE = {0: 0xC0, 90: 0xA0, 180: 0x00, 270: 0x60}


class ST7789:
    def __init__(self, dc_pin, rst_pin, bl_pin=None, spi_bus=0, spi_device=0,
                 breedte=240, hoogte=320, rotatie=90, invert=True, bgr=False,
                 snelheid_hz=40_000_000):
        if rotatie not in _MADCTL_ROTATIE:
            raise ValueError("rotatie moet 0, 90, 180 of 270 zijn")

        self.dc = DigitalOutputDevice(dc_pin)
        self.rst = DigitalOutputDevice(rst_pin, initial_value=True)
        self.bl = DigitalOutputDevice(bl_pin) if bl_pin is not None else None

        self.spi = spidev.SpiDev()
        self.spi.open(spi_bus, spi_device)
        self.spi.mode = 3  # nodig voor modules zonder CS-pin
        self.spi.max_speed_hz = snelheid_hz

        liggend = rotatie in (90, 270)
        self.breedte = hoogte if liggend else breedte
        self.hoogte = breedte if liggend else hoogte

        madctl = _MADCTL_ROTATIE[rotatie] | (0x08 if bgr else 0x00)
        self._init(madctl, invert)

    def _commando(self, cmd, *data):
        self.dc.off()
        self.spi.writebytes([cmd])
        if data:
            self.dc.on()
            self.spi.writebytes(list(data))

    def _init(self, madctl, invert):
        # hardware-reset
        self.rst.off()
        time.sleep(0.02)
        self.rst.on()
        time.sleep(0.15)

        self._commando(SWRESET)
        time.sleep(0.15)
        self._commando(SLPOUT)
        time.sleep(0.12)
        self._commando(COLMOD, 0x55)  # 16 bit per pixel
        self._commando(MADCTL, madctl)
        if invert:
            self._commando(INVON)  # IPS-panelen zoals de GMT020-02 hebben dit nodig
        self._commando(NORON)
        self._commando(DISPON)
        time.sleep(0.05)
        if self.bl is not None:
            self.bl.on()

    def toon(self, beeld):
        """Stuur een Pillow-afbeelding (breedte x hoogte) naar het scherm."""
        if beeld.size != (self.breedte, self.hoogte):
            beeld = beeld.resize((self.breedte, self.hoogte))
        x1, y1 = self.breedte - 1, self.hoogte - 1
        self._commando(CASET, 0, 0, x1 >> 8, x1 & 0xFF)
        self._commando(RASET, 0, 0, y1 >> 8, y1 & 0xFF)
        self._commando(RAMWR)
        self.dc.on()
        self.spi.writebytes2(naar_rgb565(beeld))  # writebytes2 knipt zelf in stukken

    def sluit(self):
        if self.bl is not None:
            self.bl.off()
        self.spi.close()
        for pin in (self.dc, self.rst, self.bl):
            if pin is not None:
                pin.close()
