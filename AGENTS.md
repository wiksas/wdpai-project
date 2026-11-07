# AGENTS.md

Ten plik jest przewodnikiem dla agentow AI pracujacych w tym repozytorium. Przed wprowadzeniem zmian przeczytaj go razem z `README.md` i sprawdz aktualny stan kodu. Przy pracy nad wygladem przeczytaj tez `DESIGN.md` (kierunek wizualny, paleta, typografia, powody decyzji), a potem zasady antislop jako filtr. Nie zakladaj, ze opisana funkcja istnieje, dopoki nie potwierdzisz tego w plikach projektu.

## Cel projektu

To edukacyjna aplikacja webowa napisana w czystym PHP 8.3. Korzysta z lekkiej, samodzielnie zaimplementowanej architektury MVC, bez frameworka PHP. Aplikacja dziala w kontenerach Nginx i PHP-FPM uruchamianych przez Docker Compose.

Aktualnie aplikacja obsluguje:

- strone glowna pod `/` (publiczna),
- logowanie, rejestracje i wylogowanie pod `/login`, `/register` i `/logout` (konto demonstracyjne `admin` / `admin`),
- po zalogowaniu: panel kolekcji pod `/collection`, karte okazu pod `/collection/{id}` (dane z `App\Repository\TreeRepository`), studio formowania pod `/studio`, klinike pod `/clinic` i harmonogram pod `/schedule`; gosc jest przekierowywany na `/login`,
- motyw jasny/ciemny (przelacznik w naglowku i pasku gornym, `POST /theme`),
- odpowiedz 404 dla nieznanych tras i nieistniejacych okazow.

## Stack technologiczny

- PHP 8.3 z `declare(strict_types=1)`
- Composer i autoloading PSR-4
- Nginx jako serwer HTTP
- PHP-FPM do wykonywania kodu PHP
- Docker oraz Docker Compose
- HTML i CSS bez frameworka frontendowego; czysty JavaScript (bez bundlera) jako progresywne ulepszenie

## Struktura katalogow

```text
.
├── docker/
│   ├── nginx/                 # obraz i konfiguracja Nginx
│   └── php/                   # obraz PHP-FPM i rozszerzenia PHP
├── public/                    # jedyny publicznie dostepny katalog
│   ├── css/style.css          # globalne style: tokeny, komponenty, powloka aplikacji
│   ├── css/pages/             # style pojedynczych stron (selektory z prefiksem strony, np. `.home-`)
│   ├── images/                # zdjecia z projektu Figma
│   ├── js/                    # czysty JavaScript: app.js (wspolny) + skrypty stron
│   ├── index.php              # front controller i definicje tras
│   └── views/                 # szablony HTML renderowane przez kontrolery
│       └── partials/          # wspolne fragmenty: head, naglowek, stopka, powloka aplikacji
├── src/
│   ├── controllers/           # kontrolery aplikacji
│   ├── Repository/            # repozytoria: dane demonstracyjne, konta, stan uzytkownika (bez bazy danych)
│   ├── Service/               # serwisy: Session, Csrf, Flash, AuthService, MediaStorage, Collection/Schedule/Notification/Clinic/Studio…
│   ├── Support/               # klasy pomocnicze bez zaleznosci (PolishDate)
│   └── Routing.php            # rejestracja i obsluga tras
├── storage/                   # dane uruchomieniowe poza `public/` (users.json, state/, uploads/ — w .gitignore)
├── DESIGN.md                  # kierunek wizualny i powody decyzji projektowych
├── composer.json              # konfiguracja autoloadingu
└── docker-compose.yaml        # uslugi `server` i `php`
```

Nie umieszczaj logiki aplikacji ani danych wrazliwych w `public/`. Ten katalog jest document rootem Nginx i sluzy tylko jako punkt wejscia oraz miejsce na widoki i zasoby publiczne.

## Architektura MVC

Projekt stosuje uproszczony przeplyw MVC:

```text
przegladarka
    -> Nginx
    -> public/index.php
    -> Routing
    -> Controller
    -> Model / serwis (gdy zostanie dodany)
    -> View
    -> odpowiedz HTML
```

### Front Controller

`public/index.php` jest jedynym punktem wejscia aplikacji. Laduje autoloader Composera, startuje sesje, rejestruje trasy, przekazuje sciezke i metode HTTP do routera oraz uruchamia widok 404, gdy zadna trasa nie pasuje.

Nowe trasy dodawaj w tym pliku w postaci (nazwa kontrolera bez przestrzeni nazw, sciezka bez wiodacego `/`):

