#include <ESP8266WiFi.h>
#include <WiFiUdp.h>
#include <OneWire.h>
#include <DallasTemperature.h>

#define STASSID "Maintenance"
#define STAPSK "0987654321"

const int oneWireBus = D2;

OneWire oneWire(oneWireBus);
DallasTemperature sensors(&oneWire);

WiFiUDP Udp;

void setup() {
  Serial.begin(115200);
  sensors.begin();

  WiFi.mode(WIFI_STA);
  WiFi.begin(STASSID, STAPSK);
  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }
  Serial.print("\nTerhubung ke WiFi! IP address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }

  sensors.requestTemperatures();
  float temperatureC = sensors.getTempCByIndex(0);
  float temperatureF = sensors.getTempFByIndex(0);

  Serial.print(temperatureC);
  Serial.println("ºC");
  Serial.print(temperatureF);
  Serial.println("ºF");

  Udp.beginPacket("192.168.67.50", 11000);
  Udp.write("Prodi Teknik Industri;");
  char buff[20];
  dtostrf(temperatureC, 4, 2, buff); // mengubah float ke string
  Udp.write(buff);
  Udp.endPacket();

  delay(60000);
}