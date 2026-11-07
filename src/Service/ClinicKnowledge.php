<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Baza wiedzy konsultanta Kliniki: kuratorowane odpowiedzi dopasowywane do pytania po słowach kluczowych.
 * To nie jest model językowy: przy braku dopasowania konsultant mówi to wprost i podpowiada tematy.
 */
final class ClinicKnowledge
{
    /** @var list<array{id: string, keys: list<string>, answer: string}> */
    private const ENTRIES = [
        [
            'id' => 'cambium',
            'keys' => ['zyje', 'zywa', 'zywy', 'pod kora', 'cambium', 'kambium', 'martwa', 'martwy', 'galazk'],
            'answer' => 'Test kambium: delikatnie zarysuj paznokciem lub ostrzem kawałek kory na cienkiej gałązce. Zielona, wilgotna warstwa pod spodem oznacza żywą tkankę, a brązowa i sucha martwą. Rób test w mało widocznym miejscu i nie tnij głębiej niż do drewna.',
        ],
        [
            'id' => 'po-kapieli',
            'keys' => ['po kapieli', 'kapiel', 'zanurzeniow', 'kiedy podlac', 'podlac po'],
            'answer' => 'Po kąpieli zanurzeniowej nie podlewaj od razu. Odczekaj, aż górny centymetr podłoża przeschnie (zwykle 1–2 dni; sprawdzaj patyczkiem bambusowym). Pierwsze zwykłe podlanie zrób dopiero wtedy, a drzewko do tego czasu trzymaj w półcieniu.',
        ],
        [
            'id' => 'stare-igly',
            'keys' => ['igly', 'zzolkn', 'zolkn', 'zeszlego roku', 'stare igly', 'usunac', 'opadaj'],
            'answer' => 'Starych igieł nie ścinaj „na zapas”. Naturalne żółknięcie i zrzut igieł z wnętrza korony jesienią jest normalne (sosna zrzuca 2–3-letnie igły). Usuwaj wyłącznie całkowicie brązowe i suche, palcami, delikatnym ruchem w dół. Żółknięcie świeżych wierzchołków to inny objaw (chloroza lub przesuszenie).',
        ],
        [
            'id' => 'chelat',
            'keys' => ['chelat', 'zelaz', 'eddha', 'chloroz', 'dawk'],
            'answer' => 'Przy chlorozie żelazowej stosuj chelat Fe-EDDHA (skuteczny także przy pH podłoża powyżej 7). Zacznij od ok. 20–25% dawki z etykiety, dolistnie lub do podłoża, i powtórz najwcześniej po 2 tygodniach. Zraszaj rano, nigdy w pełnym słońcu, i wstrzymaj nawozy azotowe do czasu zazielenienia igieł.',
        ],
        [
            'id' => 'przelanie',
            'keys' => ['gnic', 'przelan', 'za mokro', 'gnije', 'zgnil', 'czarne korzenie', 'root rot'],
            'answer' => 'Gnicie korzeni to zwykle skutek przelania i zbitego podłoża. Wyjmij drzewko z donicy, usuń czarne, gąbczaste korzenie ostrym narzędziem, przesadź do świeżej, gruboziarnistej mieszanki (akadama + kiryu) i ogranicz podlewanie do momentu przeschnięcia górnej warstwy. Nie nawoź przez 4–6 tygodni.',
        ],
        [
            'id' => 'przesuszenie',
            'keys' => ['przesusz', 'sucho', 'wiotk', 'wiedn', 'turgor', 'zwiedl'],
            'answer' => 'Przy przesuszeniu zanurz donicę po krawędź w miękkiej wodzie na 15–20 minut, do ustania bąbelków powietrza, a potem odstaw drzewko do półcienia na kilka dni. Nie przycinaj i nie drutuj osłabionego okazu. Sprawdzaj wilgotność bryły dwa razy dziennie.',
        ],
        [
            'id' => 'szkodniki',
            'keys' => ['przedziork', 'mszyc', 'szkodnik', 'pajeczyn', 'neem', 'wrog'],
            'answer' => 'Przędziorki i mszyce: najpierw mechanicznie spłucz koronę strumieniem wody (także od spodu liści), potem opryskaj olejkiem neem lub mydłem potasowym wieczorem, poza słońcem. Powtórz po 7 dniach. Potrząśnij gałązką nad białą kartką: ruchome kropki to przędziorki.',
        ],
        [
            'id' => 'przesadzanie',
            'keys' => ['przesadz', 'donic', 'podloze', 'akadam', 'kiryu'],
            'answer' => 'Przesadzaj wczesną wiosną, tuż przed pękaniem pąków: młode drzewa co 2–3 lata, dojrzałe co 4–5 lat. Redukuj bryłę korzeniową maksymalnie o ok. 1/3, zachowuj korzenie z mikoryzą i używaj mineralnego podłoża (akadama + kiryu + pumeks). Po zabiegu trzymaj drzewko w półcieniu, bez nawożenia, przez 3–4 tygodnie.',
        ],
        [
            'id' => 'drut',
            'keys' => ['drut', 'wrasta', 'wcina', 'miedz', 'aluminium', 'formowan'],
            'answer' => 'Drut kontroluj co tydzień w okresie wzrostu. Gdy zaczyna się wcinać w korę, zdejmij go, przecinając szczypcami do drutu w kilku miejscach, a nie odwijając (żeby nie złamać gałęzi). Zwykle drut zostaje 3–6 miesięcy, u klonów krócej.',
        ],
        [
            'id' => 'nawozenie',
            'keys' => ['nawoz', 'biogold', 'azot', 'nawozen'],
            'answer' => 'Nawóz organiczny (np. Biogold) w koszyczkach wokół krawędzi donicy zmieniaj co ok. 4 tygodnie od wiosny do wczesnej jesieni. Nie nawoź świeżo przesadzonych, osłabionych i suchych drzew ani w okresie spoczynku. Zimą nawozy odstaw.',
        ],
        [
            'id' => 'zimowanie',
            'keys' => ['zim', 'mroz', 'przymrozk', 'spoczyn', 'chlod'],
            'answer' => 'Drzewa gatunków rodzimych potrzebują zimowego spoczynku w temperaturze ok. 0–7°C. Chroń korzenie przed przemarznięciem (donica w gruncie, w słomie lub w nieogrzewanym pomieszczeniu), ogranicz podlewanie, ale nie dopuszczaj do całkowitego wyschnięcia bryły.',
        ],
        [
            'id' => 'swiatlo',
            'keys' => ['swiatl', 'slonc', 'okno', 'parapet', 'polcien', 'ekspozycj', 'cien'],
            'answer' => 'Większość bonsai gatunków rodzimych potrzebuje minimum 5–6 godzin jasnego światła dziennie, najlepiej na zewnątrz. Parapet południowy przez szybę jest tylko rozwiązaniem tymczasowym dla gatunków pokojowych (np. fikus). Drzewka po zabiegach i osłabione trzymaj w półcieniu.',
        ],
        [
            'id' => 'podlewanie',
            'keys' => ['podlew', 'jak czesto', 'woda', 'nawodni', 'wilgotnosc'],
            'answer' => 'Podlewaj „na potrzebę”, nie według kalendarza: gdy górny centymetr podłoża przeschnie (sprawdź patyczkiem), podlewaj obficie, aż woda wypłynie otworami drenażowymi. Najlepsza jest miękka, odstana woda lub deszczówka, rano.',
        ],
        [
            'id' => 'oparzenia',
            'keys' => ['oparz', 'poparz', 'brazow', 'plamy', 'sloneczne'],
            'answer' => 'Brązowe, pergaminowe plamy na liściach po silnym słońcu to oparzenia. Przenieś drzewko w półcień, zapewnij stałą wilgotność i nie stosuj nawozów, dopóki nie wypuści nowych liści. Zwiędłe liście możesz usunąć dopiero po kilku tygodniach.',
        ],
    ];

