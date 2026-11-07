# Historyjki użytkownika

Format: *Jako [rola] chcę [cel], aby [korzyść].* Każda historyjka ma kryteria akceptacji i odnośnik do wymagań z [specification.md](specification.md).

Role:
- **Gość**: osoba niezalogowana.
- **Hodowca**: zalogowany użytkownik z potwierdzonym adresem e-mail.
- **Administrator**: konto demonstracyjne `admin` (w aplikacji ma te same uprawnienia co hodowca, różni się etykietą).

## 1. Konto i dostęp

### US-01 Rejestracja konta
Jako gość chcę założyć konto z własnym loginem, aby prowadzić własną kolekcję drzewek.

Kryteria akceptacji:
- Formularz zawiera: login, imię, adres e-mail, hasło, powtórzenie hasła, poziom doświadczenia, gatunki (opcjonalnie) i zgodę.
- Login: 3–30 znaków, litery bez polskich znaków, cyfry, `.`, `_`, `-`, na początku litera; zapisywany małymi literami; unikalny bez względu na wielkość liter; nie może być zarezerwowany (`admin`, `root` itd.).
- Adres e-mail jest poprawny i unikalny bez względu na wielkość liter.
- Hasło ma 8–128 znaków, a oba pola hasła są identyczne.
- Przy błędach formularz pokazuje listę błędów z linkami do pól, oznacza błędne pola i zachowuje wpisane dane (poza hasłami).
- Po poprawnym wysłaniu konto powstaje jako niepotwierdzone, a gość trafia na stronę „Sprawdź skrzynkę”.

Wymagania: FR-01, FR-02, FR-03.

### US-02 Potwierdzenie adresu e-mail
Jako nowy użytkownik chcę potwierdzić adres e-mail linkiem z wiadomości, aby aktywować konto.

Kryteria akceptacji:
- Po rejestracji na podany adres przychodzi wiadomość z linkiem aktywacyjnym.
- Link działa 24 godziny i tylko raz.
- Po kliknięciu adres jest potwierdzony, a użytkownik trafia na logowanie z wpisanym loginem.
- Wygasły link pokazuje komunikat „Link wygasł” i przycisk wysłania nowego linku; nieprawidłowy lub zużyty: „Link nie działa”.

Wymagania: FR-04.

### US-03 Ponowne wysłanie linku aktywacyjnego
Jako użytkownik, do którego nie dotarła wiadomość, chcę wysłać link ponownie, aby dokończyć rejestrację.

Kryteria akceptacji:
- Ponowna wysyłka jest dostępna na stronie „Sprawdź skrzynkę”, przy wygasłym linku i po próbie logowania na niepotwierdzone konto.
- Nowy link unieważnia poprzedni.
- Kolejną wiadomość można wysłać najwcześniej po minucie.
- Odpowiedź jest taka sama bez względu na to, czy konto istnieje.

Wymagania: FR-04.

### US-04 Logowanie
Jako hodowca chcę zalogować się loginem albo adresem e-mail, aby wrócić do swojej kolekcji.

Kryteria akceptacji:
- Pole przyjmuje login albo e-mail, bez względu na wielkość liter.
- Błędne dane dają jeden ogólny komunikat „Nieprawidłowy login lub hasło”, który nie zdradza, czy konto istnieje.
- Konto z niepotwierdzonym adresem nie zaloguje się; po poprawnym haśle użytkownik widzi prośbę o potwierdzenie i przycisk ponownej wysyłki.
- Po zalogowaniu użytkownik trafia do kolekcji.

Wymagania: FR-05.

### US-05 Wylogowanie
Jako hodowca chcę się wylogować z menu konta, aby nikt inny nie korzystał z mojej sesji.

Kryteria akceptacji:
- Wylogowanie jest dostępne w menu konta i wymaga tokenu CSRF.
- Po wylogowaniu strony aplikacji przekierowują na `/login`.

Wymagania: FR-06.

### US-06 Ochrona stron aplikacji
Jako właściciel danych chcę, aby strony kolekcji, studia, kliniki i harmonogramu były dostępne tylko po zalogowaniu, aby nikt nie oglądał mojej kolekcji.

Kryteria akceptacji:
- Gość wchodzący na stronę aplikacji trafia na `/login`.
- Zapytanie `fetch` bez sesji dostaje JSON 401 z adresem logowania.

Wymagania: FR-06.

## 2. Wygląd

### US-07 Motyw jasny i ciemny
Jako użytkownik chcę, aby strona miała motyw zgodny z ustawieniem komputera, a w razie potrzeby przełączyć go ręcznie.

