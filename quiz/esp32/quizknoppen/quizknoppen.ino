// Quizknoppen: één knoppenkast met 4 arcade buttons voor de ICT Quiz Battle.
//
// - Leest 4 knoppen (met ontdendering) en stuurt elke druk via UDP naar de Pi.
// - Stuurt een druk opnieuw tot de Pi hem bevestigt (ACK), zodat er niks kwijtraakt.
// - Zet de lampjes in de knoppen zoals de Pi vraagt (aan, uit of knipperen).
//
// Board: "ESP32 Dev Module" (Arduino IDE met het esp32-pakket van Espressif).
// Instellingen staan in config.h.

#include <WiFi.h>
#include <WiFiUdp.h>
#include "config.h"

const uint8_t AANTAL = 4;
const uint8_t knopPinnen[AANTAL] = KNOP_PINNEN;
#if LEDS_AANWEZIG
const uint8_t ledPinnen[AANTAL] = LED_PINNEN;
#endif

const unsigned long ONTDENDER_MS = 20;      // knop moet zo lang stabiel zijn
const unsigned long HELLO_MS = 1000;        // "ik ben er nog" naar de Pi
const unsigned long OPNIEUW_MS = 120;       // druk opnieuw sturen als ACK uitblijft
const uint8_t MAX_POGINGEN = 8;
const unsigned long SERVER_STIL_MS = 4000;  // zo lang niks gehoord = verbinding kwijt

WiFiUDP udp;
bool udpGestart = false;

IPAddress serverIp;
bool serverVast = false;            // SERVER_IP ingevuld in config.h
unsigned long laatsteAntwoordMs = 0;
bool serverGezien = false;

char ledPatroon[AANTAL + 1] = "0000";

// Ontdendering per knop
bool knopStabiel[AANTAL];           // true = ingedrukt
bool knopLaatsteMeting[AANTAL];
unsigned long knopVeranderdMs[AANTAL];

// De druk die nog op een ACK wacht
uint32_t volgnummer;
bool wachtOpAck = false;
uint32_t wachtVolgnr;
uint8_t wachtKnop;
uint8_t wachtPogingen;
unsigned long wachtVerstuurdMs;

unsigned long laatsteHelloMs = 0;

// ------------------------------------------------------------------ netwerk

bool serverBereikbaar() {
  return serverGezien && millis() - laatsteAntwoordMs < SERVER_STIL_MS;
}

void stuur(const char *tekst) {
  if (!udpGestart) return;
  // Pi nog niet gevonden? Dan naar iedereen in het netwerk (broadcast).
  IPAddress doel = (serverVast || serverBereikbaar()) ? serverIp : IPAddress(255, 255, 255, 255);
  udp.beginPacket(doel, UDP_POORT);
  udp.print(tekst);
  udp.endPacket();
}

void stuurKnop() {
  char bericht[40];
  snprintf(bericht, sizeof(bericht), "BTN %d %u %lu", SPELER_NUMMER, wachtKnop, (unsigned long)wachtVolgnr);
  stuur(bericht);
  wachtVerstuurdMs = millis();
  wachtPogingen++;
}

void zorgVoorWifi() {
  static unsigned long laatstePogingMs = 0;
  if (WiFi.status() == WL_CONNECTED) {
    if (!udpGestart) {
      udp.begin(UDP_POORT);
      udpGestart = true;
      Serial.printf("Wifi verbonden, mijn IP: %s\n", WiFi.localIP().toString().c_str());
    }
    return;
  }
  if (udpGestart) {
    udp.stop();
    udpGestart = false;
    Serial.println("Wifi kwijt, opnieuw verbinden...");
  }
  if (millis() - laatstePogingMs > 5000) {
    laatstePogingMs = millis();
    WiFi.disconnect();
    WiFi.begin(WIFI_NAAM, WIFI_WACHTWOORD);
  }
}

