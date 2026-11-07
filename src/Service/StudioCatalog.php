<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;

/**
 * Katalog wiedzy Studio Formowania: klasyczne style drzewek i kalendarze zabiegów dla gatunków.
 * Dane są kuratorowane (statyczne); wybór stylu/gatunku i bieżący miesiąc decydują o widoku.
 */
final class StudioCatalog
{
    public const DEFAULT_STYLE = 'moyogi';
    public const DEFAULT_SPECIES = 'pine';

    /** Gałęzie do usunięcia (imi-eda), wspólne dla wszystkich stylów. */
    private const ERRORS = [
        ['term' => 'Kuruma-eda', 'kanji' => '車枝', 'text' => 'kilka gałęzi wyrasta z pnia na tej samej wysokości, jak szprychy koła.'],
        ['term' => 'Kannuki-eda', 'kanji' => '閂枝', 'text' => 'dwie gałęzie wyrastają naprzeciw siebie na tej samej wysokości, jak rygiel w bramie.'],
        ['term' => 'Kaeru-mata', 'kanji' => '蛙股', 'text' => 'pień lub gałąź rozwidla się symetrycznie w literę Y (żabie nogi) i rozbija stożek korony.'],
    ];

    /**
     * Style klasyczne. Kolory diagramu są rolami (`leaf`, `leaf-light`, `apex`, `apex-light`, `back`),
     * a CSS zamienia je na tokeny motywu.
     *
     * @var array<string, array<string, mixed>>
     */
    private const STYLES = [
        'chokkan' => [
            'kanji' => '直幹', 'name' => 'Chokkan', 'subtitle' => 'Pionowy formalny',
            'title' => 'Pień prosty', 'balance' => 'równoramienny',
            'text' => 'Najbardziej formalny styl: prosty, zbieżny pień wyrasta pionowo z promienistego nebari, a gałęzie układają się spiralnie wokół niego. Wierzchołek znajduje się dokładnie nad środkiem podstawy. Pasuje do gatunków o naturalnie prostym pokroju.',
            'metrics' => [
                ['label' => 'Wierzchołek', 'value' => 'Dokładnie nad nasadą'],
                ['label' => 'Nasada korzeniowa (nebari)', 'value' => 'Promieniste, na wszystkie strony'],
                ['label' => 'Gałąź dominująca (sashi-eda)', 'value' => 'Pierwsza, na 1/3 wysokości'],
            ],
            'species' => ['Pinus thunbergii (sosna)', 'Zelkova serrata (zelkowa)', 'Picea glehnii (świerk)'],
            'diagram' => [
                'triangle' => [[246, 158], [126, 306], [366, 256]],
                'trunk' => 'M246 352 C 245 300, 247 220, 246 160',
                'branches' => [
                    ['d' => 'M246 306 C 214 306, 170 308, 138 306', 'w' => 4],
                    ['d' => 'M246 258 C 290 256, 330 256, 362 256', 'w' => 4],
                    ['d' => 'M246 214 C 226 210, 206 206, 190 204', 'w' => 3],
                ],
                'foliage' => [[126, 308, 27, 10.5, 'leaf'], [132, 304, 17, 6, 'leaf-light'], [366, 258, 32, 12, 'leaf'], [356, 254, 21, 7, 'leaf-light'], [182, 206, 20, 8, 'leaf'], [246, 154, 34, 13, 'apex'], [251, 149, 24, 8, 'apex-light']],
                'angle' => '90°', 'angleAt' => [196, 306], 'note' => 'Uke-eda (+5°)', 'noteAt' => [313, 244],
            ],
        ],
        'moyogi' => [
            'kanji' => '模様木', 'name' => 'Moyogi', 'subtitle' => 'Pionowy swobodny',
            'title' => 'Drzewo faliste', 'balance' => 'asymetryczny',
            'text' => 'Styl odwzorowuje drzewa z nizin i lasów, które w walce o światło wykształciły falisty pień. Kolejne wygięcia pnia maleją ku górze, a wierzchołek zawsze wraca pionowo nad nasadę korzeniową, czyli nad środek ciężkości.',
            'metrics' => [
                ['label' => 'Wierzchołek', 'value' => 'Nad nasadą, lekko ku widzowi'],
                ['label' => 'Nasada korzeniowa (nebari)', 'value' => 'Promieniste, na wszystkie strony'],
                ['label' => 'Gałąź dominująca (sashi-eda)', 'value' => 'Na zewnątrz pierwszego łuku'],
            ],
            'species' => ['Pinus densiflora (sosna)', 'Acer palmatum (klon)', 'Juniperus chinensis (jałowiec)'],
            'diagram' => [
                'triangle' => [[248, 158], [122, 322], [366, 266]],
                'trunk' => 'M246 352 C 224 322, 226 290, 240 262 C 256 232, 258 200, 248 160',
                'branches' => [
                    ['d' => 'M244 268 C 290 256, 330 262, 362 266', 'w' => 4],
                    ['d' => 'M232 314 C 200 316, 168 318, 136 318', 'w' => 4],
                    ['d' => 'M240 236 L 294 214', 'w' => 3, 'tone' => 'back'],
                ],
                'foliage' => [[126, 320, 27, 10.5, 'leaf'], [132, 316, 17, 6, 'leaf-light'], [362, 266, 32, 12, 'leaf'], [352, 262, 21, 7, 'leaf-light'], [302, 213, 19, 6.5, 'back'], [248, 156, 34, 13, 'apex'], [253, 151, 24, 8, 'apex-light']],
                'angle' => '82°', 'angleAt' => [196, 306], 'note' => 'Gałąź tylna (ura-eda)', 'noteAt' => [306, 200],
            ],
        ],
        'shakan' => [
            'kanji' => '斜幹', 'name' => 'Shakan', 'subtitle' => 'Pień pochylony',
            'title' => 'Pień pochylony', 'balance' => 'przesunięty',
            'text' => 'Pień jest pochylony pod kątem 60–80° względem podłoża, jak drzewo zginane wiatrem albo cieniem sąsiadów. Korzenie po stronie przeciwnej do pochylenia wizualnie trzymają drzewo, a pierwsza gałąź wyrasta w przeciwnym kierunku i równoważy kompozycję.',
            'metrics' => [
                ['label' => 'Wierzchołek', 'value' => 'Poza podstawą, po stronie pochylenia'],
                ['label' => 'Nasada korzeniowa (nebari)', 'value' => 'Silniejsze po stronie przeciwnej'],
                ['label' => 'Gałąź dominująca (sashi-eda)', 'value' => 'Przeciwnie do pochylenia'],
            ],
            'species' => ['Pinus thunbergii (sosna)', 'Juniperus chinensis (jałowiec)', 'Acer palmatum (klon)'],
            'diagram' => [
                'triangle' => [[336, 162], [136, 316], [392, 256]],
                'trunk' => 'M214 352 C 224 322, 252 280, 290 232 C 310 206, 324 184, 334 164',
                'branches' => [
                    ['d' => 'M246 308 C 214 308, 176 312, 146 316', 'w' => 4],
                    ['d' => 'M296 240 C 330 240, 362 248, 388 256', 'w' => 4],
                ],
                'foliage' => [[132, 318, 27, 10.5, 'leaf'], [138, 314, 17, 6, 'leaf-light'], [388, 258, 32, 12, 'leaf'], [378, 254, 21, 7, 'leaf-light'], [336, 158, 34, 13, 'apex'], [341, 153, 24, 8, 'apex-light']],
                'angle' => '68°', 'angleAt' => [204, 336], 'note' => 'Odchylenie od pionu 22°', 'noteAt' => [300, 200],
            ],
        ],
        'kengai' => [
            'kanji' => '懸崖', 'name' => 'Kengai', 'subtitle' => 'Pełna kaskada',
            'title' => 'Kaskada', 'balance' => 'opadający',
            'text' => 'Pień opada poniżej krawędzi donicy, jak drzewo wyrastające ze skalnej ściany nad przepaścią. Styl wymaga wysokiej donicy, silnego wierzchołka kaskady poniżej dna i starannego drutowania: korona nie zwisa bezwładnie, tylko kieruje się ku światłu.',
            'metrics' => [
                ['label' => 'Wierzchołek', 'value' => 'Poniżej dna donicy'],
                ['label' => 'Nasada korzeniowa (nebari)', 'value' => 'Mocno zakotwiczone z jednej strony'],
                ['label' => 'Gałąź dominująca (sashi-eda)', 'value' => 'Pierwszy łuk nad krawędzią donicy'],
            ],
            'species' => ['Juniperus procumbens (jałowiec)', 'Pinus thunbergii (sosna)', 'Cotoneaster (irga)'],
            'diagram' => [
                'triangle' => [[282, 282], [150, 322], [338, 444]],
                'trunk' => 'M246 352 C 240 322, 252 292, 282 284 C 322 276, 352 308, 336 372 C 330 400, 332 426, 338 442',
                'branches' => [
                    ['d' => 'M262 296 C 230 300, 186 314, 158 322', 'w' => 4],
                    ['d' => 'M340 384 C 360 392, 380 398, 396 408', 'w' => 3],
                ],
                'foliage' => [[146, 324, 27, 10.5, 'leaf'], [152, 320, 17, 6, 'leaf-light'], [398, 410, 26, 10, 'leaf'], [338, 448, 34, 13, 'apex'], [343, 443, 24, 8, 'apex-light'], [284, 278, 28, 10, 'apex-light']],
                'angle' => '135°', 'angleAt' => [196, 338], 'note' => 'Wierzchołek poniżej donicy', 'noteAt' => [352, 340],
            ],
        ],
        'fukinagashi' => [
            'kanji' => '吹流し', 'name' => 'Fukinagashi', 'subtitle' => 'Smagany wiatrem',
            'title' => 'Pokrój smagany wiatrem', 'balance' => 'jednostronny',
            'text' => 'Wszystkie gałęzie i wierzchołek układają się w jedną stronę, jakby drzewo przez dziesięciolecia rosło na wietrznym wybrzeżu. Liczy się jednolity kierunek masy, brak gałęzi po stronie nawietrznej i wrażenie ruchu, nawet w bezwietrzny dzień.',
            'metrics' => [
                ['label' => 'Wierzchołek', 'value' => 'Odchylony z wiatrem'],
                ['label' => 'Nasada korzeniowa (nebari)', 'value' => 'Asymetryczne, od strony wiatru'],
                ['label' => 'Gałąź dominująca (sashi-eda)', 'value' => 'Wszystkie w jednym kierunku'],
            ],
            'species' => ['Juniperus chinensis (jałowiec)', 'Pinus thunbergii (sosna)', 'Juniperus rigida (jałowiec sztywny)'],
            'diagram' => [
                'triangle' => [[366, 198], [236, 340], [410, 266]],
                'trunk' => 'M228 352 C 230 312, 248 272, 288 242 C 318 220, 340 204, 362 198',
                'branches' => [
                    ['d' => 'M254 306 C 296 298, 346 298, 396 310', 'w' => 4],
                    ['d' => 'M274 264 C 316 254, 360 254, 408 264', 'w' => 4],
                ],
                'foliage' => [[398, 312, 30, 11, 'leaf'], [390, 308, 20, 7, 'leaf-light'], [412, 266, 28, 10, 'leaf'], [404, 262, 18, 6, 'leaf-light'], [366, 194, 34, 13, 'apex'], [371, 189, 24, 8, 'apex-light']],
                'angle' => '55°', 'angleAt' => [262, 332], 'note' => 'Kierunek wiatru →', 'noteAt' => [298, 236],
            ],
        ],
    ];

