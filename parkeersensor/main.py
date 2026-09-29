"""Achteruitrij-parkeersensor voor Raspberry Pi.

Starten op de Pi:        python3 -m parkeersensor.main
Testen zonder hardware:  python3 -m parkeersensor.main --simulatie
"""

import argparse
import math
import signal
import sys
import time

from . import config
from .logica import Zone, bepaal_zone, display_regels, mediaan, piep_pauze

LUS_INTERVAL_S = 0.01  # hoofdlus draait vaak zodat het piepritme strak blijft


# ---------------------------------------------------------------------------
# Hardware
# ---------------------------------------------------------------------------

class TftDisplay:
    """2.0" kleurendisplay GMT020-02-7P (ST7789) via SPI."""

    def __init__(self):
        from .st7789 import ST7789

        self.tft = ST7789(
            dc_pin=config.TFT_DC_PIN,
            rst_pin=config.TFT_RST_PIN,
            bl_pin=config.TFT_BLK_PIN,
            rotatie=config.TFT_ROTATIE,
            invert=config.TFT_INVERT,
            bgr=config.TFT_BGR,
            snelheid_hz=config.TFT_SPI_SNELHEID_HZ,
        )
        self.vorige = None

    def toon(self, afstand_cm, zone):
        from .scherm import teken_scherm

        # hele cm's, zodat het scherm niet bij elke millimeter opnieuw getekend wordt
        sleutel = (None if afstand_cm is None else round(afstand_cm), zone)
        if sleutel == self.vorige:
            return
        self.vorige = sleutel
        self.tft.toon(teken_scherm(
            afstand_cm, zone, config.MAX_DISTANCE_CM,
            config.ORANJE_GRENS_CM, config.ROOD_GRENS_CM,
            self.tft.breedte, self.tft.hoogte,
        ))

    def sluit(self):
        from PIL import Image

        self.tft.toon(Image.new("RGB", (self.tft.breedte, self.tft.hoogte)))
        self.tft.sluit()


class LcdDisplay:
    """16x2 tekstdisplay met I2C-rugje (PCF8574)."""

    def __init__(self):
        from RPLCD.i2c import CharLCD

        self.lcd = CharLCD(
            i2c_expander="PCF8574",
            address=config.LCD_I2C_ADRES,
            port=1,
            cols=config.LCD_KOLOMMEN,
            rows=config.LCD_RIJEN,
        )
        self.lcd.clear()
        self.vorige = None

    def toon(self, afstand_cm, zone):
        regels = display_regels(afstand_cm, zone, config.MAX_DISTANCE_CM, config.LCD_KOLOMMEN)
        if regels == self.vorige:  # LCD alleen bijwerken als er iets verandert
            return
        self.vorige = regels
        for rij, regel in enumerate(regels):
            self.lcd.cursor_pos = (rij, 0)
            self.lcd.write_string(regel)

    def sluit(self):
        self.lcd.clear()
        self.lcd.write_string("Parkeersensor".center(config.LCD_KOLOMMEN))
        self.lcd.close(clear=False)


class TerminalDisplay:
    """Reserve als er geen display gevonden wordt: tekst in de terminal."""

    def __init__(self):
        self.vorige = None

    def toon(self, afstand_cm, zone):
        regels = display_regels(afstand_cm, zone, config.MAX_DISTANCE_CM)
        if regels != self.vorige:
            self.vorige = regels
            print(f"\r{regels[0]} | {regels[1]}", end="", flush=True)

    def sluit(self):
        print()


def maak_display():
    soorten = {"st7789": TftDisplay, "lcd1602": LcdDisplay}
    try:
        return soorten[config.DISPLAY_TYPE]()
    except Exception as fout:  # display is optioneel: sensor werkt ook zonder
        print(f"Waarschuwing: display '{config.DISPLAY_TYPE}' werkt niet ({fout}); "
              "tekst gaat naar de terminal.")
        return TerminalDisplay()


class Hardware:
    """Echte hardware via gpiozero."""

    def __init__(self):
        from gpiozero import LED, Buzzer, DistanceSensor

        self.sensor = DistanceSensor(
            echo=config.ECHO_PIN,
            trigger=config.TRIG_PIN,
            max_distance=config.MAX_DISTANCE_CM / 100,
            queue_len=1,
        )
        self.leds = {
            Zone.GROEN: LED(config.LED_GROEN_PIN),
            Zone.ORANJE: LED(config.LED_ORANJE_PIN),
            Zone.ROOD: LED(config.LED_ROOD_PIN),
        }
        self.buzzer = Buzzer(config.BUZZER_PIN)
        self.display = maak_display()

    def meet_afstand_cm(self):
        afstand = self.sensor.distance * 100  # gpiozero geeft meters
        if afstand >= config.MAX_DISTANCE_CM - 0.5:
            return None  # niets binnen bereik
        return afstand

    def zet_led(self, zone):
        for led_zone, led in self.leds.items():
            led.value = led_zone is zone

    def zet_buzzer(self, aan):
        self.buzzer.value = aan

    def toon(self, afstand_cm, zone):
        self.display.toon(afstand_cm, zone)

    def sluit(self):
        self.zet_buzzer(False)
        for led in self.leds.values():
            led.off()
        self.display.sluit()
        self.sensor.close()


