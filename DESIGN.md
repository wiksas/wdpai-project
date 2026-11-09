# DESIGN.md

Kierunek wizualny KOMOREBI. Przed praca nad wygladem przeczytaj ten plik, a potem zasady antislop (filtr). Kazda decyzja ponizej ma jednozdaniowy powod.

## Design Read

Aplikacja do pielegnacji bonsai (publiczna strona glowna + narzedzia po zalogowaniu) dla polskich hobbystow, w stylu papier washi i tusz: lesna zielen jako tusz, jeden akcent terakoty.

Dial: ENERGY 2 / RHYTHM 2 / MOTION 2. Strona glowna ma MOTION 3 (parallax warstw tla zamowiony przez wlasciciela projektu).

## Paleta (role, nie dekoracja)

| Rola | Jasny | Ciemny | Powod |
| --- | --- | --- | --- |
| Tlo `--c-surface` | `#fbf9f4` papier washi | `#121713` wieczorny ogrod | cieply papier zamiast sterylnej bieli; noc w zieleni, nie czern |
| Tusz `--c-primary` | `#072417` | `#b3d1bf` | tekst, naglowki, glowny przycisk w kolorze igiel sosny |
| Mech `--c-secondary` | `#486554` | `#a6c8b3` | kicker, stan zdrowy, drugorzedne informacje |
| Akcent `--c-tertiary` | `#944929` | `#f2a886` | terakota donicy: tylko glowna akcja, ostrzezenie, stan pilny |
| Pas `--c-band` | `#072417` | `#18231c` | ciemna sekcja (protokol ratunkowy), ciemna w obu motywach |

- Najwyzej 2 kolory rdzenia (tusz, mech) + 1 akcent (terakota); neutralne nie licza sie do palety.
- Akcent pojawia sie w jednym, dwoch miejscach na ekranie. Kicker, ikona i ramka nie dostaja terakoty "dla ozdoby".
- Kolory tylko przez tokeny z sekcji 1 `public/css/style.css` (kazdy ma `light-dark()`). Literal koloru wolno uzyc tylko dla tekstu na zdjeciu (`#fff` na przyciemnieniu).

## Typografia

- Naglowki: Noto Serif (prawdziwa kursywa i polskie znaki) z Zen Old Mincho jako zrodlem kanji. Powod: kontrast kreski jak w japonskim Mincho, czyli pedzel na papierze.
- Tekst: Zen Kaku Gothic New. Powod: japonski grotesk rysowany dla kanji i lacinki z polskimi znakami, wiec 木漏れ日 i polski tekst maja jeden glos.
- Kicker (`.eyebrow`): zwykla wielkosc liter, 14 px, kolor mchu. Bez wersalikow z rozstrzeleniem.

## Motyw tozsamosci

Termin japonski obok polskiego: romaji kursywa szeryfowa + kanji w Mincho (`.term`), np. `Moyogi 模様木`, `Shunbun 春分`. Uzywany tam, gdzie tresc naprawde ma japonska nazwe (style formowania, pory sekki, znak marki), nigdy jako ozdobnik bez znaczenia.

## Znak marki

