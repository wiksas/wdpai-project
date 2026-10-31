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

    private const ERRORS = [
        ['term' => 'Kuruma-eda (Koła szprychowe):', 'text' => 'Więcej niż jedna gałąź wyrastająca z tej samej wysokości pnia.'],
        ['term' => 'Kaeru-mata (Żabie nogi):', 'text' => "Symetryczne rozgałęzienie 'Y' rozbijające dynamikę stożka."],
        ['term' => 'Kannonuki (Gałąź ryglująca):', 'text' => 'Gałąź rosnąca wprost ku widzowi na linii wzroku.'],
    ];

    /** @var array<string, array<string, mixed>> */
    private const STYLES = [
        'chokkan' => [
            'number' => '01', 'kanji' => '直幹', 'icon' => 'north', 'name' => 'Chokkan', 'subtitle' => 'Pionowy formalny', 'value' => 92,
            'title' => 'Chokkan (Pień Prosty)', 'badge' => 'Klasyka', 'balance' => 'Trójkąt Równoramienny',
            'text' => 'Najbardziej formalny styl: prosty, zbieżny pień wyrasta pionowo z promienistego nebari, a gałęzie układają się spiralnie wokół niego. Wierzchołek znajduje się dokładnie nad środkiem podstawy. Idealny dla gatunków o naturalnie prostym pokroju.',
            'metrics' => [
                ['label' => 'Wierzchołek (Tenjin)', 'value' => 'Pionowo nad nasadą (0°)', 'tone' => ''],
                ['label' => 'Podstawa (Nebari)', 'value' => 'Promieniste 360°', 'tone' => ''],
                ['label' => 'Dominanta (Sashi-eda)', 'value' => 'Pierwsza gałąź na 1/3', 'tone' => 'terra'],
            ],
            'species' => ['Pinus thunbergii (Sosna)', 'Zelkova serrata (Zelkowa)', 'Picea glehnii (Świerk)'],
            'model' => 'Sosna Czarna Chokkan',
            'diagram' => [
                'triangle' => [[246, 158], [126, 306], [366, 256]],
                'trunk' => 'M246 352 C 245 300, 247 220, 246 160',
                'branches' => [
                    ['d' => 'M246 306 C 214 306, 170 308, 138 306', 'w' => 4],
                    ['d' => 'M246 258 C 290 256, 330 256, 362 256', 'w' => 4],
                    ['d' => 'M246 214 C 226 210, 206 206, 190 204', 'w' => 3],
                ],
                'foliage' => [[126, 308, 27, 10.5, '#3d5a4a'], [132, 304, 17, 6, '#4d6b5a'], [366, 258, 32, 12, '#3d5a4a'], [356, 254, 21, 7, '#4d6b5a'], [182, 206, 20, 8, '#3d5a4a'], [246, 154, 34, 13, '#1e3a2b'], [251, 149, 24, 8, '#2f4d3d']],
                'angle' => '90°', 'angleAt' => [196, 306], 'note' => 'Uke-eda (+5°)', 'noteAt' => [313, 244],
            ],
        ],
        'moyogi' => [
            'number' => '02', 'kanji' => '模様木', 'icon' => 'show_chart', 'name' => 'Moyogi', 'subtitle' => 'Pionowy swobodny', 'value' => 75,
            'title' => 'Moyogi (Drzewo Faliste)', 'badge' => 'Popularny', 'balance' => 'Trójkąt Asymetryczny',
            'text' => 'Styl odwzorowujący drzewa z nizin i lasów, które walcząc o światło wykształciły sinusoidalny pień. Kluczowa zasada: kolejne wygięcia pnia muszą maleć ku górze, a wierzchołek zawsze wraca pionowo ponad nasadę korzeniową (środek ciężkości).',
            'metrics' => [
                ['label' => 'Wierzchołek (Tenjin)', 'value' => 'Pochylenie 8° do przodu', 'tone' => ''],
                ['label' => 'Podstawa (Nebari)', 'value' => 'Promieniste 360°', 'tone' => ''],
                ['label' => 'Dominanta (Sashi-eda)', 'value' => '1/3 wysokości pnia', 'tone' => 'terra'],
            ],
            'species' => ['Pinus densiflora (Sosna)', 'Acer palmatum (Klon)', 'Juniperus chinensis (Jałowiec)'],
            'model' => 'Jałowiec Shin-Moyogi #204',
            'diagram' => [
                'triangle' => [[248, 158], [122, 322], [366, 266]],
                'trunk' => 'M246 352 C 224 322, 226 290, 240 262 C 256 232, 258 200, 248 160',
                'branches' => [
                    ['d' => 'M244 268 C 290 256, 330 262, 362 266', 'w' => 4],
                    ['d' => 'M232 314 C 200 316, 168 318, 136 318', 'w' => 4],
                    ['d' => 'M240 236 L 294 214', 'w' => 3, 'color' => '#8e9a92'],
                ],
                'foliage' => [[126, 320, 27, 10.5, '#3d5a4a'], [132, 316, 17, 6, '#4d6b5a'], [362, 266, 32, 12, '#3d5a4a'], [352, 262, 21, 7, '#4d6b5a'], [302, 213, 19, 6.5, '#b7c4ba'], [248, 156, 34, 13, '#1e3a2b'], [253, 151, 24, 8, '#2f4d3d']],
                'angle' => '82°', 'angleAt' => [196, 306], 'note' => 'Uke-eda (+15°)', 'noteAt' => [313, 254],
            ],
        ],
        'shakan' => [
            'number' => '03', 'kanji' => '斜幹', 'icon' => 'trending_up', 'name' => 'Shakan', 'subtitle' => 'Pochylony grawitacyjnie', 'value' => 60,
            'title' => 'Shakan (Pień Pochylony)', 'badge' => 'Dynamiczny', 'balance' => 'Trójkąt Przesunięty',
            'text' => 'Pień pochylony pod kątem 60–80° względem podłoża, jak drzewo zginane wiatrem lub cieniem sąsiadów. Korzenie po stronie przeciwnej do pochylenia muszą „trzymać” drzewo wizualnie, a pierwsza gałąź wyrasta w kierunku przeciwnym do pochylenia, równoważąc kompozycję.',
            'metrics' => [
                ['label' => 'Wierzchołek (Tenjin)', 'value' => 'Przesunięty 25° od osi', 'tone' => ''],
                ['label' => 'Podstawa (Nebari)', 'value' => 'Wzmocnione po stronie pochylenia', 'tone' => ''],
                ['label' => 'Dominanta (Sashi-eda)', 'value' => 'Przeciwna do pochylenia', 'tone' => 'terra'],
            ],
            'species' => ['Pinus thunbergii (Sosna)', 'Juniperus chinensis (Jałowiec)', 'Acer palmatum (Klon)'],
            'model' => 'Sosna Czarna Shakan',
            'diagram' => [
                'triangle' => [[336, 162], [136, 316], [392, 256]],
                'trunk' => 'M214 352 C 224 322, 252 280, 290 232 C 310 206, 324 184, 334 164',
                'branches' => [
                    ['d' => 'M246 308 C 214 308, 176 312, 146 316', 'w' => 4],
                    ['d' => 'M296 240 C 330 240, 362 248, 388 256', 'w' => 4],
                ],
                'foliage' => [[132, 318, 27, 10.5, '#3d5a4a'], [138, 314, 17, 6, '#4d6b5a'], [388, 258, 32, 12, '#3d5a4a'], [378, 254, 21, 7, '#4d6b5a'], [336, 158, 34, 13, '#1e3a2b'], [341, 153, 24, 8, '#2f4d3d']],
                'angle' => '68°', 'angleAt' => [204, 336], 'note' => 'Pochylenie 22°', 'noteAt' => [313, 200],
            ],
        ],
        'kengai' => [
            'number' => '04', 'kanji' => '懸崖', 'icon' => 'south_east', 'name' => 'Kengai', 'subtitle' => 'Pełna kaskada skalna', 'value' => 40,
            'title' => 'Kengai (Kaskada)', 'badge' => 'Zaawansowany', 'balance' => 'Trójkąt Opadający',
            'text' => 'Pień opada poniżej krawędzi donicy, jak drzewo wyrastające ze skalnej ściany nad przepaścią. Wymaga wysokiej donicy, silnego wierzchołka kaskady poniżej dna oraz bardzo starannego drutowania — korona nie może „zwisać”, lecz walczyć o światło ku górze.',
            'metrics' => [
                ['label' => 'Wierzchołek (Tenjin)', 'value' => 'Poniżej dna donicy', 'tone' => ''],
                ['label' => 'Podstawa (Nebari)', 'value' => 'Mocne kotwiczenie, 1 strona', 'tone' => ''],
                ['label' => 'Dominanta (Sashi-eda)', 'value' => 'Pierwszy łuk nad rimem', 'tone' => 'terra'],
            ],
            'species' => ['Juniperus procumbens (Jałowiec)', 'Pinus thunbergii (Sosna)', 'Cotoneaster (Irga)'],
            'model' => 'Jałowiec Kengai #118',
            'diagram' => [
                'triangle' => [[282, 282], [150, 322], [338, 444]],
                'trunk' => 'M246 352 C 240 322, 252 292, 282 284 C 322 276, 352 308, 336 372 C 330 400, 332 426, 338 442',
                'branches' => [
                    ['d' => 'M262 296 C 230 300, 186 314, 158 322', 'w' => 4],
                    ['d' => 'M340 384 C 360 392, 380 398, 396 408', 'w' => 3],
                ],
                'foliage' => [[146, 324, 27, 10.5, '#3d5a4a'], [152, 320, 17, 6, '#4d6b5a'], [398, 410, 26, 10, '#3d5a4a'], [338, 448, 34, 13, '#1e3a2b'], [343, 443, 24, 8, '#2f4d3d'], [284, 278, 28, 10, '#2f4d3d']],
                'angle' => '135°', 'angleAt' => [196, 338], 'note' => 'Opadanie −62°', 'noteAt' => [352, 340],
            ],
        ],
        'fukinagashi' => [
            'number' => '05', 'kanji' => '吹流し', 'icon' => 'air', 'name' => 'Fukinagashi', 'subtitle' => 'Smagane wiatrem wybrzeża', 'value' => 85,
            'title' => 'Fukinagashi (Pokrój Smagany Wiatrem)', 'badge' => 'Ekspresyjny', 'balance' => 'Trójkąt Jednostronny',
            'text' => 'Wszystkie gałęzie i wierzchołek układają się w jedną stronę, jakby drzewo przez dziesięciolecia rosło na wietrznym wybrzeżu. Liczy się jednolity kierunek przepływu masy, brak gałęzi po stronie nawietrznej oraz wrażenie ruchu — nawet w bezwietrzny dzień.',
            'metrics' => [
                ['label' => 'Wierzchołek (Tenjin)', 'value' => 'Odchylony 55° z wiatrem', 'tone' => ''],
                ['label' => 'Podstawa (Nebari)', 'value' => 'Asymetryczne, zakotwiczone', 'tone' => ''],
                ['label' => 'Dominanta (Sashi-eda)', 'value' => 'Wszystkie w jednym kierunku', 'tone' => 'terra'],
            ],
            'species' => ['Juniperus chinensis (Jałowiec)', 'Pinus thunbergii (Sosna)', 'Juniperus rigida (Jałowiec ostry)'],
            'model' => 'Jałowiec Fukinagashi #77',
            'diagram' => [
                'triangle' => [[366, 198], [236, 340], [410, 266]],
                'trunk' => 'M228 352 C 230 312, 248 272, 288 242 C 318 220, 340 204, 362 198',
                'branches' => [
                    ['d' => 'M254 306 C 296 298, 346 298, 396 310', 'w' => 4],
                    ['d' => 'M274 264 C 316 254, 360 254, 408 264', 'w' => 4],
                ],
                'foliage' => [[398, 312, 30, 11, '#3d5a4a'], [390, 308, 20, 7, '#4d6b5a'], [412, 266, 28, 10, '#3d5a4a'], [404, 262, 18, 6, '#4d6b5a'], [366, 194, 34, 13, '#1e3a2b'], [371, 189, 24, 8, '#2f4d3d']],
                'angle' => '55°', 'angleAt' => [262, 332], 'note' => 'Kierunek wiatru →', 'noteAt' => [298, 236],
            ],
        ],
    ];

    /** @var array<string, array<string, mixed>> */
    private const SPECIES = [
        'pine' => [
            'tab' => 'Sosna<br>Gęstokwiatowa', 'icon' => 'park',
            'title' => 'Sosna Gęstokwiatowa (Pinus densiflora / Akamatsu)',
            'subtitle' => 'Gatunek iglasty dwuigielny o silnej apikalnej dominacji pąków szczytowych.',
            'rows' => [
                ['icon' => 'content_cut', 'tone' => 'terra', 'title' => 'Cięcie strukturalne (Grube konary)', 'note' => 'Najlepiej w okresie uśpienia (XII - II)', 'cells' => [0 => 'dark', 1 => 'dark', 9 => 'dark', 10 => 'muted', 11 => 'dark']],
                ['icon' => 'spa', 'tone' => 'green', 'title' => 'Uszczykiwanie pędów (Mekiri / Candling)', 'note' => 'Równoważenie wigoru (VI - VII)', 'cells' => [4 => 'pale', 5 => 'green', 6 => 'green', 9 => 'pale']],
                ['icon' => 'change_circle', 'tone' => 'terra', 'title' => 'Przerzedzanie igieł / Prześwietlanie pąków', 'note' => 'Jesienna selekcja (X - XI)', 'cells' => [9 => 'terra', 10 => 'terra']],
                ['icon' => 'all_inclusive', 'tone' => 'dark', 'title' => 'Czas bezpiecznego drutowania (Wymóg braku soku)', 'note' => 'Późna jesień i zima (X - III)', 'cells' => [0 => 'safe', 1 => 'safe', 2 => 'soft', 9 => 'safe', 10 => 'safe', 11 => 'safe']],
            ],
            'seasons' => [
                'winter' => ['state' => 'Uśpienie / Cięcie i drutowanie', 'label' => 'Wskazówka', 'alert' => 'Najlepszy czas na cięcie konarów i drutowanie. Rany większe niż 4 mm zabezpiecz pastą gojącą, a drzewko chroń przed przemarznięciem bryły korzeniowej.'],
                'spring' => ['state' => 'Start wegetacji / Obserwacja pąków', 'label' => 'Wskazówka', 'alert' => 'Nie tnij grubych konarów — soki ruszają. Obserwuj świeczki pędów i planuj przesadzanie przed ich wydłużeniem.'],
                'summer' => ['state' => 'Wzrost / Uszczykiwanie pędów', 'label' => 'Wskazówka', 'alert' => 'Uszczykuj świeczki w czerwcu i lipcu, aby wyrównać wigor. Unikaj mocnych cięć i nawożenia azotem w upał.'],
                'autumn' => ['state' => 'Stan spoczynku / Drutowanie', 'label' => 'Krytyczne ostrzeżenie', 'alert' => 'Drewno twardnieje przed przymrozkami. Zastosuj maść gojącą z hormonem kalusa (Kiyonal) na każde cięcie powyżej 4 mm średnicy, by zapobiec wyciekowi żywicy.'],
            ],
        ],
        'maple' => [
            'tab' => 'Klon<br>Japoński', 'icon' => 'eco',
            'title' => 'Klon Japoński (Acer palmatum / Irohamomiji)',
            'subtitle' => 'Gatunek liściasty o delikatnej korze i silnym krwawieniu soków wczesną wiosną.',
            'rows' => [
                ['icon' => 'content_cut', 'tone' => 'terra', 'title' => 'Cięcie strukturalne (Gałęzie główne)', 'note' => 'Po opadnięciu liści, przed sokami (XII - II)', 'cells' => [0 => 'dark', 1 => 'dark', 10 => 'muted', 11 => 'dark']],
                ['icon' => 'spa', 'tone' => 'green', 'title' => 'Defoliacja (Redukcja blaszki liściowej)', 'note' => 'Tylko zdrowe drzewa (VI)', 'cells' => [4 => 'pale', 5 => 'green']],
                ['icon' => 'change_circle', 'tone' => 'terra', 'title' => 'Przesadzanie i korekta nebari', 'note' => 'Przy nabrzmiewających pąkach (III - IV)', 'cells' => [2 => 'terra', 3 => 'terra']],
                ['icon' => 'all_inclusive', 'tone' => 'dark', 'title' => 'Czas bezpiecznego drutowania (Kora wrażliwa)', 'note' => 'Sezon wzrostu, kontrola co 3 tyg. (V - IX)', 'cells' => [4 => 'safe', 5 => 'safe', 6 => 'safe', 7 => 'safe', 8 => 'safe']],
            ],
            'seasons' => [
                'winter' => ['state' => 'Uśpienie / Cięcie strukturalne', 'label' => 'Wskazówka', 'alert' => 'Idealny czas na cięcie gałęzi głównych — przed ruszeniem soków. Rany zabezpiecz pastą; drzewko trzymaj w chłodzie, ale bez silnych mrozów.'],
                'spring' => ['state' => 'Pękanie pąków / Przesadzanie', 'label' => 'Krytyczne ostrzeżenie', 'alert' => 'Silne krwawienie soków po cięciu — nie tnij po ruszeniu wegetacji. Przesadzaj tuż przed rozwinięciem liści i chroń młode liście przed słońcem.'],
                'summer' => ['state' => 'Pełnia liści / Defoliacja', 'label' => 'Wskazówka', 'alert' => 'Defoliację wykonuj tylko na silnych drzewach w czerwcu. Chroń przed oparzeniami w południe i podlewaj obficie.'],
                'autumn' => ['state' => 'Kolory jesieni / Przygotowanie do zimy', 'label' => 'Wskazówka', 'alert' => 'Ogranicz nawożenie i zadbaj o czyste, jasne stanowisko — od tego zależą barwy liści. Po opadzie liści oceń strukturę gałęzi.'],
            ],
        ],
        'juniper' => [
            'tab' => 'Jałowiec<br>Chiński', 'icon' => 'forest',
            'title' => 'Jałowiec Chiński (Juniperus chinensis / Shimpaku)',
            'subtitle' => 'Gatunek iglasty o łuskowatych igłach, doskonały do rzeźbienia martwego drewna (jin, shari).',
            'rows' => [
                ['icon' => 'content_cut', 'tone' => 'terra', 'title' => 'Cięcie i redukcja korony', 'note' => 'Po wzroście wiosennym (IV - VIII)', 'cells' => [3 => 'dark', 4 => 'dark', 5 => 'dark', 6 => 'dark', 7 => 'dark']],
                ['icon' => 'spa', 'tone' => 'green', 'title' => 'Uszczykiwanie pędów (Pinching)', 'note' => 'Regularnie w sezonie (VI - IX)', 'cells' => [4 => 'pale', 5 => 'green', 6 => 'green', 7 => 'green', 8 => 'green']],
                ['icon' => 'change_circle', 'tone' => 'terra', 'title' => 'Przesadzanie i odmładzanie podłoża', 'note' => 'Wczesna wiosna (III - IV)', 'cells' => [2 => 'terra', 3 => 'terra']],
                ['icon' => 'all_inclusive', 'tone' => 'dark', 'title' => 'Czas bezpiecznego drutowania', 'note' => 'Jesień i zima (IX - III)', 'cells' => [0 => 'safe', 1 => 'safe', 2 => 'soft', 8 => 'safe', 9 => 'safe', 10 => 'safe', 11 => 'safe']],
            ],
            'seasons' => [
                'winter' => ['state' => 'Uśpienie / Drutowanie', 'label' => 'Wskazówka', 'alert' => 'Dobry moment na drutowanie i rzeźbienie jin/shari. Chroń bryłę przed przemarznięciem i nie dopuszczaj do przesuszenia.'],
                'spring' => ['state' => 'Start wzrostu / Przesadzanie', 'label' => 'Wskazówka', 'alert' => 'Przesadź drzewko w marcu–kwietniu, zachowując mikoryzę. Nie nawoź przez 3 tygodnie po zabiegu.'],
                'summer' => ['state' => 'Wzrost / Uszczykiwanie', 'label' => 'Wskazówka', 'alert' => 'Uszczykuj końcówki palcami, nie nożyczkami (unikasz brązowienia). Zraszaj koronę rano i dbaj o przewiew.'],
                'autumn' => ['state' => 'Spowolnienie wzrostu / Drutowanie', 'label' => 'Wskazówka', 'alert' => 'Sezon drutowania — kontroluj, czy drut nie wrasta. Pędy układaj łagodnie; jałowiec łatwo pęka przy ostrych zgięciach.'],
            ],
        ],
        'azalea' => [
            'tab' => 'Azalia<br>Satsuki', 'icon' => 'local_florist',
            'title' => 'Azalia Satsuki (Rhododendron indicum)',
            'subtitle' => 'Gatunek zimozielony o obfitym, późnym kwitnieniu; wymaga kwaśnego podłoża.',
            'rows' => [
                ['icon' => 'content_cut', 'tone' => 'terra', 'title' => 'Cięcie formujące', 'note' => 'Tuż po kwitnieniu (VI - VII)', 'cells' => [5 => 'dark', 6 => 'dark']],
                ['icon' => 'spa', 'tone' => 'green', 'title' => 'Usuwanie przekwitłych kwiatów', 'note' => 'Natychmiast po przekwitnięciu (V - VI)', 'cells' => [4 => 'pale', 5 => 'green']],
                ['icon' => 'change_circle', 'tone' => 'terra', 'title' => 'Przesadzanie (podłoże kwaśne)', 'note' => 'Po kwitnieniu (VI - VII)', 'cells' => [5 => 'terra', 6 => 'terra']],
                ['icon' => 'all_inclusive', 'tone' => 'dark', 'title' => 'Czas bezpiecznego drutowania', 'note' => 'Jesień i zima (X - II)', 'cells' => [0 => 'safe', 1 => 'soft', 9 => 'safe', 10 => 'safe', 11 => 'safe']],
            ],
            'seasons' => [
                'winter' => ['state' => 'Spoczynek / Drutowanie', 'label' => 'Wskazówka', 'alert' => 'Chroń przed mrozem poniżej −5°C. Drutuj ostrożnie — gałązki azalii są kruche.'],
                'spring' => ['state' => 'Rozwój pąków kwiatowych', 'label' => 'Wskazówka', 'alert' => 'Nie tnij przed kwitnieniem. Podlewaj miękką, kwaśną wodą i unikaj zmoczenia kwiatów.'],
                'summer' => ['state' => 'Po kwitnieniu / Cięcie i przesadzanie', 'label' => 'Krytyczne ostrzeżenie', 'alert' => 'Tnij i przesadzaj zaraz po przekwitnięciu — później usuniesz pąki na przyszły sezon. Używaj podłoża o pH 4,5–5,5.'],
                'autumn' => ['state' => 'Zawiązywanie pąków / Drutowanie', 'label' => 'Wskazówka', 'alert' => 'Pąki na przyszły rok są już zawiązane — nie przycinaj końcówek. Drutowanie dozwolone, ale bez zgięć na pąkach.'],
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
     * Kalendarz gatunku dla bieżącego miesiąca: wiersze wykresu, stan sezonu i ostrzeżenie.
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

        $rows = array_map(static function (array $row) use ($month): array {
            if (in_array($row['cells'][$month] ?? '', ['dark', 'green', 'pale'], true)) {
                $row['cells'][$month] = 'window';
            }

            return $row;
        }, $species['rows']);

        return [
            'id' => $speciesId,
            'title' => $species['title'],
            'subtitle' => $species['subtitle'],
            'icon' => $species['icon'],
            'rows' => $rows,
            'currentMonth' => $month,
            'season' => $species['seasons'][$season],
        ];
    }
}
