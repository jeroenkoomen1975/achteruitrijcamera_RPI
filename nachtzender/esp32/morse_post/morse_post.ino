// ===== Nachtzender: MORSE-POST (post 2, Embedded / IoT) =====
// De studenten bouwen zelf de schakeling op het breadboard (zie kaartje).
// Druk op de knop: de ESP32 vraagt sleutelgroep 2 (5 cijfers) op bij de zender
// en seint die als morse met de zoemer en de led.
//
// Geen extra bibliotheken nodig.
// Aansluiten:  actieve zoemer + -> GPIO25, - -> GND
//              led (lange poot) -> 220 ohm -> GPIO26, korte poot -> GND
//              drukknop tussen GPIO27 en GND

#include <WiFi.h>
#include <HTTPClient.h>

// ---- Instellingen: pas aan voor jouw netwerk ----
const char* WIFI_NAAM = "Nachtzender";
const char* WIFI_WACHTWOORD = "geheim123";
const char* ZENDER_URL = "http://192.168.8.10:8080/api/morse";  // IP van de Pi

const int ZOEMER = 25;
const int LED = 26;
const int KNOP = 27;
const int EENHEID = 200;  // ms: punt = 1, streep = 3, pauze = 1, tussen cijfers = 4 eenheden

void toon(int duur) {
  digitalWrite(ZOEMER, HIGH);
  digitalWrite(LED, HIGH);
  delay(duur);
  digitalWrite(ZOEMER, LOW);
  digitalWrite(LED, LOW);
  delay(EENHEID);
}

void knipperFout() {  // 3x snel: geen missie of geen verbinding
  for (int i = 0; i < 3; i++) {
    digitalWrite(LED, HIGH);
    delay(80);
    digitalWrite(LED, LOW);
    delay(80);
  }
}

String haalMorse() {
  HTTPClient http;
  http.begin(ZENDER_URL);
  String morse;
  if (http.GET() == 200) {
    // Antwoord ziet eruit als {"tekens": "38211", "morse": "...-- ---.. ..--- .---- .----"}
    String body = http.getString();
    int start = body.indexOf("\"morse\": \"");
    if (start >= 0) {
      start += 10;
      morse = body.substring(start, body.indexOf('"', start));
    }
  }
  http.end();
  return morse;
}

void setup() {
  Serial.begin(115200);
  pinMode(ZOEMER, OUTPUT);
  pinMode(LED, OUTPUT);
  pinMode(KNOP, INPUT_PULLUP);
  WiFi.begin(WIFI_NAAM, WIFI_WACHTWOORD);
  while (WiFi.status() != WL_CONNECTED) {
    digitalWrite(LED, !digitalRead(LED));
    delay(300);
  }
  digitalWrite(LED, LOW);
  Serial.println("Verbonden met de Nachtzender-WiFi");
}

void loop() {
  if (digitalRead(KNOP) == LOW) {
    String morse = haalMorse();
    Serial.println("Morse: " + morse);
    if (morse.length() == 0) {
      knipperFout();
    }
    for (char teken : morse) {
      if (teken == ' ') {
        delay(4 * EENHEID);  // stilte tussen twee cijfers
      } else {
        toon(teken == '.' ? EENHEID : 3 * EENHEID);
      }
    }
    delay(500);  // knop loslaten
  }
}
