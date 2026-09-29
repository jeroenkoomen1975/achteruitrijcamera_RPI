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

class Hardware:
    """Echte hardware via gpiozero en RPLCD."""

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
        self.lcd = self._maak_lcd()

    @staticmethod
    def _maak_lcd():
        try:
            from RPLCD.i2c import CharLCD

            lcd = CharLCD(
                i2c_expander="PCF8574",
                address=config.LCD_I2C_ADRES,
                port=1,
                cols=config.LCD_KOLOMMEN,
                rows=config.LCD_RIJEN,
            )
            lcd.clear()
            return lcd
        except Exception as fout:  # display is optioneel: sensor werkt ook zonder
            print(f"Waarschuwing: LCD niet gevonden ({fout}); tekst gaat naar de terminal.")
            return None

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

    def toon(self, regel1, regel2):
        if self.lcd is None:
            print(f"\r{regel1} | {regel2}", end="", flush=True)
            return
        self.lcd.cursor_pos = (0, 0)
        self.lcd.write_string(regel1)
        self.lcd.cursor_pos = (1, 0)
        self.lcd.write_string(regel2)

    def sluit(self):
        self.zet_buzzer(False)
        for led in self.leds.values():
            led.off()
        if self.lcd is not None:
            self.lcd.clear()
            self.lcd.write_string("Parkeersensor".center(config.LCD_KOLOMMEN))
            self.lcd.close(clear=False)
        self.sensor.close()


class SimulatieHardware:
    """Nep-hardware: auto rijdt langzaam achteruit naar een muur en weer terug."""

    KLEUR = {Zone.GROEN: "\033[92m", Zone.ORANJE: "\033[33m", Zone.ROOD: "\033[91m"}

    def __init__(self):
        self.start = time.monotonic()
        self.zone = Zone.GROEN
        self.buzzer_aan = False

    def meet_afstand_cm(self):
        t = time.monotonic() - self.start
        # cosinus tussen ~120 cm en ~3 cm, periode 20 s
        return 61.5 + 58.5 * math.cos(2 * math.pi * t / 20)

    def zet_led(self, zone):
        self.zone = zone

    def zet_buzzer(self, aan):
        self.buzzer_aan = aan

    def toon(self, regel1, regel2):
        kleur = self.KLEUR[self.zone]
        piep = "PIEP" if self.buzzer_aan else "    "
        print(f"\r{kleur}● \033[0m[{regel1}] [{regel2}] {piep}", end="", flush=True)

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
    vorige_tekst = None
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

            tekst = display_regels(afstand, zone, config.MAX_DISTANCE_CM, config.LCD_KOLOMMEN)
            if tekst != vorige_tekst:  # LCD alleen bijwerken als er iets verandert
                hardware.toon(*tekst)
                vorige_tekst = tekst

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
    args = parser.parse_args(argv)

    hardware = SimulatieHardware() if args.simulatie else Hardware()

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