```php
Routing::get('example', 'ExampleController', 'index');
Routing::post('example', 'ExampleController', 'store');
```

### Router

`src/Routing.php` laczy metode HTTP i sciezke URL z publiczna metoda kontrolera (`App\Controllers\<Nazwa>`). Parametry zapisane jako `{name}` sa dopasowywane do znakow `a-zA-Z0-9_.-` i przekazywane do akcji kontrolera jako `string` — akcja musi sama zwalidowac typ (np. `ctype_digit`).

Przyklad: `collection/{id}` wywoluje `CollectionController::show(string $id)`.

Router obsluguje metody GET (`Routing::get()`) i POST (`Routing::post()`); trasa jest dopasowywana tylko dla swojej metody, a brak dopasowania daje 404. Nie dodawaj logiki biznesowej do routera.

### Controller

Kontrolery znajduja sie w `src/controllers/`, uzywaja przestrzeni nazw `App\Controllers` i dziedzicza po `AppController`. Ich zadaniem jest:

- odebranie i walidacja danych wejsciowych,
- wywolanie modelu lub serwisu,
- przygotowanie danych dla widoku,
- wybranie szablonu przez `$this->render()`.

Kontroler powinien byc cienki. Nie umieszczaj w nim rozbudowanych zapytan do bazy, HTML ani stylow.

### Model

Pelna warstwa Model/baza danych nie jest jeszcze zaimplementowana. Dane okazow (`App\Repository\TreeRepository`) sa demonstracyjne i zapisane w kodzie, a konta uzytkownikow (`App\Repository\UserRepository`) leza w pliku `storage/users.json`. Nie udawaj, ze dane widoczne w widokach pochodza z bazy danych. Gdy funkcja wymaga trwalego przechowywania danych, wydziel klasy modelu/repozytorium zamiast dodawac zapytania SQL do kontrolera.

