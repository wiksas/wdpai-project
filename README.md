# KOMOREBI: cyfrowy dziennik opiekuna bonsai

Aplikacja webowa (czysty PHP 8.3, MVC bez frameworka) pomagająca opiekunom bonsai monitorować kondycję drzewek, planować zabiegi formowania i reagować na objawy chorób. Kierunek wizualny i powody decyzji projektowych opisuje `DESIGN.md`. Historyjki użytkownika, specyfikacja i przypadki użycia są w katalogu [`docs/`](docs/README.md).

## Funkcje

| Obszar | Trasa | Dostęp | Co robi |
|---|---|---|---|
| Strona startowa | `/` | publiczny | Przewodnik: szybka ocena ryzyka, najczęstsze błędy, protokół pierwszej pomocy, style formowania |
| Logowanie | `/login` | gość | Logowanie loginem lub e-mailem, komunikaty o błędach |
| Rejestracja | `/register` | gość | Założenie konta hodowcy (walidacja, hasło hashowane); konto aktywuje link wysłany e-mailem |
| Potwierdzenie e-maila | `/verify-email` | wszyscy | „Sprawdź skrzynkę” po rejestracji, obsługa linku aktywacyjnego (24 h, jednorazowy), ponowna wysyłka (raz na minutę) |
| Wylogowanie | `POST /logout` | zalogowany | Menu konta w nagłówku |
| Moja Kolekcja | `/collection` | zalogowany | Panel kolekcji: filtry (iglaste/liściaste/do podlania) i wyszukiwarka na żywo, podlewanie, dziennik obserwacji, nadchodzące zabiegi, przycisk „Dodaj drzewko” (maks. 12 okazów) |
| Nowe drzewko | `/collection/new` | zalogowany | Formularz własnego okazu: nazwa, gatunek, rodzaj, styl, wiek, donica i opcjonalne zdjęcie; błędy przy polach, działa bez JS |
| Karta okazu | `/collection/{id}` | zalogowany | Paszport drzewka: status biologiczny, zalecenia, podlanie z animowanym paskiem, harmonogram okazu na 7 dni z odhaczaniem, czujnik wilgotności, galeria zdjęć z pełną datą dodania w aplikacji, skan kondycji okazu (nowe lub zapisane zdjęcie, przyczyna, kroki z przyciskiem „Dodaj do harmonogramu”, postępy względem poprzedniego skanu), druk i udostępnianie, usuwanie z kolekcji z potwierdzeniem (404 dla nieznanego id) |
| Studio Formowania | `/studio` | zalogowany | 5 stylów klasycznych z rysunkami drzewek (pień, gałęzie, korony i donica generowane w SVG, numerowane cechy, trójkąt korony do wyłączenia) i 4 gatunki (`?style=`, `?species=`), kalendarz zabiegów dla bieżącego miesiąca, kalkulator grubości drutu, wzorzec kąta owijania, symulator balansu korony z zapisem planu cięcia do harmonogramu |
| Klinika | `/clinic` | zalogowany | Skaner zdjęcia: model wizyjny (Ollama) ogląda drzewko, rozpoznaje przesuszenie, przelanie, szkodniki lub niedobór składników, podaje pewność, co widać i co zrobić (kroki trafiają do harmonogramu); opis „co się ostatnio działo z drzewkiem” poprawia wybór przyczyny; skan można przypisać do okazu z kolekcji (trafia na jego kartę i jest porównywany z poprzednim skanem tego okazu); historia skanów, a po kliknięciu strona skanu (`/clinic/scans/{id}`) z wynikiem, zdjęciem z lupą, ponowną oceną tego samego zdjęcia i usuwaniem; konsultant (model z Ollama znający Twoją kolekcję i ostatni skan, rozmowa zapisuje się na koncie, awaryjnie baza porad); protokół ratunkowy, filtr archiwum |
| Harmonogram | `/schedule` | zalogowany | Tydzień ze strzałkami (animowane przesuwanie, gest przeciągnięcia, „Dziś”), zadania dnia z odhaczaniem, własne zabiegi, okna sezonowe (sekki) |
| Powiadomienia | dzwonek w pasku górnym | zalogowany | Panel powiadomień liczonych z danych (podlewanie, zadania, okna sezonowe), oznaczanie jako przeczytane |
| Motyw jasny/ciemny | przycisk w nagłówku i pasku górnym | wszyscy | Domyślnie zgodny z motywem ustawionym w systemie (zmienia się razem z nim). Przycisk wymusza motyw inny niż systemowy i zapisuje go w ciasteczku `theme` (`POST /theme`); powrót do motywu systemowego usuwa ciasteczko. Zmiana bez przeładowania z JS, formularzem bez JS |

