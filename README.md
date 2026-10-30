# KOMOREBI — cyfrowy dziennik opiekuna bonsai

Aplikacja webowa (czysty PHP 8.3, MVC bez frameworka) pomagająca opiekunom bonsai monitorować kondycję drzewek, planować zabiegi formowania i reagować na objawy chorób. Interfejs odwzorowuje projekt Figma „projekt Wdpai”.

## Funkcje

| Obszar | Trasa | Dostęp | Co robi |
|---|---|---|---|
| Strona startowa | `/` | publiczny | Przewodnik: szybka ocena ryzyka, najczęstsze błędy, protokół pierwszej pomocy, style formowania |
| Logowanie | `/login` | gość | Logowanie loginem lub e-mailem, komunikaty o błędach |
| Rejestracja | `/register` | gość | Założenie konta hodowcy (walidacja, hasło hashowane), automatyczne zalogowanie |
| Wylogowanie | `POST /logout` | zalogowany | Menu konta w nagłówku |
| Moja Kolekcja | `/collection` | zalogowany | Panel kolekcji: statystyki, karty drzewek, mikroklimat, kalendarz prac, dziennik obserwacji |
| Karta okazu | `/collection/{id}` | zalogowany | Paszport drzewka: status biologiczny, zalecenia kuratora, oś zabiegów (404 dla nieznanego id) |
| Studio Formowania | `/studio` | zalogowany | Kanony stylów, kalendarz cięcia, zasady drutowania, symulator balansu korony |
| Klinika AI | `/clinic` | zalogowany | Skaner okazu i wynik diagnostyki fitopatologicznej |
| Harmonogram | `/schedule` | zalogowany | Tydzień zabiegów, zadania na dziś, nadchodzące okna sezonowe |

Niezalogowany użytkownik wchodzący na stronę aplikacji jest przekierowywany na `/login`.

## Konto demonstracyjne

| Login | Hasło |
|---|---|
| `admin` | `admin` |

Konta założone w formularzu rejestracji są zapisywane w `storage/users.json` (plik poza `public/`, ignorowany przez git). Dane drzewek są demonstracyjne (`src/Repository/TreeRepository.php`). Aplikacja nie ma jeszcze warstwy bazy danych, a konto demonstracyjne należy usunąć przed wdrożeniem produkcyjnym.

## Uruchomienie

```bash
docker compose up --build -d server php
```

Aplikacja: `http://localhost:8080` (port z `APP_PORT` w `.env`).
