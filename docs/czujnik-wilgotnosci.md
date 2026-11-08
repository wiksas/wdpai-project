# Czujnik wilgotności podłoża

Czujnik przypisuje się do jednego drzewka na jego karcie (`/collection/{id}`, sekcja „Czujnik wilgotności”). Urządzenie wysyła do aplikacji surową wartość odczytu, a aplikacja zamienia ją na procenty według kalibracji (suche = 0%, mokre = 100%). Świeży odczyt (do 12 godzin) zastępuje wilgotność startową okazu: poniżej 40% okaz dostaje status „Wymaga działania”, poniżej 25% „Wymaga podlania”, i trafia do listy zabiegów, powiadomień i harmonogramu.

## Co kupić

Sprawdzony zestaw, który wysyła odczyty bezpośrednio do aplikacji, bez dodatkowej bramki:

| Element | Uwagi | Orientacyjny koszt |
|---|---|---|
| Płytka ESP32 z Wi-Fi (np. ESP32 DevKit) | Wysyła odczyt przez HTTP, zasilanie z USB albo akumulatora 18650 | 25-40 zł |
| Pojemnościowy czujnik wilgotności gleby (Capacitive Soil Moisture Sensor v1.2 albo DFRobot SEN0193) | Napięcie wyjściowe maleje, gdy podłoże robi się mokre; sonda nie koroduje | 8-30 zł |
| Przewody połączeniowe | Zasilanie 3,3 V, GND, wyjście analogowe do pinu ADC | kilka zł |

Czego unikać:

- **Czujniki rezystancyjne** (widełki FC-28, YL-69): w mokrej ziemi korodują w kilka tygodni i po miesiącu pokazują losowe wartości.
- **Gotowe czujniki Xiaomi Mi Flora, Zigbee i Tuya**: nie wyślą odczytu do tej aplikacji bez dodatkowej bramki (np. Home Assistant z webhookiem). Da się to złożyć, ale to osobny projekt, nie gotowe rozwiązanie.

Uwagi do bonsai:

- Podłoże z akadamy, pumeksu i lawy ma dużo powietrza, więc czujnik pojemnościowy łatwo łapie pustkę zamiast korzeni. Wetknij sondę w strefę korzeni i dociśnij podłoże wokół niej.
- Zanurzaj sondę tylko do linii zaznaczonej na płytce; elektronika nie może zamoknąć. Wylot kabla i płytkę zabezpiecz lakierem lub koszulką termokurczliwą.
- Odczyt zależy od donicy i mieszanki, więc **skalibruj każdy czujnik osobno** (patrz niżej).

Nie testowałem tego zestawu na fizycznym urządzeniu. Aplikacja jest sprawdzona na odczytach wysyłanych poleceniem `curl`, więc po stronie sprzętu zostają: okablowanie, napięcie zasilania i sensowny zakres wartości.

## Jak to podłączyć

1. Na karcie okazu kliknij „Dodaj czujnik”. Aplikacja pokaże token **tylko raz**; w bazie zostaje jego skrót. Gdy go zgubisz, kliknij „Wystaw nowy token”.
2. Adres do wysyłania odczytów to `http://ADRES_KOMPUTERA:8080/api/sensor/readings`. ESP32 w sieci domowej potrzebuje adresu IP komputera z Dockerem (np. `192.168.0.12`), a nie `localhost`. Zapora systemu musi przepuszczać port 8080.
3. Wgraj szkic poniżej i wpisz w nim Wi-Fi, adres IP i token.
4. Odczyty pojawią się w sekcji czujnika. Skalibruj czujnik.

## Kalibracja

Czujnik nie zna Twojego podłoża, więc potrzebuje dwóch punktów odniesienia:

1. Gdy sonda tkwi w **suchym** podłożu, a odczyt jest już w aplikacji, kliknij „Ostatni odczyt = suche (0%)”.
2. Podlej drzewko obficie, poczekaj, aż odczyt się ustabilizuje (kilka minut), i kliknij „Ostatni odczyt = mokre (100%)”.

Od tej chwili aplikacja liczy procenty z obu punktów. Zmiana kalibracji poprawia także historię odczytów.

## Protokół urządzenia

`POST /api/sensor/readings`, token w nagłówku `Authorization: Bearer kmr_…` (albo `X-Sensor-Token`, albo w polu `token`), wartość w ciele JSON `{"raw": 1850}` albo formularzu `raw=1850` (liczba całkowita 0-65535).

| Odpowiedź | Znaczenie |
|---|---|
| `200 {"ok": true, "percent": 50}` | Zapisano; `percent` to `null`, dopóki czujnik nie jest skalibrowany |
| `401` | Brak lub zły token |
| `422` | `raw` nie jest liczbą całkowitą 0-65535 |
| `429` | Odczyty najwyżej co 30 sekund (nagłówek `Retry-After: 30`) |

Wysyłaj odczyt co 5-15 minut: podłoże wysycha powoli, a częstsze próbkowanie tylko zużywa baterię.

## Szkic ESP32 (Arduino IDE)

```cpp
#include <WiFi.h>
#include <HTTPClient.h>

const char* WIFI_SSID = "TWOJA_SIEC";
const char* WIFI_PASSWORD = "TWOJE_HASLO";
const char* ENDPOINT = "http://192.168.0.12:8080/api/sensor/readings";
const char* TOKEN = "kmr_WKLEJ_TOKEN_Z_APLIKACJI";

const int SENSOR_PIN = 34;                    // wejście ADC (np. GPIO34)
const unsigned long INTERVAL_MS = 10UL * 60UL * 1000UL;  // co 10 minut

int readAveraged() {
  long sum = 0;
  for (int i = 0; i < 16; i++) {
    sum += analogRead(SENSOR_PIN);
    delay(20);
  }
  return sum / 16;                            // uśrednienie wygładza szum
}

void send(int raw) {
  HTTPClient http;
  http.begin(ENDPOINT);
  http.addHeader("Authorization", String("Bearer ") + TOKEN);
  http.addHeader("Content-Type", "application/json");
  int code = http.POST("{\"raw\": " + String(raw) + "}");
  Serial.printf("raw=%d -> HTTP %d\n", raw, code);
  http.end();
}

void setup() {
  Serial.begin(115200);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  while (WiFi.status() != WL_CONNECTED) delay(500);
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) send(readAveraged());
  delay(INTERVAL_MS);
}
```

Gdy po wysłaniu dostajesz `HTTP -1`, ESP32 nie dociera do komputera (zły adres IP, zapora, inna sieć Wi-Fi). Kod `401` oznacza zły token, a `429` zbyt częste wysyłanie.