void ontvang() {
  if (!udpGestart) return;
  while (udp.parsePacket() > 0) {
    char buf[32];
    int n = udp.read(buf, sizeof(buf) - 1);
    if (n <= 0) continue;
    buf[n] = '\0';

    // Alleen LED en ACK komen van de server. HELLO/BTN van de andere kast
    // (die ook broadcast) negeren we, anders denken we dat die de server is.
    bool isLed = strncmp(buf, "LED ", 4) == 0 && n >= 4 + AANTAL;
    bool isAck = strncmp(buf, "ACK ", 4) == 0;
    if (!isLed && !isAck) continue;

    if (!serverVast) serverIp = udp.remoteIP();
    if (!serverBereikbaar()) Serial.printf("Quizserver gevonden op %s\n", udp.remoteIP().toString().c_str());
    serverGezien = true;
    laatsteAntwoordMs = millis();

    if (isLed) {
      memcpy(ledPatroon, buf + 4, AANTAL);
      ledPatroon[AANTAL] = '\0';
    } else {
      uint32_t nr = strtoul(buf + 4, nullptr, 10);
      if (wachtOpAck && nr == wachtVolgnr) wachtOpAck = false;
    }
  }
}

// ------------------------------------------------------------------ knoppen

void leesKnoppen() {
  unsigned long nu = millis();
  for (uint8_t i = 0; i < AANTAL; i++) {
    bool ingedrukt = digitalRead(knopPinnen[i]) == LOW;  // pull-up: ingedrukt = LOW
    if (ingedrukt != knopLaatsteMeting[i]) {
      knopLaatsteMeting[i] = ingedrukt;
      knopVeranderdMs[i] = nu;
    }
    if (nu - knopVeranderdMs[i] >= ONTDENDER_MS && ingedrukt != knopStabiel[i]) {
      knopStabiel[i] = ingedrukt;
      if (ingedrukt) {
        Serial.printf("Knop %c ingedrukt\n", 'A' + i);
        wachtOpAck = true;
        wachtVolgnr = ++volgnummer;
        wachtKnop = i;
        wachtPogingen = 0;
        stuurKnop();
      }
    }
  }
}

void herhaalKnop() {
  if (!wachtOpAck) return;
  if (wachtPogingen >= MAX_POGINGEN) {
    wachtOpAck = false;  // opgeven; de Pi is waarschijnlijk niet bereikbaar
    return;
  }
  if (millis() - wachtVerstuurdMs >= OPNIEUW_MS) stuurKnop();
}

void stuurHello() {
  if (millis() - laatsteHelloMs < HELLO_MS) return;
  laatsteHelloMs = millis();
  char bericht[16];
  snprintf(bericht, sizeof(bericht), "HELLO %d", SPELER_NUMMER);
  stuur(bericht);
}

// ------------------------------------------------------------------ lampjes

void zetLeds() {
  unsigned long nu = millis();
  bool verbonden = serverBereikbaar();
  digitalWrite(STATUS_LED, verbonden ? HIGH : LOW);
#if LEDS_AANWEZIG
  for (uint8_t i = 0; i < AANTAL; i++) {
    bool aan;
    if (!verbonden) {
      aan = (nu / 150) % AANTAL == i;   // looplicht: "ik zoek de server"
    } else if (ledPatroon[i] == 'B') {
      aan = (nu / 250) % 2 == 0;        // knipperen
    } else {
      aan = ledPatroon[i] == '1';
    }
    digitalWrite(ledPinnen[i], aan == (LED_AAN_BIJ_HOOG == 1) ? HIGH : LOW);
  }
#endif
}

// ------------------------------------------------------------------ start

void setup() {
  Serial.begin(115200);
  Serial.printf("\nQuizknoppen - speler %d\n", SPELER_NUMMER);

  pinMode(STATUS_LED, OUTPUT);
  for (uint8_t i = 0; i < AANTAL; i++) {
    pinMode(knopPinnen[i], INPUT_PULLUP);
    knopStabiel[i] = knopLaatsteMeting[i] = false;
    knopVeranderdMs[i] = 0;
#if LEDS_AANWEZIG
    pinMode(ledPinnen[i], OUTPUT);
#endif
  }

  // Willekeurig beginnen, zodat een herstarte ESP32 niet hetzelfde volgnummer hergebruikt.
  volgnummer = esp_random() & 0xFFFFFF;

  if (strlen(SERVER_IP) > 0 && serverIp.fromString(SERVER_IP)) {
    serverVast = true;
  }

  WiFi.mode(WIFI_STA);
  WiFi.setSleep(false);  // geen stroombesparing: anders kan een druk 100+ ms vertraging krijgen
  WiFi.begin(WIFI_NAAM, WIFI_WACHTWOORD);
  Serial.printf("Verbinden met wifi '%s'...\n", WIFI_NAAM);
}

void loop() {
  zorgVoorWifi();
  leesKnoppen();
  ontvang();
  herhaalKnop();
  stuurHello();
  zetLeds();
}
