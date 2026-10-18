"""Instellingen voor de quizserver.

Pas hier tijden, punten en poorten aan. De ESP32-knoppen moeten dezelfde
UDP_POORT gebruiken (zie esp32/quizknoppen/config.h).
"""

# --- Netwerk ---
HTTP_POORT = 8080      # het scherm: open http://<ip-van-de-pi>:8080
UDP_POORT = 4210       # hier sturen de ESP32-knoppen hun berichten naartoe

# --- Spelverloop (in seconden) ---
AANTAL_VRAGEN = 10
AFTELLEN_S = 3                 # "3, 2, 1" voor de eerste vraag
VRAAG_S = 15                   # bedenktijd per vraag
UITSLAG_S = 7                  # hoe lang het goede antwoord + uitleg in beeld blijft
WACHT_OP_TEGENSTANDER_S = 15   # na zoveel seconden start één speler alleen
EINDE_S = 30                   # daarna gaat het scherm vanzelf terug naar de lobby
EINDE_BLOKKEER_S = 4           # zo lang telt een knop op het eindscherm nog niet

# --- Punten ---
PUNTEN_GOED = 500              # voor een goed antwoord
PUNTEN_SNELHEID_MAX = 500      # extra bij direct antwoorden, loopt af naar 0

# --- Knoppen ---
AANTAL_KNOPPEN = 4             # A (rood), B (blauw), C (groen), D (geel)
KNOP_TIMEOUT_S = 3.0           # zo lang zonder bericht = knoppenkast niet verbonden