Kryteria akceptacji:
- Bez wyboru strona używa motywu systemu i zmienia się razem z nim.
- Przełącznik w nagłówku, górnym pasku i na stronach logowania ustawia motyw przeciwny do systemowego i zapamiętuje go na rok.
- Ponowne przełączenie na motyw systemowy usuwa zapamiętany wybór.
- Przełącznik działa bez JavaScriptu (formularz) i z JavaScriptem (bez przeładowania).

Wymagania: FR-20.

## 3. Strona publiczna

### US-08 Szybka ocena ryzyka
Jako gość chcę ocenić ryzyko uschnięcia drzewka na podstawie gatunku, podłoża, podlewania i światła, aby wiedzieć, co poprawić.

Kryteria akceptacji:
- Wynik (procent, poziom ryzyka, główne czynniki, zalecana akcja) zmienia się od razu po zmianie odpowiedzi.
- Bez JavaScriptu formularz pokazuje wartości domyślne i prowadzi do kliniki.

Wymagania: FR-07.

### US-09 Pierwsza pomoc i style formowania
Jako gość chcę przeczytać protokół ratunkowy i opis klasycznych stylów, aby zareagować na objawy i poznać podstawy.

Kryteria akceptacji:
- Strona główna zawiera najczęstsze przyczyny usychania, protokół pierwszej pomocy i opis stylów z odnośnikiem do Studia.

Wymagania: FR-07.

## 4. Kolekcja i karta okazu

### US-10 Przegląd kolekcji
Jako hodowca chcę zobaczyć, które drzewka wymagają teraz zabiegu, aby zacząć od najpilniejszych.

Kryteria akceptacji:
- Sekcja „Wymaga zabiegu teraz” pokazuje drzewka wymagające podlania lub korekty drutu, a najpilniejsze jest wyróżnione.
- Lista wszystkich okazów pokazuje wilgotność, stan i ostatnie podlanie.
- Panel boczny pokazuje zdrowie kolekcji i liczbę drzewek do podlania.

Wymagania: FR-08.

### US-11 Filtrowanie i wyszukiwanie
Jako hodowca chcę filtrować kolekcję (iglaste, liściaste, wymagające uwagi) i szukać po nazwie, aby szybko znaleźć drzewko.

Kryteria akceptacji:
- Filtry i wyszukiwarka działają przez parametry adresu (`?category=`, `?status=`, `?q=`), także bez JavaScriptu.
- Gdy nic nie pasuje, strona pokazuje przyczynę i sposób powrotu do pełnej listy.

Wymagania: FR-08.

### US-12 Rejestrowanie zabiegów
Jako hodowca chcę zapisać podlanie albo korektę drutu, aby stan drzewka i harmonogram były aktualne.

Kryteria akceptacji:
- Po zapisie zmienia się wilgotność, stan i data ostatniego podlania, a także podsumowanie kolekcji i liczba powiadomień.
- Działa przez `fetch` (bez przeładowania) i zwykły formularz.

Wymagania: FR-09.

### US-13 Dziennik obserwacji
Jako hodowca chcę zapisywać krótkie notatki o kolekcji, aby śledzić zmiany w czasie.

Kryteria akceptacji:
- Wpis ma 3–500 znaków; dziennik przechowuje do 200 wpisów.
- Pusty dziennik pokazuje informację, gdzie pojawi się pierwszy wpis.

Wymagania: FR-10.

### US-14 Zdjęcia okazu
Jako hodowca chcę dodawać zdjęcia do karty drzewka, aby porównywać jego wygląd w kolejnych sezonach.

Kryteria akceptacji:
- Przyjmowane formaty: JPEG, PNG, WebP, do 2 MB i 6000 px; przeglądarka zmniejsza duże zdjęcia przed wysłaniem.
- Na jedno konto przypada do 60 zdjęć; zdjęcie można usunąć.
- Zdjęcia widzi wyłącznie ich właściciel.

Wymagania: FR-11, NFR-05.

## 5. Studio formowania

### US-15 Style klasyczne
Jako hodowca chcę porównać pięć klasycznych stylów (Chokkan, Moyogi, Shakan, Kengai, Fukinagashi), aby wybrać kierunek formowania drzewka.

Kryteria akceptacji:
- Każdy styl ma rysunek z trójkątem korony, opis, położenie wierzchołka, nasady i gałęzi dominującej oraz polecane gatunki.
- Wybór stylu działa linkiem (`?style=`) i bez przeładowania.

Wymagania: FR-12.

### US-16 Kalendarz zabiegów gatunku
Jako hodowca chcę zobaczyć, w których miesiącach wykonać zabiegi dla danego gatunku, aby nie ciąć w złym terminie.

