# 📻 Operatie Nachtzender

Een escaperoom-achtige opdracht voor de open dag. Een Raspberry Pi 5 is een
geheim **number station**: hij roept door de speaker rijen getallen om, zoals
echte spionagezenders in de Koude Oorlog deden. Die getallen zijn een
versleutelde kluiscode. Teams verzamelen bij vijf posten (één per IT-vak) de
sleutel, ontcijferen de code en kraken een kluis met een ESP32. Het scorebord
houdt de snelste teams bij.

Elke post heeft een eigen pagina in de browser met genummerde stappen en
**sleepopdrachten**, plus een "echte" variant met hardware voor wie meer wil.

| Post | Vak | In de browser (slepen) | Echte variant |
|---|---|---|---|
| 1 | **Netwerken** | Netwerk opbouwen, daarna in een mini-Wireshark het live verkeer bekijken en een verborgen HTTP-header vinden | Wireshark op de Pi + ESP32-radio met OLED |
| 2 | **Embedded / IoT** | Zoemer, led en knop naar de goede pinnen slepen, dan de morse ontcijferen | Zelf aansluiten op een ESP32 + breadboard |
| 3 | **Linux** | Commando's naar een terminal slepen (of typen) en met `grep` het archief doorzoeken | Inloggen via SSH op de Pi |
| 4 | **Databases** | Een SQL-query bouwen met blokken en het bericht vinden tussen de lokkertjes | Zelf SQL typen |
| 5 | **Programmeren** | De ontcijferfunctie afmaken met codeblokken, daarna letters uit het codeboek slepen | `ontcijfer.py` in Thonny |
| 🔐 | **Finale** | Code intoetsen | ESP32 met keypad en servo als slot |

**Security** loopt als rode draad door alles heen: afluisteren van
onversleuteld verkeer, inbreken op een server, en waarom een one-time pad
onkraakbaar is.

Wat je moet kopen staat in **[BOODSCHAPPENLIJST.md](BOODSCHAPPENLIJST.md)**;
de printbare opdrachten staan in **[POSTKAARTEN.md](POSTKAARTEN.md)**.

## Hoe de puzzel werkt

Het geheime bericht is altijd `KLUIS` + een kluiscode van 4 cijfers. Met een
codeboek wordt dat 20 cijfers (`01`=A … `26`=Z, `00`=spatie, `30`…`39` = cijfer
0…9), verdeeld in 4 groepen van 5, zoals echte number stations dat doen:

```
bericht     K L  U I  S _  1 4  9 9   →  11122 10919 00313 43939
sleutel   + 74372 69101 74826 14817     (één groep per post 1-4)
            ------------------------  per cijfer, mod 10
zender      85494 79010 74139 57746   ← dit roept de Pi om
```

Op post 5 rekenen de bezoekers terug met `(versleuteld - sleutel) % 10` en
vertalen ze de paren met het codeboek. Bij elke nieuwe ronde kiest de Pi een
nieuwe code en sleutel, en maakt hij het Linux-archief en de database opnieuw.
Afkijken van het vorige team heeft dus geen zin.

### De uitzending

Zoals in de Koude Oorlog: herkenningsmelodie, drie keer "Attentie", drie keer
de roepcode, het aantal groepen, elke groep twee keer, dan alles nog een keer
en "Einde". Dat duurt ongeveer anderhalve minuut. In het beheerscherm kies je
per ronde de stijl:

| Stijl | Klinkt als |
|---|---|
| Nederlands | "Attentie… drie, vier, negen…" |
| Koude Oorlog (Duits) | "Achtung… drei, vier, neun…" met "zwo" zoals de Oost-Duitse stations |
| Iran (Perzisch) | "Tavajjoh…" met Perzische cijfers |

Teams laten elke gevonden sleutelgroep nakijken in het gele vakje op de pagina.
Dan verschijnt er een ✅ op het scorebord: zo zie je hoe ver ze zijn.

```
                 wifi "Nachtzender"
 ┌────────────┐  GET /api/radio  ┌──────────────────────────┐   HDMI 1: scorebord
 │ ESP32 radio│◄────────────────►│                          │──► (tv)
 └────────────┘  GET /api/morse  │    Raspberry Pi 5        │   HDMI 2: Wireshark
 ┌────────────┐◄────────────────►│    nachtzender.py :8080  │──► (post 1)
 │ ESP32 morse│  POST /api/kluis │                          │   USB: speaker
 └────────────┘◄────────────────►│                          │──► 🔊 "Achtung… drei…"
 ┌────────────┐                  └──────────────────────────┘
 │ ESP32 kluis│       laptops: browser op /missie en de postpagina's
 └────────────┘
```