    private const TOPICS = [
        'Jak sprawdzić, czy gałązka żyje?',
        'Jak podlewać po kąpieli zanurzeniowej?',
        'Kiedy przesadzać bonsai?',
        'Co robić przy gniciu korzeni?',
        'Jak walczyć z przędziorkami?',
    ];

    /**
     * @return array{answer: string, matched: bool, topics: list<string>}
     */
    public function answer(string $question): array
    {
        $normalized = $this->normalize($question);
        $best = null;
        $bestScore = 0;

        foreach (self::ENTRIES as $entry) {
            $score = 0;
            foreach ($entry['keys'] as $key) {
                if (str_contains($normalized, $key)) {
                    // Dłuższe, bardziej szczegółowe frazy ważą więcej niż krótkie rdzenie słów.
                    $score += 1 + (int) (strlen($key) / 6);
                }
            }
            if ($score > $bestScore) {
                $best = $entry;
                $bestScore = $score;
            }
        }

        if ($best === null) {
            return [
                'answer' => 'Nie mam pewnej odpowiedzi na to pytanie w swojej bazie wiedzy. Opisz objaw konkretniej (np. co dokładnie się zmieniło i na której części drzewka) albo wybierz jeden z tematów poniżej. Przy poważnych objawach skonsultuj się ze specjalistą.',
                'matched' => false,
                'topics' => self::TOPICS,
            ];
        }

        return ['answer' => $best['answer'], 'matched' => true, 'topics' => []];
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);

        return strtr($text, [
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z', 'ż' => 'z',
        ]);
    }
}
