# Boodschappenlijstje Nachtzender

Prijzen zijn richtprijzen (incl. btw) bij Nederlandse webshops zoals Kiwi
Electronics, TinyTronics of Otronic. Controleer ze even voordat je bestelt.

Legenda: ✅ heb je al · 🛒 aanschaffen · 🏫 lenen van school

## 1. De zender (Raspberry Pi 5)

| | Aantal | Onderdeel | Waarvoor | Ca. prijs |
|---|---|---|---|---|
| ✅ | 1 | Raspberry Pi 5 (4 of 8 GB) | Zender, webserver, scorebord | – |
| 🛒 | 1 | Officiële 27 W USB-C-voeding voor de Pi 5 | Met een gewone lader krijg je onderspanning | € 13 |
| 🛒 | 1 | Active Cooler voor de Pi 5 | Hij draait de hele dag; zonder koeling gaat hij langzamer werken | € 6 |
| 🛒 | 1 | Behuizing voor de Pi 5 | Bescherming op de open dag | € 6–12 |
| 🛒 | 1 | microSD 32 GB, A2 (bv. SanDisk Extreme) | Raspberry Pi OS | € 12 |
| 🛒 | 1 | **USB-speaker** (of USB-geluidskaartje + actieve speakers) | ⚠️ De Pi 5 heeft géén koptelefoonaansluiting meer | € 15–25 |
| 🛒 | 2 | Micro-HDMI-naar-HDMI-kabel | Scherm 1 = scorebord, scherm 2 = Wireshark-post | € 2 × 7 |
| 🏫 | 2 | Monitor of tv met HDMI | Scorebord + werkplek | – |
| 🏫 | 1 | Toetsenbord + muis (USB) | Wireshark-post op de Pi | – |

## 2. Eigen wifi-netwerk (sterk aanbevolen)

Het schoolnetwerk blokkeert vaak het verkeer tussen apparaten, of heeft een
inlogpagina waar een ESP32 niet mee overweg kan. Een eigen netwerkje voorkomt
veel stress op de dag zelf.

| | Aantal | Onderdeel | Waarvoor | Ca. prijs |
|---|---|---|---|---|
| 🛒 | 1 | Reisrouter, bv. GL.iNet GL-SFT1200 (Opal) of GL-MT3000 | Netwerk "Nachtzender" voor de Pi, de ESP32's en de laptops | € 35–90 |
| 🛒 | 1 | UTP-kabel (Cat6, 1–2 m) | Pi vast aan de router: stabieler | € 4 |

Geen router kopen? De Pi kan ook zelf een hotspot maken (zie de README). Dat
werkt, maar met meer dan 5 à 6 apparaten wordt het minder stabiel.

## 3. Post 1: Netwerken (radio-ontvanger)

| | Aantal | Onderdeel | Ca. prijs |
|---|---|---|---|
| ✅ | 1 | ESP32 DevKit (ESP32-WROOM-32) | – |
| 🛒 | 1 | 0,96" OLED 128×64, SSD1306, **I2C** (4 pinnen) | € 5 |
| 🛒 | 1 | Breadboard (400 of 830 punten) | € 3 |
| 🛒 | 1 set | Jumperdraadjes male-male en male-female (bv. 3 × 40 stuks) | € 5 |

## 4. Post 2: Embedded / IoT (morse)

De studenten bouwen deze schakeling zelf. Koop dus ook wat reserveonderdelen.

| | Aantal | Onderdeel | Ca. prijs |
|---|---|---|---|
| ✅ | 1 | ESP32 DevKit | – |
| 🛒 | 3 | **Actieve** zoemer 3,3–5 V (piept vanzelf als hij spanning krijgt) | € 3 |
| 🛒 | 1 zak | Leds 5 mm (rood, groen, geel) | € 3 |
| 🛒 | 1 zak | Weerstanden 220 Ω | € 2 |
| 🛒 | 3 | Drukknop 6×6 mm (tactile switch) | € 1 |
| 🛒 | 1 | Breadboard 830 punten | € 4 |

## 5. Finale: de kluis

| | Aantal | Onderdeel | Ca. prijs |
|---|---|---|---|
| ✅ | 1 | ESP32 DevKit | – |
| 🛒 | 1 | 4×4-membraankeypad | € 3 |
| 🛒 | 1 | Servo **MG90S** (metalen tandwielen, sterker dan de SG90) | € 5 |
| 🛒 | 1 | Houten kistje met scharnierend deksel (Action of hobbywinkel) | € 5–10 |
| 🛒 | 1 | Klein haakje of oogschroefje waar de servoarm achter valt | € 2 |
| 🛒 | – | Actieve zoemer, rode en groene led, 2 × 220 Ω | (zie post 2) |
| 🛒 | 1 | Snoep of kleine prijsjes voor in de kluis 🍬 | € 10 |

## 6. Voeding en kabels voor de ESP32's

| | Aantal | Onderdeel | Ca. prijs |
|---|---|---|---|
| 🛒 | 4 | USB-kabel **met datalijnen** (micro-USB of USB-C: check je board) | € 4 × 3 |
| 🛒 | 1 | USB-lader met 4 poorten (min. 2 A per poort), of 3 powerbanks | € 15–20 |
| 🛒 | 1 | Verlengsnoer met 6 stopcontacten | € 10 |
| 🛒 | 1 | Reserve-ESP32 | € 8 |

## 7. Posten met een laptop

| | Aantal | Onderdeel | Waarvoor |
|---|---|---|---|
| 🏫 | 1 | Laptop met browser + SSH (Windows Terminal is genoeg) | Post 3: Linux |
| 🏫 | 1 | Laptop met browser | Post 4: Databases (`/sql`) |
| 🏫 | 1 | Laptop met Python + Thonny of VS Code | Post 5: Programmeren |

Weinig laptops? Posten 3 en 4 passen samen op één laptop: SSH in het ene
venster, de browser in het andere.

## 8. Aankleding (optioneel, maar het maakt het af)

| | Onderdeel | Ca. prijs |
|---|---|---|
| 🛒 | Postkaarten printen en lamineren (A5) | € 5 |
| 🛒 | Oude radio of kastje van de kringloop als behuizing voor de Pi en de speaker | € 5–15 |
| 🛒 | Rode lamp of ledstrip voor de sfeer bij het scorebord | € 10 |

## Totaal

| Variant | Ca. kosten |
|---|---|
| Minimaal: geen router (Pi als hotspot), geen aankleding | **€ 120** |
| Aanbevolen: alles hierboven | **€ 200–260** |

De demo werkt ook al zonder ESP32's en losse onderdelen: elke post heeft een
browserversie (`/radio`, `/morse`, `/sql`, `/kluis`).