## Snel proberen (op elke computer)

Alleen Python 3.10+ nodig, geen extra pakketten:

```bash
cd nachtzender/pi
python3 nachtzender.py --stil
```

**Op Windows (PowerShell)** gebruik je `python` (of `py`) en typ je de
commando's los onder elkaar, want Windows PowerShell kent `&&` niet:

```powershell
cd nachtzender\pi
python nachtzender.py --stil
```

Open daarna **http://localhost:8080/beheer**, vul een teamnaam in, kies een
zenderstijl en klik op *Start ronde*. Open in andere tabbladen:

- **http://localhost:8080/**: het scorebord voor het grote scherm
- **http://localhost:8080/missie**: de startpagina voor de teams, met links naar alle posten

**Geluid in de demo:** klik rechtsboven op het scorebord op **🔇 Geluid aan**.
De browser speelt dan het melodietje af en leest de getallen voor met een
Nederlandse stem. Dat werkt het mooist in **Microsoft Edge**: die heeft
natuurlijk klinkende stemmen (Fenna, Maarten). Google Chrome heeft
"Google Nederlands". Hoor je een Engelse stem die Nederlands probeert, voeg dan
in Windows de Nederlandse spraak toe via *Instellingen → Tijd en taal → Spraak*.
Voor de stijl Iran heb je een Perzische stem nodig: Edge heeft die (Dilara, Farid).
Laat deze knop op de Pi zelf uit, want daar praat `espeak-ng` al via de speaker.

## Installatie op de Raspberry Pi 5

Uitgaande van Raspberry Pi OS (64-bit, met bureaublad). Geef de Pi bij het
flashen de hostnaam `nachtzender` en de gebruiker `pi`.

```bash
sudo apt update
sudo apt install -y git espeak-ng alsa-utils wireshark
git clone https://github.com/jeroenkoomen1975/achteruitrijcamera_RPI.git
sudo mkdir -p /srv/nachtzender && sudo chown pi: /srv/nachtzender
```

Kies bij de vraag van Wireshark *"Should non-superusers be able to capture
packets?"* voor **Ja**, en daarna:

```bash
sudo usermod -aG wireshark pi      # daarna opnieuw inloggen
```

### Geluid

Sluit de USB-speaker aan en test:

```bash
espeak-ng -v nl "Attentie, attentie" --stdout | aplay
```

Hoor je niets? Kies de speaker als uitvoer via het luidsprekericoon rechtsboven
in het bureaublad. Of zoek het apparaat op met `aplay -l` en start met
`--audio-apparaat plughw:2,0` (vul je eigen kaartnummer in).

