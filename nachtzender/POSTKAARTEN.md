# Postkaarten

Print elke kaart op A5 en leg hem bij de post. Elke post heeft ook een eigen
pagina in de browser met dezelfde opdracht in stappen, plus sleepopdrachten.
Onderaan staan de antwoorden voor de begeleiders.

---

## 📻 Startkaart: Operatie Nachtzender

Al weken zendt een geheime zender elke nacht rijen getallen uit, net als de
spionagezenders uit de Koude Oorlog. Wij weten: het is een versleuteld bericht
met de **code van een kluis**.

De zender gebruikt een **one-time pad**: bij elk cijfer is een geheim
sleutelcijfer opgeteld. De sleutel is verdeeld in 4 groepen van 5 cijfers,
en die liggen verspreid over de posten.

1. Luister naar de zender en noteer de **4 groepen** van 5 cijfers (ze staan ook op het grote scherm).
2. Verdeel jullie team over post 1 t/m 4 en verzamel de **4 sleutelgroepen**.
   Laat elke groep nakijken in het gele vakje op de pagina: dan komt er een ✅ op het scorebord.
3. Ontcijfer het bericht op post 5.
4. Kraak de kluis. De klok loopt!

| | Groep 1 | Groep 2 | Groep 3 | Groep 4 |
|---|---|---|---|---|
| Bericht | | | | |
| Sleutel | | | | |

Startpagina op de laptop: **http://nachtzender.local:8080/missie**

---

## 🌐 Post 1: Netwerken

Pagina: **/netwerken**

De radio-ontvanger op tafel (een ESP32) vraagt elke 2 seconden over wifi het
laatste bericht op bij de zender. Op het schermpje zie je alleen de getallen…

1. **Bouw het netwerk:** sleep de apparaten op de goede plek en klik op *Verbinding testen*.
2. **Luister het verkeer af:** klik in de pakketlijst op een antwoord (`HTTP/1.0 200 OK`),
   klap *Headers* open en zoek de vreemde header.
3. Vul de sleutelgroep in en klik op *Controleer*.

Voor echte hackers: open **Wireshark** op de Pi, kies `wlan0` en filter op `http`.

---

## 🔌 Post 2: Embedded / IoT

Pagina: **/embedded** · of gebruik de echte ESP32 op tafel

1. Bouw de schakeling op het breadboard (of sleep de onderdelen in de browser):
   - **zoemer:** + naar pin `25`, − naar `GND`
   - **led:** lange poot via de weerstand (220 Ω) naar pin `26`, korte poot naar `GND`
   - **knop:** tussen pin `27` en `GND`
2. Druk op de knop. De ESP32 seint **5 cijfers** in morse. Kort = punt, lang = streep,
   een lange stilte = volgend cijfer.
3. Zoek elk cijfer op in de tabel.

| 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 |
|---|---|---|---|---|---|---|---|---|---|
| −−−−− | ·−−−− | ··−−− | ···−− | ····− | ····· | −···· | −−··· | −−−·· | −−−−· |

---

## 🐧 Post 3: Linux

Pagina: **/linux** · of echt via SSH: `ssh agent@nachtzender.local` (wachtwoord `uil`)

1. Sleep `cat LEESMIJ.txt` naar de terminal en druk op Enter.
2. Zoek in het archief naar `SLEUTELGROEP_3`. Er zijn duizenden regels: laat `grep` het zoekwerk doen.
3. Let op: niet elke sleutelgroep is nog **GELDIG**.

---

## 🗄️ Post 4: Databases

Pagina: **/database**

Zoek het bericht dat zender **NACHTZENDER** stuurde aan agent **UIL**.

1. Sleep de SQL-blokken in de goede volgorde naar de balk: `SELECT … FROM … WHERE …`
2. Krijg je te veel rijen? Maak je voorwaarde strenger met `AND`.

---

## 🧮 Post 5: Ontcijferen

Pagina: **/ontcijferen** · gevorderd: `ontcijfer.py` in Thonny

1. Vul het bericht en de 4 sleutelgroepen in.
2. Sleep de blokken in de functie zodat hij de versleuteling **terugdraait**.
   Versleutelen ging zo: `(bericht + sleutel) % 10`.
3. Vertaal elk paar cijfers met het codeboek: `01`=A … `26`=Z, `00`=spatie, `30`…`39` = cijfer 0…9.

---

## 🔐 De kluis

Toets de 4 cijfers en druk op `#`. Fout getypt? Druk op `*`.

---

# Antwoorden voor begeleiders

De actuele sleutel, klaartekst en code van elke ronde staan op **/beheer** (spiekbriefje).
Daar zie je ook welke posten het team al heeft gehaald.

- **Post 1:** antwoordheader `X-Sleutelgroep-1: <5 cijfers>`. Netwerk: ESP32-radio → wifi-router → Raspberry Pi.
- **Post 2:** zoemer 25, led 26, knop 27. Werkt de ESP32 niet? Gebruik de browserpagina.
- **Post 3:** `grep -r SLEUTELGROEP .` toont ook de VERLOPEN-lokkertjes; de GELDIGE staat in het verborgen
  bestand `archief/nacht/.geheim.log` (`ls -a`). `grep SLEUTELGROEP *.log` in `archief` vindt hem níét:
  hij zit in een submap.
- **Post 4:** `SELECT bericht FROM uitzendingen WHERE zender = 'NACHTZENDER' AND agent = 'UIL'`
- **Post 5:** `return (versleuteld - sleutel) % 10`. Het bericht is altijd `KLUIS ` + 4 cijfers.
