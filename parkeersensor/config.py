"""Instellingen voor de achteruitrij-parkeersensor.

Alle GPIO-nummers zijn BCM-nummers (dus GPIO23, niet fysieke pin 16).
Pas hier de pinnen en afstanden aan naar jouw opstelling.
"""

# --- Ultrasone sensor (HC-SR04) ---
TRIG_PIN = 23          # fysieke pin 16
ECHO_PIN = 24          # fysieke pin 18 (via spanningsdeler! 5V -> 3.3V)
MAX_DISTANCE_CM = 200  # verder weg telt als "niks achter de auto"

# --- LEDs ---
LED_GROEN_PIN = 17     # fysieke pin 11
LED_ORANJE_PIN = 27    # fysieke pin 13
LED_ROOD_PIN = 22      # fysieke pin 15

# --- Buzzer (actieve buzzer) ---
BUZZER_PIN = 18        # fysieke pin 12

# --- Display ---
# "st7789"  = 2.0" kleurendisplay GMT020-02-7P (SPI, 240x320)
# "lcd1602" = 16x2 tekstdisplay met I2C-rugje (PCF8574)
DISPLAY_TYPE = "st7789"

# GMT020-02-7P (ST7789). SCL -> GPIO 11 (pin 23), SDA -> GPIO 10 (pin 19): vast (SPI0)
TFT_DC_PIN = 25        # fysieke pin 22
TFT_RST_PIN = 5        # fysieke pin 29
TFT_BLK_PIN = 6        # fysieke pin 31; None als BLK op 3.3V zit
TFT_ROTATIE = 90       # 90 of 270 = liggend (320x240); 0 of 180 = staand
TFT_INVERT = True      # kleuren negatief? zet dan op False
TFT_BGR = False        # rood en blauw omgewisseld? zet dan op True
TFT_SPI_SNELHEID_HZ = 40_000_000  # strepen of storing? probeer 20_000_000

# 16x2 LCD met I2C-rugje (alleen als DISPLAY_TYPE = "lcd1602")
LCD_I2C_ADRES = 0x27   # soms 0x3F; controleer met: i2cdetect -y 1
LCD_KOLOMMEN = 16
LCD_RIJEN = 2

# --- Afstandszones (in cm) ---
# afstand >  ORANJE_GRENS_CM              -> GROEN  (niks dichtbij)
# ROOD_GRENS_CM < afstand <= ORANJE_GRENS -> ORANJE (let op, ca. 20 cm)
# afstand <= ROOD_GRENS_CM                -> ROOD   (stop!)
ORANJE_GRENS_CM = 20
ROOD_GRENS_CM = 10

# --- Piepgedrag ---
# In de groene zone piept de buzzer alleen als er binnen deze afstand
# iets gezien wordt (langzaam piepen). Zet op 0 om groen altijd stil te houden.
PIEP_START_CM = 50
PIEP_DUUR_S = 0.05     # lengte van één piep

# --- Meten ---
METING_INTERVAL_S = 0.1  # 10 metingen per seconde
GEMIDDELDE_VAN = 3       # mediaan over zoveel metingen tegen uitschieters