Mooiere stem? `espeak-ng` klinkt robotachtig, en dat past juist goed bij een
number station. Wil je toch iets natuurlijkers, kijk dan naar
[Piper](https://github.com/rhasspy/piper) met een Nederlandse stem.

### Post 3: de agent-gebruiker

De bezoekers loggen in als een aparte gebruiker die alleen het archief ziet:

```bash
sudo useradd -m -s /bin/bash agent
echo "agent:uil" | sudo chpasswd
sudo ln -s /srv/nachtzender/linux /home/agent/missie
sudo systemctl enable --now ssh
```

Test vanaf de laptop: `ssh agent@nachtzender.local` (wachtwoord `uil`).

### Starten

```bash
~/achteruitrijcamera_RPI/nachtzender/pi/start_open_dag.sh
```

Dit start de zender en opent het scorebord op volledig scherm. Sleep daarna
Wireshark naar het tweede scherm voor post 1. Bediening gaat via
**http://nachtzender.local:8080/beheer**, bijvoorbeeld vanaf je telefoon.

### Automatisch starten

Wil je dat alles na het aanzetten vanzelf start, voeg dan aan het einde van
`~/.config/labwc/autostart` deze regel toe (maak het bestand aan als het niet
bestaat):

```
/home/pi/achteruitrijcamera_RPI/nachtzender/pi/start_open_dag.sh &
```

De zender start bewust met het bureaublad en niet als systemd-service: zo
werkt het geluid via de gewone audio-instellingen.

## Netwerk

**Aanbevolen: een reisrouter** (zie het boodschappenlijstje). Noem het netwerk
`Nachtzender` met wachtwoord `geheim123`, sluit de Pi met een kabel aan en geef
hem in de router een vast adres, bv. `192.168.8.10`.

**Zonder router:** laat de Pi zelf een hotspot maken. Gebruik dan wel een
kabel naar het schoolnetwerk als je internet nodig hebt, want de wifi is
bezet.

```bash
sudo nmcli device wifi hotspot ifname wlan0 ssid Nachtzender password geheim123
```

De Pi is dan bereikbaar op `10.42.0.1`. Vul dat adres in de ESP32-code in.

## ESP32's programmeren

Gebruik de Arduino IDE (2.x) met het board-pakket **esp32 by Espressif**
(board: *ESP32 Dev Module*). Installeer via de Library Manager:

| Sketch | Bibliotheken |
|---|---|
| `esp32/radio_ontvanger` | Adafruit SSD1306, Adafruit GFX, ArduinoJson (v7) |
| `esp32/morse_post` | geen |
| `esp32/kluis` | Keypad (Mark Stanley), ESP32Servo |

Pas bovenin elke sketch `WIFI_NAAM`, `WIFI_WACHTWOORD` en het IP-adres van de
Pi aan. De aansluitingen staan bovenin elke sketch als commentaar.

**Kluis:** de servo krijgt 5 V van de `VIN`-pin (alleen als de ESP32 via USB
gevoed wordt), niet van 3V3. Druk na elke ronde op **A** om de kluis weer op
slot te doen. Stel de hoeken `DICHT` en `OPEN` af op je kistje.

## Draaiboek voor de open dag

**Opbouw (± 1 uur)**
1. Pi, router en schermen aansluiten; `start_open_dag.sh` draait.
2. ESP32's aan: de radio toont "ruis…", de morseknop knippert 3× (geen missie), de kluis brandt rood.
3. Proefronde op `/beheer`: elke post één keer doorlopen.
4. Op de morsepost de draadjes van zoemer, led en knop weer losmaken.
5. Klik op *Alle scores wissen*.

**Per team (15-25 minuten)**
1. Team kiest een naam, jij kiest de zenderstijl en klikt op *Start ronde*; de zender begint te praten.
2. Geef de startkaart en laat de laptops op `/missie` staan. Tip: laat het team zich verdelen over de posten.
3. Kluis open → de tijd verschijnt op het scorebord. Snoep pakken, kluis dicht (**A**).
4. Morsepost weer losmaken voor het volgende team.

**Als er iets misgaat**
- ESP32 doet het niet? Elke post werkt ook helemaal in de browser.
- Team zit vast? Het spiekbriefje op `/beheer` toont de sleutel en de code.
- Pi herstart? De scores staan in `/srv/nachtzender/scores.json` en blijven bewaard.

## Bestanden

```
nachtzender/
├── pi/
│   ├── nachtzender.py      webserver + API (start dit)
│   ├── spel.py             rondes, codeboek, versleuteling, kluis, scores
│   ├── posten.py           maakt het Linux-archief en de database per ronde
│   ├── terminal.py         veilige mini-terminal voor de Linux-post in de browser
│   ├── omroeper.py         melodie + stem via de speaker (3 zenderstijlen)
│   ├── start_open_dag.sh   zender + scorebord in kioskmodus
│   └── web/                scorebord, missie, beheer en de pagina's per post
├── esp32/
│   ├── radio_ontvanger/    post 1
│   ├── morse_post/         post 2
│   └── kluis/              finale
├── posten-materiaal/
│   └── ontcijfer.py        startbestand voor post 5 (gevorderd, kopieer naar de laptop)
├── tests/                  python3 -m unittest discover -s tests
├── BOODSCHAPPENLIJST.md
└── POSTKAARTEN.md
```

## Uitbreidingsideeën

- **AI/data:** een post waar een klein spraakherkenningsmodel de zender "afluistert".
- **Webdevelopment:** laat bezoekers de kleur of tekst van het scorebord aanpassen in `web/scorebord.html`.
- **Meerdere teams tegelijk:** geef elk team een eigen ronde en een eigen kluiscode.
- **Echte radio:** zend de stem uit via een FM-zendertje (let op de regels voor zenden).