Kryteria akceptacji:
- Wykres pokazuje najlepsze i możliwe miesiące dla cięcia, drutowania i innych zabiegów; bieżący miesiąc jest zaznaczony.
- Wskazówka sezonowa dotyczy bieżącego miesiąca; pilne ostrzeżenie jest wyróżnione.
- Kalendarz można wydrukować.

Wymagania: FR-12.

### US-17 Dobór drutu i kąt owijania
Jako hodowca chcę dobrać grubość drutu do średnicy gałęzi i sprawdzić kąt owijania, aby drut nie wrósł w korę.

Kryteria akceptacji:
- Kalkulator podaje grubość drutu, czas do kontroli i sposób zakotwiczenia dla aluminium i miedzi.
- Wzorzec kąta ostrzega przy zbyt ciasnym (poniżej 30°) i zbyt luźnym (powyżej 55°) owinięciu.

Wymagania: FR-13.

### US-18 Próbne cięcie
Jako hodowca chcę zaznaczyć gałęzie do usunięcia na rysunku, aby przed cięciem zobaczyć, ile ulistnienia zostanie i jak przesunie się środek masy korony.

Kryteria akceptacji:
- Gałęzie zaznacza się myszą lub klawiaturą (Tab, Enter).
- Wynik pokazuje procent ulistnienia, przesunięcie środka masy i wskazówkę; usunięcie ponad 1/3 ulistnienia daje ostrzeżenie.
- Plan cięcia można dodać do harmonogramu na dziś.

Wymagania: FR-14.

## 6. Klinika

### US-19 Skan zdjęcia
Jako hodowca chcę wgrać zdjęcie chorego drzewka, aby otrzymać orientacyjną ocenę barwy liści i zachować ją w historii.

Kryteria akceptacji:
- Przeglądarka analizuje udział zieleni, żółci i brązu na zdjęciu i pokazuje indeks barwy z poziomem (dobry, obserwuj, ostrzeżenie, krytyczny).
- Ocena jest opisana jako orientacyjna, nie diagnoza.
- Skan zapisuje się w historii (ostatnie 30 skanów, starsze są usuwane), którą można filtrować (iglaste, liściaste).

Wymagania: FR-15.

### US-20 Pytanie do konsultanta
Jako hodowca chcę zadać pytanie o objawy lub zabieg, aby dostać poradę z bazy wiedzy.

Kryteria akceptacji:
- Pytanie ma 3–300 znaków.
- Odpowiedź pochodzi z bazy wiedzy dopasowywanej po słowach kluczowych (nie z modelu językowego); gdy nic nie pasuje, konsultant to mówi i podsuwa tematy.

Wymagania: FR-16.

### US-21 Protokół ratunkowy do harmonogramu
Jako hodowca chcę jednym przyciskiem dodać kroki protokołu ratunkowego do harmonogramu, aby nie zapomnieć o żadnym kroku.

Kryteria akceptacji:
- Trzy kroki trafiają do harmonogramu na dziś jako własne zabiegi.

Wymagania: FR-16.

## 7. Harmonogram i powiadomienia

### US-22 Zadania dnia
Jako hodowca chcę zobaczyć zadania na wybrany dzień i odhaczać wykonane, aby trzymać się planu pielęgnacji.

Kryteria akceptacji:
- Tydzień można przewijać strzałkami, gestem i przyciskiem „Dziś”; każdy dzień pokazuje liczbę zabiegów.
- Zadania pochodzą z reguł (stan kolekcji, pora roku) i z własnych zabiegów.
- Odhaczenie zapisuje się trwale; dzień bez zadań pokazuje informację i sposób dodania zabiegu.
- Strona pokazuje bieżącą porę sekki i okna sezonowe.

Wymagania: FR-17.

### US-23 Własne zabiegi
Jako hodowca chcę dodać własny zabieg z nazwą, opisem, godziną i rodzajem, aby harmonogram obejmował też moje plany.

Kryteria akceptacji:
- Nazwa 3–80 znaków, opis do 240 znaków, rodzaj: nawadnianie, cięcie i formowanie, pielęgnacja.
- Do 100 własnych zabiegów; własny zabieg można usunąć.

Wymagania: FR-17.

### US-24 Powiadomienia
Jako hodowca chcę widzieć w dzwonku powiadomienia o podlewaniu, zadaniach i oknach sezonowych, aby nic mi nie umknęło.

Kryteria akceptacji:
- Powiadomienia są liczone z bieżących danych; licznik pokazuje nieprzeczytane.
- Można oznaczyć jedno albo wszystkie jako przeczytane; panel odświeża się przy otwarciu.
- Panel zamyka się klawiszem Escape i kliknięciem poza nim.

Wymagania: FR-18.
