# KOMOREBI: cyfrowy dziennik opiekuna bonsai

Aplikacja webowa (czysty PHP 8.3, MVC bez frameworka) pomagająca opiekunom bonsai monitorować kondycję drzewek, planować zabiegi formowania i reagować na objawy chorób. Kierunek wizualny i powody decyzji projektowych opisuje `DESIGN.md`.

## Funkcje

| Obszar | Trasa | Dostęp | Co robi |
|---|---|---|---|
| Strona startowa | `/` | publiczny | Przewodnik: szybka ocena ryzyka, najczęstsze błędy, protokół pierwszej pomocy, style formowania |
| Logowanie | `/login` | gość | Logowanie loginem lub e-mailem, komunikaty o błędach |
| Rejestracja | `/register` | gość | Założenie konta hodowcy (walidacja, hasło hashowane), automatyczne zalogowanie |
| Wylogowanie | `POST /logout` | zalogowany | Menu konta w nagłówku |
| Moja Kolekcja | `/collection` | zalogowany | Panel kolekcji: filtry (iglaste/liściaste/do podlania) i wyszukiwarka na żywo, podlewanie, dziennik obserwacji, nadchodzące zabiegi |
| Karta okazu | `/collection/{id}` | zalogowany | Paszport drzewka: status biologiczny, zalecenia, podlanie z animowanym paskiem, galeria zdjęć (upload ze zmniejszaniem), druk i udostępnianie (404 dla nieznanego id) |
| Studio Formowania | `/studio` | zalogowany | 5 stylów klasycznych i 4 gatunki (`?style=`, `?species=`), kalendarz zabiegów dla bieżącego miesiąca, kalkulator grubości drutu, wzorzec kąta owijania, symulator balansu korony z zapisem planu cięcia do harmonogramu |
| Klinika | `/clinic` | zalogowany | Wgrywanie zdjęcia z animacją skanowania i analizą barwy w przeglądarce, historia skanów, konsultant odpowiadający z bazy wiedzy (dopasowanie słów kluczowych, nie model językowy), protokół ratunkowy → harmonogram, filtr archiwum |
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

```bash
docker compose up --build -d server php db
```

Aplikacja: `http://localhost:8080` (port z `APP_PORT` w `.env`). Dane dostępowe bazy (`POSTGRES_DB`, `POSTGRES_USER`, `POSTGRES_PASSWORD`) pochodzą z `.env` (wzór w `.env.example`). Podgląd bazy: pgAdmin pod `http://localhost:5050` albo `docker compose exec db psql -U wdpai_user -d wdpai_db`.

Jeśli wolumen bazy już istniał przed dodaniem schematu lub migracji, wykonaj pliki ręcznie w kolejności (są idempotentne):

```bash
docker compose exec -T db sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB"' < docker/db/01-schema.sql
docker compose exec -T db sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB"' < docker/db/02-login-format.sql
```

`02-login-format.sql` nadaje kontom założonym przed dodaniem pola „Login” login z części adresu e-mail przed `@` (przy kolizji z numerem na końcu) i dodaje regułę formatu loginu.
