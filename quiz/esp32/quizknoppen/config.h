// Instellingen voor één knoppenkast. Pas dit aan vóór het uploaden.
#pragma once

// 1 voor de linker kast, 2 voor de rechter kast. Elke ESP32 krijgt een ander nummer!
#define SPELER_NUMMER 1

// Het wifi-netwerk van de Raspberry Pi (zie de README: "Hotspot op de Pi").
#define WIFI_NAAM       "QuizNet"
#define WIFI_WACHTWOORD "quiz12345"

// IP-adres van de Pi. Leeg laten ("") = automatisch zoeken via broadcast.
// Werkt dat op jouw netwerk niet, vul dan het adres in, bijv. "10.42.0.1".
#define SERVER_IP ""

// Moet gelijk zijn aan UDP_POORT in quizserver/config.py.
#define UDP_POORT 4210

// GPIO-pinnen van de knoppen A (rood), B (blauw), C (groen), D (geel).
// Knop tussen pin en GND; de interne pull-up wordt gebruikt.
#define KNOP_PINNEN {32, 33, 25, 26}

// GPIO-pinnen voor de lampjes in de knoppen, in dezelfde volgorde.
// Geen lampjes aangesloten? Zet LEDS_AANWEZIG op 0.
#define LEDS_AANWEZIG 1
#define LED_PINNEN {21, 22, 23, 27}
// 1 = pin HOOG maakt de led aan (via transistor/ULN2803, of led direct op de pin)
#define LED_AAN_BIJ_HOOG 1

// Ingebouwde led van het ESP32-bordje: brandt zodra de server antwoordt.
#define STATUS_LED 2