    /**
     * Kalendarze gatunków. Komórki wykresu: `best` (najlepszy termin) albo `possible` (możliwy, ostrożnie).
     *
     * @var array<string, array<string, mixed>>
     */
    private const SPECIES = [
        'pine' => [
            'tab' => 'Sosna gęstokwiatowa',
            'title' => 'Sosna gęstokwiatowa', 'latin' => 'Pinus densiflora', 'jaName' => 'Akamatsu', 'jaKanji' => '赤松',
            'subtitle' => 'Iglasta, dwuigielna, z silną dominacją pąków szczytowych.',
            'rows' => [
                ['title' => 'Cięcie strukturalne grubych konarów', 'note' => 'W okresie uśpienia (X–II)', 'cells' => [0 => 'best', 1 => 'best', 9 => 'best', 10 => 'possible', 11 => 'best']],
                ['title' => 'Wyłamywanie świec (midori-tsumi)', 'note' => 'Wyrównanie wigoru (V–VII)', 'cells' => [4 => 'possible', 5 => 'best', 6 => 'best', 9 => 'possible']],
                ['title' => 'Przerzedzanie igieł i pąków', 'note' => 'Jesienna selekcja (X–XI)', 'cells' => [9 => 'best', 10 => 'best']],
                ['title' => 'Drutowanie', 'note' => 'Późna jesień i zima, bez ruchu soków (X–III)', 'cells' => [0 => 'best', 1 => 'best', 2 => 'possible', 9 => 'best', 10 => 'best', 11 => 'best']],
            ],
            'seasons' => [
                'winter' => ['state' => 'uśpienie, cięcie i drutowanie', 'urgent' => false, 'alert' => 'Najlepszy czas na cięcie konarów i drutowanie. Rany większe niż 4 mm zabezpiecz pastą gojącą, a bryłę korzeniową chroń przed przemarznięciem.'],
                'spring' => ['state' => 'start wegetacji, obserwacja pąków', 'urgent' => false, 'alert' => 'Nie tnij grubych konarów, bo ruszają soki. Obserwuj świece pędów i przesadzaj, zanim się wydłużą.'],
                'summer' => ['state' => 'wzrost, wyłamywanie świec', 'urgent' => false, 'alert' => 'Wyłamuj świece w czerwcu i lipcu, aby wyrównać wigor. W upał unikaj mocnych cięć i nawożenia azotem.'],
                'autumn' => ['state' => 'spoczynek, drutowanie', 'urgent' => false, 'alert' => 'Dobry czas na drutowanie i cięcie konarów. Każdą ranę powyżej 4 mm zabezpiecz pastą gojącą, bo sosna mocno wycieka żywicą.'],
            ],
        ],
        'maple' => [
            'tab' => 'Klon japoński',
            'title' => 'Klon japoński', 'latin' => 'Acer palmatum', 'jaName' => 'Iroha-momiji', 'jaKanji' => 'いろは紅葉',
            'subtitle' => 'Liściasty, o delikatnej korze; wczesną wiosną po cięciu mocno krwawi sokiem.',
            'rows' => [
                ['title' => 'Cięcie strukturalne gałęzi głównych', 'note' => 'Po opadnięciu liści, przed ruszeniem soków (XI–II)', 'cells' => [0 => 'best', 1 => 'best', 10 => 'possible', 11 => 'best']],
                ['title' => 'Defoliacja (hagari)', 'note' => 'Tylko zdrowe, silne drzewa (V–VI)', 'cells' => [4 => 'possible', 5 => 'best']],
                ['title' => 'Przesadzanie i korekta nebari', 'note' => 'Przy nabrzmiewających pąkach (III–IV)', 'cells' => [2 => 'best', 3 => 'best']],
                ['title' => 'Drutowanie', 'note' => 'W sezonie wzrostu, kontrola co 3 tygodnie (V–IX)', 'cells' => [4 => 'best', 5 => 'best', 6 => 'best', 7 => 'best', 8 => 'best']],
            ],
            'seasons' => [
                'winter' => ['state' => 'uśpienie, cięcie strukturalne', 'urgent' => false, 'alert' => 'Idealny czas na cięcie gałęzi głównych, zanim ruszą soki. Rany zabezpiecz pastą; drzewko trzymaj w chłodzie, ale bez silnych mrozów.'],
                'spring' => ['state' => 'pękanie pąków, przesadzanie', 'urgent' => true, 'alert' => 'Po ruszeniu wegetacji klon mocno krwawi sokiem z każdej rany, więc teraz nie tnij. Przesadzaj tuż przed rozwinięciem liści i chroń młode liście przed słońcem.'],
                'summer' => ['state' => 'pełnia liści, defoliacja', 'urgent' => false, 'alert' => 'Defoliację wykonuj tylko na silnych drzewach na przełomie maja i czerwca. Chroń liście przed południowym słońcem i podlewaj obficie.'],
                'autumn' => ['state' => 'przebarwianie, przygotowanie do zimy', 'urgent' => false, 'alert' => 'Ogranicz nawożenie i zapewnij jasne stanowisko, od tego zależą barwy liści. Po opadnięciu liści oceń strukturę gałęzi.'],
            ],
        ],
        'juniper' => [
            'tab' => 'Jałowiec chiński',
            'title' => 'Jałowiec chiński', 'latin' => 'Juniperus chinensis', 'jaName' => 'Shimpaku', 'jaKanji' => '真柏',
            'subtitle' => 'Iglasty, o łuskowatych igłach; dobrze znosi rzeźbienie martwego drewna (jin, shari).',
            'rows' => [
                ['title' => 'Cięcie i redukcja korony', 'note' => 'Po wiosennym wzroście (IV–VIII)', 'cells' => [3 => 'best', 4 => 'best', 5 => 'best', 6 => 'best', 7 => 'best']],
                ['title' => 'Uszczykiwanie pędów', 'note' => 'Regularnie w sezonie (V–IX)', 'cells' => [4 => 'possible', 5 => 'best', 6 => 'best', 7 => 'best', 8 => 'best']],
                ['title' => 'Przesadzanie i wymiana podłoża', 'note' => 'Wczesna wiosna (III–IV)', 'cells' => [2 => 'best', 3 => 'best']],
                ['title' => 'Drutowanie', 'note' => 'Jesień i zima (IX–III)', 'cells' => [0 => 'best', 1 => 'best', 2 => 'possible', 8 => 'best', 9 => 'best', 10 => 'best', 11 => 'best']],
            ],
            'seasons' => [
                'winter' => ['state' => 'uśpienie, drutowanie', 'urgent' => false, 'alert' => 'Dobry moment na drutowanie i rzeźbienie jin oraz shari. Chroń bryłę przed przemarznięciem i nie dopuszczaj do przesuszenia.'],
                'spring' => ['state' => 'start wzrostu, przesadzanie', 'urgent' => false, 'alert' => 'Przesadź drzewko w marcu lub kwietniu, zachowując mikoryzę. Nie nawoź przez 3 tygodnie po zabiegu.'],
                'summer' => ['state' => 'wzrost, uszczykiwanie', 'urgent' => false, 'alert' => 'Uszczykuj końcówki palcami, nie nożyczkami, żeby nie brązowiały. Zraszaj koronę rano i dbaj o przewiew.'],
                'autumn' => ['state' => 'spowolnienie wzrostu, drutowanie', 'urgent' => false, 'alert' => 'Sezon drutowania: sprawdzaj, czy drut nie wrasta. Pędy układaj łagodnie, bo jałowiec łatwo pęka przy ostrych zgięciach.'],
            ],
        ],
        'azalea' => [
            'tab' => 'Azalia satsuki',
            'title' => 'Azalia satsuki', 'latin' => 'Rhododendron indicum', 'jaName' => 'Satsuki', 'jaKanji' => '皐月',
            'subtitle' => 'Zimozielona, kwitnie obficie i późno; wymaga kwaśnego podłoża.',
            'rows' => [
                ['title' => 'Cięcie formujące', 'note' => 'Tuż po kwitnieniu (VI–VII)', 'cells' => [5 => 'best', 6 => 'best']],
                ['title' => 'Usuwanie przekwitłych kwiatów', 'note' => 'Zaraz po przekwitnięciu (V–VI)', 'cells' => [4 => 'possible', 5 => 'best']],
                ['title' => 'Przesadzanie do kwaśnego podłoża', 'note' => 'Po kwitnieniu (VI–VII)', 'cells' => [5 => 'best', 6 => 'best']],
                ['title' => 'Drutowanie', 'note' => 'Jesień i zima (X–II)', 'cells' => [0 => 'best', 1 => 'possible', 9 => 'best', 10 => 'best', 11 => 'best']],
            ],
            'seasons' => [
                'winter' => ['state' => 'spoczynek, drutowanie', 'urgent' => false, 'alert' => 'Chroń przed mrozem poniżej −5 °C. Drutuj ostrożnie, bo gałązki azalii są kruche.'],
                'spring' => ['state' => 'rozwój pąków kwiatowych', 'urgent' => false, 'alert' => 'Nie tnij przed kwitnieniem. Podlewaj miękką, kwaśną wodą i nie mocz kwiatów.'],
                'summer' => ['state' => 'po kwitnieniu, cięcie i przesadzanie', 'urgent' => true, 'alert' => 'Tnij i przesadzaj zaraz po przekwitnięciu; później usuniesz pąki na przyszły sezon. Używaj podłoża o pH 4,5–5,5.'],
                'autumn' => ['state' => 'zawiązywanie pąków, drutowanie', 'urgent' => false, 'alert' => 'Pąki na przyszły rok są już zawiązane, więc nie przycinaj końcówek. Drutowanie jest dozwolone, ale bez zgięć na pąkach.'],
            ],
        ],
    ];

