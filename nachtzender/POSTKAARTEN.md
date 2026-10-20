# Postkaarten

Print elke kaart op A5 en leg hem bij de post. Onderaan staan de antwoorden
voor de begeleiders.

---

## 📻 Startkaart: Operatie Nachtzender

Al weken zendt een geheime zender elke avond rijen getallen uit. Wij weten:
het is een versleutelde **kluiscode** voor agent UIL.

De zender gebruikt een **one-time pad**: bij elk cijfer van de code is een
geheim sleutelcijfer opgeteld. Die 4 sleutelcijfers liggen verspreid over de
posten.

1. Luister naar de zender en schrijf het bericht op (het staat ook op het scorebord).
2. Verdeel jullie team over de posten 1 t/m 4 en verzamel de sleutelcijfers.
3. Ontcijfer de code op post 5.
4. Kraak de kluis. De klok loopt!

| Bericht | | | | |
|---|---|---|---|---|
| Sleutel | 1: ___ | 2: ___ | 3: ___ | 4: ___ |
| Kluiscode | ___ | ___ | ___ | ___ |

---

## 📡 Post 1: Netwerken

De radio-ontvanger op tafel (een ESP32) vraagt elke 2 seconden over wifi
het laatste bericht op bij de zender. Op het schermpje zie je alleen de
getallen, maar in het netwerkverkeer reist meer mee…

1. Ga naar de Pi en open **Wireshark**. Dubbelklik op de netwerkkaart (`wlan0` of `eth0`).
2. Typ bovenin het filter: `http` en druk op Enter.
3. Klik op een regel met `HTTP/1.0 200 OK` (het antwoord op `GET /api/radio`).
4. Klap onderin *Hypertext Transfer Protocol* open. Zie je een vreemde header?

**Wist je dat?** Alles wat niet versleuteld is (http in plaats van https)
kan iedereen op hetzelfde netwerk meelezen.

---

## 🔦 Post 2: Embedded / IoT

Deze ESP32 kan sleuteldeel 2 in morse seinen, maar de schakeling is nog niet af!

1. Bouw de schakeling op het breadboard:
   - **zoemer:** + naar pin `25`, − naar `GND`
   - **led:** lange poot via de weerstand (220 Ω) naar pin `26`, korte poot naar `GND`
   - **knop:** tussen pin `27` en `GND`
2. Druk op de knop en luister/kijk goed. Kort = punt, lang = streep.
3. Zoek het cijfer op in de morsetabel.

| 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 |
|---|---|---|---|---|---|---|---|---|---|
| −−−−− | ·−−−− | ··−−− | ···−− | ····− | ····· | −···· | −−··· | −−−·· | −−−−· |

**Wist je dat?** Waarom een weerstand voor de led? Zonder weerstand loopt
er te veel stroom en brandt de led door.

---

## 🐧 Post 3: Linux

Inbreken op de computer van de zender. Open een terminal en log in:

```
ssh agent@nachtzender.local        (wachtwoord: uil)
```

1. Kijk rond met `ls` en lees `cat missie/LEESMIJ.txt`.
2. Ergens in `missie/archief` staat `SLEUTELDEEL_3`. Er zijn honderden regels:
   gebruik `grep` om te zoeken in plaats van alles te lezen.
3. Let op: niet elk sleuteldeel is nog **GELDIG**.

**Wist je dat?** Bijna alle servers op internet draaien Linux, en beheerders
werken vooral via de terminal.

---

## 🗄️ Post 4: Databases

Open op de laptop: **http://nachtzender.local:8080/sql**

De inlichtingendienst heeft honderden berichten onderschept. Zoek het
bericht dat zender **NACHTZENDER** stuurde aan agent **UIL**.

```sql
SELECT * FROM uitzendingen LIMIT 10;
```

Tip: met `WHERE` filter je, met `AND` combineer je twee voorwaarden.
Tekst zet je tussen enkele aanhalingstekens: `'UIL'`.

**Wist je dat?** Elke app die je gebruikt (Instagram, je bank, Magister)
slaat zijn gegevens op in een database en praat er met SQL tegen.

---

## 🐍 Post 5: Programmeren

Open `ontcijfer.py` in Thonny (of VS Code).

1. Vul het bericht van de zender en de 4 sleutelcijfers in.
2. Maak de functie `ontcijfer` af. Versleutelen ging zo:
   `versleuteld = (code + sleutel) % 10`
3. Druk op **Run** en ga met de code naar de kluis!

**Wist je dat?** Een one-time pad is wiskundig onkraakbaar, zolang je de
sleutel maar één keer gebruikt. Spionnen gebruikten dit echt, met boekjes vol
willekeurige getallen.

---

## 🔐 De kluis

Toets de 4 cijfers en druk op `#`. Fout getypt? Druk op `*`.

---

# Antwoorden voor begeleiders

De actuele sleutel en code van elke ronde staan op **/beheer** (spiekbriefje).

- **Post 1:** antwoordheader `X-Sleuteldeel-1: <cijfer>`.
  Wireshark-filter voor gevorderden: `http.response and http.server contains "Nachtzender"`.
- **Post 2:** zoemer/led/knop zoals op de kaart; morse komt van `/api/morse`.
  Werkt het niet? Laat de browserversie `/morse` zien.
- **Post 3:** `grep -r SLEUTELDEEL missie/` laat ook de VERLOPEN-lokkertjes zien;
  `grep -r GELDIG missie/` geeft het juiste antwoord. Dat staat in het verborgen
  bestand `missie/archief/nacht/.geheim.log` (zichtbaar met `ls -a`).
- **Post 4:** `SELECT bericht FROM uitzendingen WHERE zender = 'NACHTZENDER' AND agent = 'UIL';`
- **Post 5:** `return (versleuteld - sleutel) % 10`