Niezalogowany użytkownik wchodzący na stronę aplikacji jest przekierowywany na `/login`.

Strona startowa ma kalkulator ryzyka utraty turgoru liczony na żywo. Nawigacja, zapisy i filtry działają także bez JavaScriptu (formularze i linki z przeładowaniem); kalkulatory i symulator w Studio oraz kalkulator ryzyka wymagają JS. Każdy `POST` wymaga tokenu CSRF. Przejścia między stronami korzystają z View Transitions API.

## Konto demonstracyjne

| Login | Hasło |
|---|---|
| `admin` | `admin` |

Konta są zapisywane w bazie PostgreSQL (usługa `db`, tabele `users` i `user_species`). Hasła przechowujemy wyłącznie jako skrót Argon2id (`password_hash`), e-mail i login są unikalne bez względu na wielkość liter. Schemat (`docker/db/01-schema.sql`) tworzy się automatycznie przy pierwszym starcie bazy, razem z kontem demonstracyjnym, które należy usunąć przed wdrożeniem produkcyjnym. Dane bazy leżą w wolumenie `pg-data`, więc przetrwają `docker compose down`. Dane drzewek są demonstracyjne (`src/Repository/TreeRepository.php`).

## Uruchomienie

Na nowym urządzeniu potrzebne są tylko Git i Docker (Docker Desktop albo Docker Engine z Compose); PHP, Composer, PostgreSQL i Nginx działają w kontenerach.

```bash
git clone https://github.com/wiksas/wdpai-project.git
cd wdpai-project
docker compose up --build -d server php db mailpit
```

Aplikacja: `http://localhost:8080`, skrzynka z mailami aktywacyjnymi: `http://localhost:8025`. Baza przy pierwszym starcie sama wykonuje `docker/db/*.sql` i zakłada konto demonstracyjne. Podgląd bazy: pgAdmin (`docker compose up -d pgadmin-wdpai`, potem `http://localhost:5050`, `admin@example.com` / `admin`) albo `docker compose exec db psql -U wdpai_user -d wdpai_db`. Usługi są wymienione z nazwy, żeby nie pobierać Ollamy (kilka GB), gdy skaner nie jest potrzebny; samo `docker compose up -d` uruchamia wszystko, także na komputerze bez karty graficznej.

Plik `.env` jest opcjonalny: `docker-compose.yaml` ma wartości domyślne dla wszystkich zmiennych (baza `wdpai_db`, użytkownik `wdpai_user`, hasło `wdpai_password`, porty 8080/5433/8025/5050). Żeby coś zmienić (np. zajęty port albo inne hasło), skopiuj wzór `cp .env.example .env` i popraw wartości; `.env` nie trafia do repozytorium. Hasło bazy działa tylko przy pierwszym starcie pustego wolumenu `pg-data`, później zmiana w `.env` wymaga `docker compose down -v` (usuwa dane). Zmieniając `APP_PORT`, zmień też `APP_URL`, bo inaczej linki w mailach poprowadzą pod stary port.

Autoloader Composera (`vendor/`) jest w repozytorium, więc po sklonowaniu nic nie trzeba instalować. Projekt nie ma zewnętrznych zależności, a autoloader to zwykłe PSR-4, więc nowe klasy w `src/` ładują się bez przebudowy. Po zmianie mapowania w `composer.json` wygeneruj go ponownie i zacommituj:

```bash
docker compose exec php composer dump-autoload
```

Jeśli wolumen bazy już istniał przed dodaniem schematu lub migracji, wykonaj pliki ręcznie w kolejności (są idempotentne):

```bash
docker compose exec -T db sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB"' < docker/db/01-schema.sql
docker compose exec -T db sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB"' < docker/db/02-login-format.sql
docker compose exec -T db sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB"' < docker/db/03-email-verification.sql
docker compose exec -T db sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB"' < docker/db/04-user-state.sql
docker compose exec -T db sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB"' < docker/db/05-sensors.sql
```