class SimulatieHardware:
    """Nep-hardware: auto rijdt langzaam achteruit naar een muur en weer terug.

    Met scherm_png wordt het beeld van het kleurendisplay steeds naar dat
    bestand geschreven, zodat je het op de pc kunt bekijken.
    """

    KLEUR = {Zone.GROEN: "\033[92m", Zone.ORANJE: "\033[33m", Zone.ROOD: "\033[91m"}

    def __init__(self, scherm_png=None):
        self.start = time.monotonic()
        self.zone = Zone.GROEN
        self.buzzer_aan = False
        self.scherm_png = scherm_png

    def meet_afstand_cm(self):
        t = time.monotonic() - self.start
        # cosinus tussen ~120 cm en ~3 cm, periode 20 s
        return 61.5 + 58.5 * math.cos(2 * math.pi * t / 20)

    def zet_led(self, zone):
        self.zone = zone

    def zet_buzzer(self, aan):
        self.buzzer_aan = aan

    def toon(self, afstand_cm, zone):
        regel1, regel2 = display_regels(afstand_cm, zone, config.MAX_DISTANCE_CM)
        kleur = self.KLEUR[self.zone]
        piep = "PIEP" if self.buzzer_aan else "    "
        print(f"\r{kleur}● \033[0m[{regel1}] [{regel2}] {piep}", end="", flush=True)
        if self.scherm_png:
            from .scherm import teken_scherm

            teken_scherm(afstand_cm, zone, config.MAX_DISTANCE_CM,
                         config.ORANJE_GRENS_CM, config.ROOD_GRENS_CM).save(self.scherm_png)

    def sluit(self):
        print()


# ---------------------------------------------------------------------------
# Hoofdprogramma
# ---------------------------------------------------------------------------

class Piper:
    """Niet-blokkerende piepgenerator: aanroepen in elke lus-ronde."""

    def __init__(self, hardware):
        self.hw = hardware
        self.uit_om = 0.0
        self.volgende_piep = 0.0

    def update(self, pauze, nu):
        if pauze is None:
            self.hw.zet_buzzer(False)
            self.volgende_piep = nu
            return
        if pauze == 0:
            self.hw.zet_buzzer(True)
            self.volgende_piep = nu
            return
        if nu >= self.volgende_piep:
            self.hw.zet_buzzer(True)
            self.uit_om = nu + config.PIEP_DUUR_S
            self.volgende_piep = self.uit_om + pauze
        elif nu >= self.uit_om:
            self.hw.zet_buzzer(False)


def draai(hardware):
    piper = Piper(hardware)
    metingen = []
    afstand = None
    zone = Zone.GROEN
    volgende_meting = 0.0

    while True:
        nu = time.monotonic()

        if nu >= volgende_meting:
            volgende_meting = nu + config.METING_INTERVAL_S
            metingen.append(hardware.meet_afstand_cm())
            metingen = metingen[-config.GEMIDDELDE_VAN:]
            afstand = mediaan(metingen)

            zone = bepaal_zone(afstand, config.ORANJE_GRENS_CM, config.ROOD_GRENS_CM)
            hardware.zet_led(zone)

            hardware.toon(afstand, zone)

        pauze = piep_pauze(
            afstand, config.ORANJE_GRENS_CM, config.ROOD_GRENS_CM, config.PIEP_START_CM
        )
        piper.update(pauze, nu)

        time.sleep(LUS_INTERVAL_S)


def main(argv=None):
    parser = argparse.ArgumentParser(description="Achteruitrij-parkeersensor")
    parser.add_argument(
        "--simulatie", action="store_true", help="draai zonder Raspberry Pi-hardware"
    )
    parser.add_argument(
        "--scherm-png", metavar="BESTAND",
        help="(bij --simulatie) schrijf het beeld van het kleurendisplay naar dit PNG-bestand",
    )
    args = parser.parse_args(argv)

    hardware = SimulatieHardware(args.scherm_png) if args.simulatie else Hardware()

    # netjes afsluiten bij Ctrl+C en bij 'systemctl stop'
    signal.signal(signal.SIGTERM, lambda *_: sys.exit(0))
    try:
        draai(hardware)
    except KeyboardInterrupt:
        pass
    finally:
        hardware.sluit()


if __name__ == "__main__":
    main()