Dla nowych klas zgodnych z mapowaniem `App\` w Composerze preferuj katalogi i nazwy klas o zgodnej wielkosci liter, np.:

```text
src/Model/Project.php          -> App\Model\Project
src/Repository/ProjectRepository.php -> App\Repository\ProjectRepository
```

Po dodaniu lub przeniesieniu klas odswiez autoloader poleceniem `composer dump-autoload`.

### View

Widoki znajduja sie w `public/views/` i maja nazwy `*.html.php`. `AppController::render()` przekazuje do nich dane jako zmienne przy uzyciu `extract(..., EXTR_SKIP)`.

Widoki odpowiadaja za prezentacje. Dozwolone sa w nich proste instrukcje warunkowe i petle, ale nie logika biznesowa, dostep do bazy ani obsluga routingu.

Kazda wartosc pochodzaca od uzytkownika lub z bazy musi byc escapowana w kontekscie HTML:

```php
<?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?>
```

## Uwierzytelnianie

- Sesja jest startowana w `public/index.php` (ciasteczko `HttpOnly`, `SameSite=Lax`). Do `$_SESSION` siega wylacznie `App\Service\Session` (uzywana przez `AuthService`, `Csrf`, `Flash`); kontrolery i widoki korzystaja z tych serwisow.
- `AppController::render()` dodaje do kazdego widoku zmienna `$currentUser` (`null` dla goscia; tablica `id`, `name`, `email`, `role`, `initials`, `label` — bez hasha hasla).
- Strony aplikacji (`/collection`, `/collection/{id}`, `/studio`, `/clinic`, `/schedule`) zaczynaja akcje od `$this->requireLogin()`, ktore przekierowuje goscia na `/login`. Nowy kontroler aplikacji musi robic to samo.
- Logowanie, rejestracja i wylogowanie to `AuthController`: `GET/POST /login`, `GET/POST /register`, `POST /logout`. Walidacja wejscia zyje w kontrolerze, hasla zapisujemy tylko przez `password_hash()`.
- Konta obsluguje `App\Repository\UserRepository`: konto demonstracyjne `admin` / `admin` jest w kodzie, a konta z rejestracji laduja w `storage/users.json` (poza `public/`, w `.gitignore`). To rozwiazanie tymczasowe — po dodaniu bazy danych podmien implementacje repozytorium na PDO bez zmiany kontrolerow. Konto demonstracyjne usun przed wdrozeniem produkcyjnym.
- Menu konta (`partials/user-menu.html.php`) i przyciski „Zaloguj sie / Zarejestruj sie” w naglowku zalezą od `$currentUser`; nie wstawiaj do widokow zdjec ani danych „na sztywno” zalogowanego uzytkownika.

## Zabezpieczenia formularzy i stan uzytkownika

- **CSRF:** kazdy `POST` (w tym `fetch`) wymaga tokenu. Formularze HTML wstawiaja `<?php include __DIR__ . '/partials/csrf.html.php'; ?>` (pole `_csrf`), a `fetch` wysyla naglowek `X-CSRF-Token` (token jest w `<meta name="csrf-token">` z `partials/head.html.php`). Kontroler na poczatku akcji `POST` wywoluje `$this->requireCsrf()` (brak/zly token = 419). Nowa akcja `POST` bez tego wywolania jest bledem.
- **Sesja:** do `$_SESSION` siega wylacznie `App\Service\Session` (oraz przez nia `AuthService`, `Csrf`, `Flash`). Komunikaty jednorazowe: `Flash` (`$this->flash()`), wyswietlane jako toasty (`partials/foot.html.php`).
- **Odpowiedzi dla JS i bez JS:** akcje zapisujace koncza sie `$this->respond($message, $redirect, $extra, $ok, $status)` — dla zadan `fetch` (`X-Requested-With: fetch`) zwraca JSON `{ok, message, …}`, w pozostalych przypadkach ustawia flash i przekierowuje (z ochrona przed open redirect). Dzieki temu kazda funkcja dziala takze bez JavaScriptu.
- **Stan uzytkownika** (odczytane powiadomienia, odhaczone zadania, wlasne zabiegi, podlewanie, dziennik, zdjecia, skany) zapisuje `App\Repository\UserStateRepository` w `storage/state/user-{id}.json` (blokada `flock`). Nowe pole dodaj do `UserStateRepository::DEFAULTS`.
- **Zdjecia:** wgrywa je `App\Service\MediaStorage` (kontrola MIME przez `finfo`, limit 2 MB i 6000 px, losowe nazwy) do `storage/uploads/{userId}/`. Pliki sa serwowane tylko wlascicielowi przez `GET /media/{name}` (`MediaController`); `storage/` nie jest publiczne.
- **Serwisy domenowe:** `CollectionService` (stan drzewek po zabiegach), `ScheduleService` (zadania generowane regulami + wlasne), `NotificationService` (powiadomienia liczone z danych, ID zawieraja date/status), `ClinicKnowledge` (baza wiedzy konsultanta — dopasowanie slow kluczowych, nie model jezykowy), `StudioCatalog` (style i kalendarze gatunkow), `Support\PolishDate` (polskie daty).

## JavaScript

Czysty JS bez bundlera i bibliotek, w `public/js/`, ladowany `defer` na koncu widoku (`<script src="<?= $asset('/js/x.js') ?>" defer>`). Zasady:

- **Progresywne ulepszenie:** nawigacja, zapisy danych i filtry musza dzialac bez JS (linki, formularze, `?query`); JS tylko przechwytuje je, by zrobic to bez przeladowania. Wyjatek: czysto klienckie kalkulatory i symulatory (Studio, kalkulator ryzyka na stronie glownej) pokazuja bez JS wartosci domyslne.
- `app.js` (ladowany wszedzie przez `partials/foot.html.php`) udostepnia `window.Komorebi`: `request`, `post`, `fetchDocument`, `swap`, `play`, `toast`, `sleep`, `reduceMotion`, `resizeImage`, panel powiadomien. Skrypty stron (`schedule.js`, `collection.js`, `specimen.js`, `clinic.js`, `studio.js`, `home.js`) z niego korzystaja i nie powtarzaja logiki.
- Szanuj `prefers-reduced-motion` (`K.reduceMotion`); nie uzywaj `confirm`/`alert` (blokuja strone) — uzywaj toastow.
- Dane z serwera wstawiaj przez `textContent`, nie `innerHTML` (wyjatek: stale, wlasne stringi HTML).

## Konwencje implementacji

- Zachowuj `declare(strict_types=1)` we wszystkich nowych plikach PHP.
- Uzywaj deklaracji typow parametrow, zwracanych wartosci i wlasciwosci.
- Nazwy klas zapisuj w `PascalCase`, a metod i zmiennych w `camelCase`.
- Nazwy techniczne w kodzie zapisuj po angielsku; tekst interfejsu moze byc po polsku.
- Jeden kontroler powinien obslugiwac jeden spojny obszar aplikacji.
- Dane przekazuj do widoku jawnie w tablicy drugiego argumentu `render()`.
- Nie tworz globalnego stanu i nie uzywaj `$_GET`, `$_POST` ani `$_SERVER` poza warstwa wejscia, jezeli dane mozna przekazac jako argument.
- Nie dodawaj frameworka ani duzej biblioteki bez wyraznej potrzeby i zgody uzytkownika.
- Sekrety umieszczaj w `.env`; nigdy nie commituj hasel, tokenow ani danych dostepowych.
- Nie edytuj `vendor/` recznie.
- Zachowuj zakres zadania i nie zmieniaj niezwiązanych plikow.

## Dodawanie nowej funkcji

Typowy porzadek prac:

1. Dodaj trase w `public/index.php`.
2. Dodaj albo rozszerz kontroler w `src/controllers/`.
3. Dodaj model, repozytorium lub serwis, jesli funkcja zawiera logike biznesowa albo dostep do danych.
4. Dodaj widok w `public/views/` i przekaz do niego tylko potrzebne dane.
5. Dodaj lub zmien style: wspolne komponenty i tokeny w `public/css/style.css`, style konkretnej strony w `public/css/pages/<strona>.css` (linkowane w widoku po `partials/head.html.php`). Kolory tylko przez tokeny (patrz „Motyw jasny i ciemny”) i sprawdz wynik w obu motywach.
6. Sprawdz poprawna trase, dane wejsciowe, HTML oraz zachowanie dla bledow i 404.

### Animacje sterowane przewijaniem

Animacje przy przewijaniu (Scroll-driven Animations) sa w czystym CSS — bez JavaScriptu. Wspolne klatki kluczowe i tokeny sa w sekcji 17 `public/css/style.css`: wejscia `--sd-reveal` (z dolu), `--sd-reveal-left` / `--sd-reveal-right` (z boku), `--sd-pop` (ikony), `--sd-clip` (ramka zdjecia), `--sd-grow` (paski, os X), `--sd-draw` (linie, os Y), wyjscie `--sd-exit`, `--sd-lag` (tresc zostaje w tyle i blednie), `--sd-turn` oraz zakresy `--sd-range`, `--sd-range-2…4`. **Parallax:** `--sd-layer` przesuwa warstwe od (`--sd-x0`, `--sd-y0`) do (`--sd-x1`, `--sd-y1`) — warstwy tla (pseudo-elementy sekcji z `overflow: clip`) dostaja rozne predkosci i kierunki, a dla sekcji nizej na stronie uzywaj nazwanej osi (`view-timeline: --nazwa block`) i `animation-range: cover`; `--sd-pan` przesuwa zdjecie wewnatrz ramki z `overflow: clip`. Nie uzywaj `overflow: hidden` na przodkach animowanych elementow — tworzy kontener przewijania. Globalnie dzialaja tez pasek postepu, cien naglowka, karty, stopka i `.progress__bar`. Wsuwanie z prawej wlaczaj tylko w ukladzie dwukolumnowym (`@media (min-width: 1025px)`), zeby nie wywolac poziomego przewijania na mobile. Elementy konkretnej strony dopisuj na koncu jej pliku `public/css/pages/<strona>.css`. Zawsze ustawiaj pelna trojke `animation` + `animation-timeline` + `animation-range` (skrot `animation` zeruje os czasu):

```css
@media (prefers-reduced-motion: no-preference) {
    @supports (animation-timeline: view()) {
        .x-card {
            animation: var(--sd-reveal);
            animation-timeline: view();
            animation-range: var(--sd-range);
        }
    }
}
```

Zawsze zachowuj obie ochrony (`prefers-reduced-motion` i `@supports`), zeby tresc byla widoczna w przegladarkach bez obslugi. Nie animuj elementow, ktore maja wlasny `transform` (np. stan `:active`), bo koncowa klatka `transform: none` go nadpisze.

Odslanianie (`--sd-range`) trwa na odcinku ok. 40% wysokosci okna i konczy sie, gdy gorna krawedz elementu dojedzie do ~60% od gory (`cover 0% cover 40vh`) — dzieki temu ruch jest widoczny, a nie ukryty tuz przy dolnej krawedzi. Elementy na samym koncu strony nie maja jak wjechac tak wysoko, wiec: tresc aplikacji ma zapas przewijania (`.app-content` `padding-bottom` w sekcji 17), stopka uzywa bezpiecznego zakresu `entry 0% entry 100%`, a po zmianie ukladu sprawdz w przegladarce (przewin na sam dol), czy zaden element nie zostal polprzezroczysty lub z niedopelnionym paskiem, oraz czy `animation.timeline.source` kazdej animacji to okno (`document.scrollingElement`) — `overflow: hidden/auto` na przodku przejmuje os czasu.

Przejscia miedzy stronami to View Transitions API w trybie MPA (sekcja 20 `style.css`: `@view-transition { navigation: auto; }`). Stale elementy ukladu (`.app-sidebar`, `.app-topbar`, `.bottom-nav`, `.site-header`) maja `view-transition-name` i zostaja w miejscu, a tresc plynnie zanika i wjezdza. Nowy staly element ukladu dostaje wlasna nazwe; przegladarki bez wsparcia po prostu pomijaja blok.

### Motyw jasny i ciemny

- Tokeny kolorow w sekcji 1 `public/css/style.css` maja postac `light-dark(jasny, ciemny)`. `:root` ma `color-scheme: light dark`, wiec bez wyboru obowiazuje motyw systemu; `<html data-theme="light|dark">` wymusza motyw. Nie wpisuj literalow kolorow w CSS stron (wyjatek: bialy tekst i przyciemnienie na zdjeciu); nowy kolor dodaj jako token z obiema wartosciami.
- `--c-primary` w ciemnym motywie staje sie jasna szalwia (z `--c-on-primary` ciemnym), wiec ciemna sekcja, ktora ma zostac ciemna w obu motywach, uzywa `--c-band`, `--c-band-raised`, `--c-on-band`, `--c-on-band-variant`. Tekst na terakocie: `--c-on-tertiary`.
- Wybor zapisuje `ThemeController::update()` (`POST /theme`, pole `theme`: `light`/`dark`/`system`, CSRF): `light`/`dark` trafia do ciasteczka `theme` na rok, `system` usuwa ciasteczko i strona znow podaza za ustawieniem komputera. `AppController::render()` przekazuje do widokow `$theme` (`light`/`dark`/`system`) i `$currentPath`; kazdy widok ustawia `<html lang="pl" data-theme="...">`, a `partials/head.html.php` meta `color-scheme`.
- Przelacznik to `partials/theme-toggle.html.php` (formularz z `_back`, dziala bez JS). `app.js` przechwytuje go, liczy motyw widoczny teraz (takze systemowy) i zmienia `data-theme` bez przeladowania (przenikanie View Transitions poza `prefers-reduced-motion`). Gdy nowy motyw jest taki sam jak systemowy, wysyla `system` zamiast go zapisywac, wiec drugie klikniecie przywraca sledzenie systemu (takze zmian na zywo). Nowa strona z wlasnym ukladem (jak logowanie) musi zawierac ten partial.

## Uruchamianie projektu

```bash
docker compose up --build -d
```

Aplikacja jest domyslnie dostepna pod `http://localhost:8081`. Port mozna zmienic przez zmienna `APP_PORT`.