    /** @return list<array<string, mixed>> */
    public function styles(string $activeId): array
    {
        $list = [];
        foreach (self::STYLES as $id => $style) {
            $list[] = ['id' => $id, 'active' => $id === $activeId] + $style;
        }

        return $list;
    }

    /** @return array<string, mixed> */
    public function style(string $id): array
    {
        $id = isset(self::STYLES[$id]) ? $id : self::DEFAULT_STYLE;

        return ['id' => $id, 'errors' => self::ERRORS] + self::STYLES[$id];
    }

    public function normalizeStyle(mixed $id): string
    {
        return is_string($id) && isset(self::STYLES[$id]) ? $id : self::DEFAULT_STYLE;
    }

    public function normalizeSpecies(mixed $id): string
    {
        return is_string($id) && isset(self::SPECIES[$id]) ? $id : self::DEFAULT_SPECIES;
    }

    /** @return list<array{id: string, tab: string, active: bool}> */
    public function speciesTabs(string $activeId): array
    {
        $tabs = [];
        foreach (self::SPECIES as $id => $species) {
            $tabs[] = ['id' => $id, 'tab' => $species['tab'], 'active' => $id === $activeId];
        }

        return $tabs;
    }

    /**
     * Kalendarz gatunku dla bieżącego miesiąca: wiersze wykresu, stan sezonu i wskazówka.
     *
     * @return array<string, mixed>
     */
    public function calendar(string $speciesId, DateTimeImmutable $today): array
    {
        $speciesId = $this->normalizeSpecies($speciesId);
        $species = self::SPECIES[$speciesId];
        $month = (int) $today->format('n') - 1;
        $season = match (true) {
            in_array($month, [11, 0, 1], true) => 'winter',
            in_array($month, [2, 3, 4], true) => 'spring',
            in_array($month, [5, 6, 7], true) => 'summer',
            default => 'autumn',
        };

        return [
            'id' => $speciesId,
            'title' => $species['title'],
            'latin' => $species['latin'],
            'jaName' => $species['jaName'],
            'jaKanji' => $species['jaKanji'],
            'subtitle' => $species['subtitle'],
            'rows' => $species['rows'],
            'currentMonth' => $month,
            'season' => $species['seasons'][$season],
        ];
    }
}
