// ===== Nachtzender: RADIO-ONTVANGER (post 1, Netwerken) =====
// ESP32 + 0,96" OLED (SSD1306, I2C). Haalt elke 2 seconden het bericht op
// bij de zender en toont de getallen. Het sleuteldeel reist onzichtbaar mee
// in een HTTP-header: dat zien de studenten alleen met Wireshark.
//
// Bibliotheken (Library Manager): Adafruit SSD1306, Adafruit GFX, ArduinoJson (v7)
// Aansluiten: OLED VCC->3V3, GND->GND, SDA->GPIO21, SCL->GPIO22

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>

// ---- Instellingen: pas aan voor jouw netwerk ----
const char* WIFI_NAAM = "Nachtzender";
const char* WIFI_WACHTWOORD = "geheim123";
const char* ZENDER_URL = "http://192.168.8.10:8080/api/radio";  // IP van de Pi

Adafruit_SSD1306 scherm(128, 64, &Wire, -1);

void toon(const char* boven, const String& midden, const char* onder) {
  scherm.clearDisplay();
  scherm.setTextColor(SSD1306_WHITE);
  scherm.setTextSize(1);
  scherm.setCursor(0, 0);
  scherm.print(boven);
  scherm.setTextSize(2);
  scherm.setCursor(0, 22);
  scherm.print(midden);
  scherm.setTextSize(1);
  scherm.setCursor(0, 54);
  scherm.print(onder);
  scherm.display();
}

void setup() {
  Serial.begin(115200);
  if (!scherm.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
    Serial.println("OLED niet gevonden: controleer de draden (adres 0x3C?)");
  }
  toon("NACHTZENDER", "Zoeken...", WIFI_NAAM);
  WiFi.begin(WIFI_NAAM, WIFI_WACHTWOORD);
  while (WiFi.status() != WL_CONNECTED) delay(300);
  Serial.print("Verbonden, mijn IP: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    toon("NACHTZENDER", "Geen WiFi", "opnieuw verbinden...");
    WiFi.reconnect();
    delay(2000);
    return;
  }

  HTTPClient http;
  http.begin(ZENDER_URL);
  int status = http.GET();
  if (status == 200) {
    JsonDocument doc;
    if (!deserializeJson(doc, http.getString())) {
      String groepen;
      for (JsonVariant g : doc["groepen"].as<JsonArray>()) {
        if (groepen.length()) groepen += "\n";
        groepen += g.as<const char*>();
      }
      bool live = doc["live"];
      toon(live ? "** LIVE ** NACHTZENDER" : "ruis...", groepen, live ? "Wat reist er nog mee?" : "");
    }
  } else {
    toon("NACHTZENDER", "Geen zender", ("HTTP " + String(status)).c_str());
  }
  http.end();
  delay(2000);
}
