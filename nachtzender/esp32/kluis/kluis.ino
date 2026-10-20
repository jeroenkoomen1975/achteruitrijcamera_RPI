// ===== Nachtzender: DE KLUIS (finale) =====
// 4x4-keypad + servo als slot. Toets de 4 cijfers en druk op #.
// De ESP32 vraagt de zender of de code klopt. Zo ja: servo draait, groen licht.
//   *  = invoer wissen
//   A  = kluis weer op slot (voor de spelleider, na elke ronde)
//
// Bibliotheken (Library Manager): Keypad (Mark Stanley), ESP32Servo
// Aansluiten:  keypad rijen  -> GPIO32, 33, 25, 26
//              keypad kolommen -> GPIO27, 14, 16, 17
//              servo signaal (oranje) -> GPIO18, rood -> 5V (VIN), bruin -> GND
//              groene led -> 220 ohm -> GPIO19, rode led -> 220 ohm -> GPIO21
//              actieve zoemer -> GPIO22

#include <WiFi.h>
#include <HTTPClient.h>
#include <Keypad.h>
#include <ESP32Servo.h>

// ---- Instellingen: pas aan voor jouw netwerk ----
const char* WIFI_NAAM = "Nachtzender";
const char* WIFI_WACHTWOORD = "geheim123";
const char* ZENDER_URL = "http://192.168.8.10:8080/api/kluis";  // IP van de Pi

const int SERVO_PIN = 18, GROEN = 19, ROOD = 21, ZOEMER = 22;
const int DICHT = 0, OPEN = 90;  // servohoeken: pas aan op jouw slot

char toetsen[4][4] = {
  {'1', '2', '3', 'A'},
  {'4', '5', '6', 'B'},
  {'7', '8', '9', 'C'},
  {'*', '0', '#', 'D'},
};
byte rijPinnen[4] = {32, 33, 25, 26};
byte kolomPinnen[4] = {27, 14, 16, 17};
Keypad keypad = Keypad(makeKeymap(toetsen), rijPinnen, kolomPinnen, 4, 4);

Servo slot;
String invoer;

void piep(int duur) {
  digitalWrite(ZOEMER, HIGH);
  delay(duur);
  digitalWrite(ZOEMER, LOW);
}

void opSlot() {
  slot.write(DICHT);
  digitalWrite(GROEN, LOW);
  digitalWrite(ROOD, HIGH);
}

void openKluis() {
  slot.write(OPEN);
  digitalWrite(ROOD, LOW);
  digitalWrite(GROEN, HIGH);
  for (int i = 0; i < 3; i++) {
    piep(100);
    delay(80);
  }
}

void fout() {
  for (int i = 0; i < 3; i++) {
    digitalWrite(ROOD, LOW);
    delay(120);
    digitalWrite(ROOD, HIGH);
    delay(120);
  }
  piep(600);
}

bool vraagZender(const String& code) {
  HTTPClient http;
  http.begin(ZENDER_URL);
  http.addHeader("Content-Type", "application/json");
  int status = http.POST("{\"code\":\"" + code + "\"}");
  String antwoord = status == 200 ? http.getString() : "";
  http.end();
  Serial.println("Zender zegt: " + antwoord);
  return antwoord.indexOf("\"open\": true") >= 0;
}

void setup() {
  Serial.begin(115200);
  pinMode(GROEN, OUTPUT);
  pinMode(ROOD, OUTPUT);
  pinMode(ZOEMER, OUTPUT);
  slot.attach(SERVO_PIN);
  opSlot();
  WiFi.begin(WIFI_NAAM, WIFI_WACHTWOORD);
  while (WiFi.status() != WL_CONNECTED) delay(300);
  Serial.println("Kluis verbonden");
}

void loop() {
  char t = keypad.getKey();
  if (!t) return;
  piep(30);
  if (t >= '0' && t <= '9' && invoer.length() < 4) {
    invoer += t;
  } else if (t == '*') {
    invoer = "";
  } else if (t == 'A') {
    invoer = "";
    opSlot();
  } else if (t == '#') {
    if (vraagZender(invoer)) openKluis();
    else fout();
    invoer = "";
  }
  Serial.println("Invoer: " + invoer);
}