- Znak laczy nazwe z drzewkiem: 木漏れ日 (komorebi) to slonce przeswitujace miedzy liscmi, wiec na bonsai w stylu moyogi male slonce zaglada miedzy gorna i prawa korone. Promienie wychodza ze slonca, ale widac je tylko w przeswitach (korony i pien je zaslaniaja), a czesci koron najblizej slonca jasnieja jak liscie pod swiatlo. Drzewko: zwezajacy sie pien w ksztalcie litery S (jedna bryla), trzy splaszczone korony w trojkacie nierownobocznym (szczyt, prawa, nizsza lewa), niska donica. Bez cieniowania, blyskow, galazek i faktury: kazda czesc ma jedna barwe (faktury koron, luki i linie, odrzucone, bo zasmiecaly sylwetke).
- Barwy: tokeny rysunkow (`--c-bark`, `--c-needle`, `--c-pot`) plus dwa tokeny znaku: `--c-sun` (slonce, promienie z przezroczystoscia 0.3) i `--c-needle-sunlit` (liscie pod swiatlo). To ten sam wyjatek od palety co ilustracje: drzewko i swiatlo maja wygladac naturalnie, a zolc slonca nie pojawia sie poza znakiem. Kafelek ma papierowe tlo (`--c-surface-lowest`) z cienka linia, wiec zielen i terakota czytaja sie w obu motywach. Panel logowania jest ciemny w obu motywach, dlatego jego kafelek ma `color-scheme: light`.
- Zrodlo: `public/views/components/logo-mark.html.php` (SVG wpisany raz, wstawiany `include` w naglowkach, panelu bocznym, stopce i stronach logowania; kazdy egzemplarz dostaje losowe `id` przyciecia koron, bo znak bywa kilka razy na stronie), reguly `.logo-*` w sekcji 11 `style.css`. Ikona karty przegladarki `public/favicon.svg` ma te same ksztalty ze stalymi barwami wersji jasnej (nie czyta tokenow), wiec zmieniajac znak popraw oba pliki. Wersja do druku i prezentacji: `public/images/komorebi-logo.jpg` (1024 x 1024 px, tlo papieru `#fbf9f4`, eksport `favicon.svg`); po zmianie znaku wyeksportuj ja ponownie.
- Czytelnosc: znak zajmuje caly kafelek 40 px z marginesem ok. 3 px; korony i donica sa duzymi, plaskimi bryłami, bo cienkie detale znikaja w tej skali (przy 16 px promienie zanikaja, a slonce zostaje jako zolta kropka przy koronach). Nie dodawaj mu cienia, poswiaty ani gradientu.

## Ksztalt i glebia

- Promienie: 2 px przyciski i pola (ciete jak papier), 12 px karty i panele, pelne zaokraglenie tylko dla awatarow i przelacznikow. Powod: roznica promienia odroznia akcje od pojemnika.
- Karty leza plasko: tlo `--c-surface-lowest` + linia `--c-divider`. Cien (`--shadow-md/lg`) tylko dla warstw nad trescia: menu konta, panel powiadomien, toast, okno dialogowe.
- Rozmycie (`backdrop-filter`) tylko na przyklejonym naglowku strony publicznej.

## Ruch

- Odslanianie przy przewijaniu (sekcja 17 `style.css`) prowadzi wzrok do kolejnej sekcji; parallax tylko na stronie glownej.
- Bez petli: zadnych pulsujacych kropek ani animacji bez wyzwalacza od uzytkownika.
- Zmiana motywu: przenikanie przez View Transitions, wylaczone przy `prefers-reduced-motion`.

## Tresc

- Liczby tylko z danych aplikacji (kolekcja, harmonogram, skany). Bez wymyslonych statystyk, opinii, liczby uzytkownikow, numerow telefonu i nazw "silnikow".
- Konsultant w klinice dopasowuje slowa kluczowe w bazie wiedzy (`ClinicKnowledge`), wiec nie nazywamy go AI.
- Bez myslnika em (—) w tekstach interfejsu; uzywaj przecinka, dwukropka lub nawiasu.
- Przyciski mowia, co sie stanie ("Zapisz zabieg", "Sprawdz ryzyko"), nie "Dowiedz sie wiecej".

## Motyw jasny i ciemny

- Domyslnie motyw systemu (`prefers-color-scheme`), zmieniany na zywo razem z ustawieniem komputera. Przelacznik (`partials/theme-toggle.html.php`) wymusza motyw inny niz systemowy i zapisuje go w ciasteczku `theme` przez `POST /theme`; wybranie z powrotem motywu systemowego usuwa ciasteczko. Dziala bez JS, a `app.js` zmienia motyw bez przeladowania.
- Kazdy nowy styl sprawdz w obu motywach (kontrast WCAG AA: 4.5:1 tekst, 3:1 duzy tekst i ikony).