`02-login-format.sql` nadaje kontom założonym przed dodaniem pola „Login” login z części adresu e-mail przed `@` (przy kolizji z numerem na końcu) i dodaje regułę formatu loginu. `03-email-verification.sql` dodaje potwierdzanie adresu e-mail; istniejące wtedy konta uznaje za potwierdzone. `04-user-state.sql` tworzy tabelę `user_state`: cały stan konta (własne drzewka, zabiegi, skany, zdjęcia, dziennik, rozmowa z konsultantem) leży w bazie, więc przetrwa restart kontenerów. `05-sensors.sql` tworzy tabele `sensors` i `sensor_readings` (czujniki wilgotności i ich odczyty). Aplikacja sama zakłada tabele z migracji 04 i 05 oraz importuje stare pliki `storage/state/user-{id}.json` przy pierwszym użyciu konta. Zdjęcia zostają w `storage/uploads/`. Wolumen `pg-data` znika dopiero po `docker compose down -v`.

## Czujnik wilgotności

Do okazu można dodać czujnik wilgotności podłoża (karta okazu, sekcja „Czujnik wilgotności”). Urządzenie wysyła surowy odczyt na `POST /api/sensor/readings` z tokenem, a po kalibracji (suche = 0%, mokre = 100%) świeży odczyt zastępuje wilgotność startową okazu i wpływa na status, listę zabiegów i powiadomienia. Co kupić, jak podłączyć ESP32 i szkic z kodem: `docs/czujnik-wilgotnosci.md`. Aplikację sprawdziłem na odczytach wysyłanych `curl`-em, nie na fizycznym czujniku.

## Skaner zdjęć i konsultant (Ollama)

Skaner używa modelu wizyjnego z usługi `ollama` (domyślnie `qwen2.5vl:7b`, zmiana przez `VISION_MODEL` w `.env`). Model odpowiada tylko „tak/nie” na pytania o to, co widać na zdjęciu; przyczynę i zalecenia wyprowadza z tych odpowiedzi reguła w `App\Service\PlantVision`. Wynik jest podpowiedzią, nie diagnozą. Przy pierwszym uruchomieniu pobierz model (ok. 6 GB):

```bash
docker compose up -d ollama
docker compose exec ollama ollama pull qwen2.5vl:7b
docker compose exec ollama ollama pull gemma3:4b
```

Bez dodatkowej konfiguracji Ollama liczy na procesorze, więc działa na każdym komputerze, ale analiza zdjęcia trwa znacznie dłużej (Nginx czeka na nią do 180 s, `fastcgi_read_timeout`). Na komputerze z kartą NVIDIA (sterownik i NVIDIA Container Toolkit; Docker Desktop na Windows ma go w WSL 2) dopisz do `.env`:

```bash
COMPOSE_PATH_SEPARATOR=:
COMPOSE_FILE=docker-compose.yaml:docker-compose.gpu.yaml
```

Wtedy każde `docker compose …` dołącza `docker-compose.gpu.yaml`, który rezerwuje GPU dla Ollamy (analiza trwa kilka sekund). Bez karty NVIDIA tych linii nie dodawaj: start kończy się błędem `nvidia-container-cli: initialization error`. Gdy Ollama nie działa lub brakuje modelu, skaner pokazuje samą analizę barwy z przeglądarki.

Konsultant w Klinice rozmawia innym modelem (`CHAT_MODEL`, domyślnie `gemma3:4b`, ok. 3 GB): zna kolekcję i ostatni skan użytkownika, pamięta rozmowę (zapis na koncie) i opiera się na poradach z `ClinicKnowledge`. Na karcie 8 GB oba modele nie mieszczą się naraz, więc przy przełączaniu między zdjęciem a rozmową pierwsza odpowiedź trwa kilkanaście sekund dłużej.

## Poczta i potwierdzanie konta

Po rejestracji konto jest nieaktywne, dopóki użytkownik nie kliknie linku z e-maila; logowanie przed potwierdzeniem pokazuje komunikat i przycisk ponownej wysyłki. Link działa 24 godziny i jest jednorazowy, a w bazie (`email_verifications`) leży tylko skrót SHA-256 tokenu. Ponowna wysyłka unieważnia poprzedni link i nie zdradza, czy konto istnieje.

Wiadomości wysyła `App\Service\Mailer` przez SMTP. W Dockerze odbiera je Mailpit: nic nie wychodzi na zewnątrz, a skrzynka testowa jest pod `http://localhost:8025`. Konfiguracja w `.env` / `docker-compose.yaml`: `APP_URL` (adres w linkach), `MAIL_HOST`, `MAIL_PORT`, `MAIL_FROM`. Prawdziwy serwer pocztowy wymagałby dodania TLS i uwierzytelniania SMTP.