Przydatne polecenia:

```bash
docker compose ps
docker compose logs -f
docker compose exec php php -l src/Routing.php
docker compose exec php composer dump-autoload
docker compose down
```

Jesli katalog `vendor/` nie istnieje, zainstaluj zaleznosci/autoloader poleceniem:

```bash
docker compose run --rm php composer install
```

## Weryfikacja zmian

W projekcie nie ma obecnie skonfigurowanego zestawu testow automatycznych ani narzedzia do formatowania. Po zmianie:

- uruchom `php -l` dla kazdego zmienionego pliku PHP,
- sprawdz `composer dump-autoload`, jezeli zmieniales klasy lub namespace,
- uruchom kontenery i sprawdz zmienione endpointy,
- sprawdz przypadek poprawny, bledne dane oraz nieistniejaca trase,
- upewnij sie, ze logi `docker compose logs` nie zawieraja nowych bledow,
- nie twierdz, ze testy przeszly, jezeli ich faktycznie nie uruchomiono.

## Zasady dla agentow AI

- Najpierw przeczytaj pliki bezposrednio zwiazane z zadaniem i potwierdz aktualna implementacje.
- Przed edycja sprawdz `git status`; zachowaj zmiany uzytkownika i nie cofaj ich.
- Wprowadzaj najmniejsza spojna zmiane realizujaca wymaganie.
- Utrzymuj rozdzial odpowiedzialnosci MVC.
- Nie rozszerzaj zakresu zadania o refaktoryzacje, migracje lub nowe zaleznosci bez uzasadnienia.
- Jesli wymaganie jest niejednoznaczne, wybierz rozwiazanie zgodne z istniejacymi wzorcami; pytaj tylko wtedy, gdy wybor istotnie zmieni zachowanie aplikacji.
- Po zakonczeniu opisz zmienione pliki oraz wykonana weryfikacje.
