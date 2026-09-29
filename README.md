# Achteruitrij-parkeersensor met Raspberry Pi

Een parkeersensor zoals in een auto: een ultrasone sensor meet de afstand tot
een obstakel achter de auto. Drie LEDs, een buzzer en een LCD-display laten
zien hoe dichtbij je bent.

| Afstand           | LED       | Buzzer                          | Display (regel 2) |
|-------------------|-----------|---------------------------------|-------------------|
| meer dan 50 cm    | 🟢 groen  | stil                            | `VRIJ`            |
| 20 – 50 cm        | 🟢 groen  | langzaam piepen                 | `VRIJ`            |
| 10 – 20 cm        | 🟠 oranje | snel piepen (sneller = dichter) | `LET OP`          |
| 10 cm of minder   | 🔴 rood   | continu piepen                  | `STOP!`           |

Regel 1 van het display toont altijd de afstand, bijvoorbeeld `Afstand: 15.3 cm`.
Alle grenzen staan in [`parkeersensor/config.py`](parkeersensor/config.py) en kun
je daar aanpassen.

## Onderdelen

- Raspberry Pi (3, 4, 5 of Zero 2 W) met Raspberry Pi OS
- HC-SR04 ultrasone afstandssensor
- 3 LEDs: groen, oranje (of geel) en rood
- 3 weerstanden van 220 Ω (voor de LEDs)
- Actieve buzzer, 5V of 3,3V (een actieve buzzer piept zelf als hij spanning krijgt)
- LCD-display 16x2 met I2C-rugje (PCF8574, adres meestal `0x27` of `0x3F`)
- Weerstanden van 1 kΩ en 2 kΩ (spanningsdeler voor de ECHO-pin)
- Breadboard en jumperdraden

## Aansluitschema

> ⚠️ **Belangrijk:** de ECHO-pin van de HC-SR04 geeft 5V. De GPIO-pinnen van de
> Pi kunnen maar 3,3V aan. Gebruik daarom altijd de spanningsdeler hieronder,
> anders kan je Pi kapotgaan.

| Onderdeel         | Pin onderdeel | Raspberry Pi                 | Fysieke pin |
|-------------------|---------------|------------------------------|-------------|
| HC-SR04           | VCC           | 5V                           | 2           |
| HC-SR04           | GND           | GND                          | 6           |
| HC-SR04           | TRIG          | GPIO 23                      | 16          |
| HC-SR04           | ECHO          | GPIO 24 **via spanningsdeler** | 18        |
| LED groen         | + (lange poot)| GPIO 17 (via 220 Ω)          | 11          |
| LED oranje        | + (lange poot)| GPIO 27 (via 220 Ω)          | 13          |
| LED rood          | + (lange poot)| GPIO 22 (via 220 Ω)          | 15          |
| LEDs              | − (korte poot)| GND                          | 9 / 14      |
| Buzzer            | +             | GPIO 18                      | 12          |
| Buzzer            | −             | GND                          | 20          |
| LCD (I2C)         | VCC           | 5V                           | 4           |
| LCD (I2C)         | GND           | GND                          | 25          |
| LCD (I2C)         | SDA           | GPIO 2 (SDA)                 | 3           |
| LCD (I2C)         | SCL           | GPIO 3 (SCL)                 | 5           |

### Spanningsdeler voor de ECHO-pin

```
HC-SR04 ECHO ──[ 1 kΩ ]──┬──────── GPIO 24 (pin 18)
                         │
                      [ 2 kΩ ]
                         │
                        GND
```

5V × 2k / (1k + 2k) ≈ 3,3V. Heb je geen 2 kΩ? Twee weerstanden van 1 kΩ in serie werken ook.

### Buzzer

Een actieve buzzer trekt meestal weinig stroom en kan direct op een GPIO-pin. Is
je buzzer luider of heeft hij meer dan ~15 mA nodig, stuur hem dan aan via een
NPN-transistor (bijv. BC547 of 2N2222) met 1 kΩ op de basis.

## Installatie op de Raspberry Pi

1. **I2C aanzetten** (voor het display):

   ```bash
   sudo raspi-config   # Interface Options → I2C → Enable
   sudo reboot
   ```

   Controleer daarna of het display gevonden wordt (je ziet `27` of `3f`):

   ```bash
   sudo apt install -y i2c-tools
   i2cdetect -y 1
   ```

   Staat er `3f`? Zet dan `LCD_I2C_ADRES = 0x3F` in `parkeersensor/config.py`.

2. **Project ophalen en installeren:**

   ```bash
   cd ~
   git clone https://github.com/jeroenkoomen1975/achteruitrijcamera_RPI.git
   cd achteruitrijcamera_RPI
   sudo apt install -y python3-gpiozero python3-lgpio
   python3 -m venv --system-site-packages .venv
   .venv/bin/pip install -r requirements.txt
   ```

3. **Starten:**

   ```bash
   .venv/bin/python -m parkeersensor.main
   ```

   Stoppen met `Ctrl+C`. De LEDs en de buzzer gaan dan uit.

### Automatisch starten bij het opstarten

```bash
sudo cp systemd/parkeersensor.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable --now parkeersensor
```

Gebruik je een andere gebruikersnaam dan `pi`, pas dan `User` en de paden aan in
`systemd/parkeersensor.service`. Logboek bekijken: `journalctl -u parkeersensor -f`.

Tip voor in de auto: sluit de Pi aan op een USB-lader die op de
achteruitrijlamp is aangesloten. Dan start de sensor alleen als je in z'n
achteruit zet.

## Testen zonder Raspberry Pi

De simulatie laat een auto langzaam naar een muur toe rijden en weer weg. In de
terminal zie je de LED-kleur, de displaytekst en wanneer de buzzer piept:

```bash
python3 -m parkeersensor.main --simulatie
```

Unittests voor de logica (zones, piepritme, displaytekst):

```bash
python3 -m unittest -v
```

## Hoe werkt het?

- `parkeersensor/config.py`: pinnen, afstandsgrenzen en piepinstellingen.
- `parkeersensor/logica.py`: bepaalt de zone, het piepritme en de displaytekst.
  Deze code gebruikt geen hardware en is daardoor te testen.
- `parkeersensor/main.py`: stuurt de hardware aan. De sensor meet 10 keer per
  seconde. Het programma neemt de mediaan van de laatste 3 metingen, zodat
  losse foute metingen genegeerd worden. De buzzer piept zonder `sleep`
  ertussen, dus het meten gaat door terwijl hij piept.

## Problemen oplossen

| Probleem                          | Oplossing                                                        |
|-----------------------------------|------------------------------------------------------------------|
| Display blijft leeg of toont blokjes | Draai aan het blauwe potmetertje op het I2C-rugje (contrast).  |
| `LCD niet gevonden`               | I2C aanzetten, adres controleren met `i2cdetect -y 1`.           |
| Afstand staat altijd op maximum   | TRIG en ECHO omgewisseld? Spanningsdeler goed aangesloten?       |
| Afstand springt heen en weer      | Sensor recht naar achteren richten, zet `GEMIDDELDE_VAN` op 5.   |
| Buzzer piept niet                 | Is het een passieve buzzer? Die heeft een toon nodig; gebruik een actieve buzzer. |
