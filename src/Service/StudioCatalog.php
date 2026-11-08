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
                'pot' => ['kind' => 'shallow', 'x' => 260, 'y' => 394, 'w' => 210, 'h' => 36],
                'trunk' => ['pts' => [[260, 396], [257, 330], [263, 220], [260, 98]], 'w0' => 36, 'w1' => 6, 'flare' => 16],
                'branches' => [
                    ['pts' => [[259, 334], [222, 326], [176, 336], [132, 346]], 'w0' => 9, 'w1' => 2.5],
                    ['pts' => [[261, 284], [304, 274], [346, 284], [388, 292]], 'w0' => 8, 'w1' => 2.3],
                    ['pts' => [[259, 226], [232, 216], [206, 220], [182, 228]], 'w0' => 7, 'w1' => 2],
                    ['pts' => [[260, 176], [288, 168], [312, 172], [336, 178]], 'w0' => 6, 'w1' => 2],
                    ['pts' => [[259, 132], [246, 126], [234, 128], [222, 134]], 'w0' => 5, 'w1' => 1.8],
                ],
                'pads' => [
                    ['cx' => 118, 'cy' => 350, 'rx' => 68, 'ry' => 25, 'rot' => -2, 'branch' => 0],
                    ['cx' => 404, 'cy' => 296, 'rx' => 64, 'ry' => 23, 'rot' => 3, 'branch' => 1],
                    ['cx' => 172, 'cy' => 232, 'rx' => 50, 'ry' => 18, 'rot' => -3, 'branch' => 2],
                    ['cx' => 350, 'cy' => 182, 'rx' => 44, 'ry' => 16, 'rot' => 4, 'branch' => 3],
                    ['cx' => 206, 'cy' => 136, 'rx' => 36, 'ry' => 14, 'rot' => -4, 'branch' => 4],
                    ['cx' => 260, 'cy' => 86, 'rx' => 46, 'ry' => 26, 'rot' => 0, 'tone' => 'apex'],
                ],
                'triangle' => [[260, 64], [112, 356], [412, 300]],
                'axis' => [260, 60, 396],
                'marks' => [[260, 86], [292, 402], [190, 338]],
                'angle' => '90°', 'note' => 'Gałęzie układają się spiralnie wokół prostego pnia.',
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
                'pot' => ['kind' => 'shallow', 'x' => 260, 'y' => 394, 'w' => 210, 'h' => 36],
                'trunk' => ['pts' => [[256, 398], [255, 366], [206, 356], [212, 320], [218, 288], [262, 276], [278, 240], [292, 208], [286, 190], [264, 170], [250, 154], [252, 122], [258, 100]], 'w0' => 38, 'w1' => 6, 'flare' => 18],
                'branches' => [
                    ['pts' => [[212, 326], [180, 326], [150, 342], [118, 354]], 'w0' => 9, 'w1' => 2.5],
                    ['pts' => [[286, 238], [326, 226], [364, 238], [398, 250]], 'w0' => 8, 'w1' => 2.3],
                    ['pts' => [[268, 190], [294, 176], [316, 172], [336, 170]], 'w0' => 5, 'w1' => 1.8, 'back' => true],
                    ['pts' => [[252, 146], [228, 136], [206, 138], [186, 146]], 'w0' => 5, 'w1' => 1.8],
                ],
                'pads' => [
                    ['cx' => 104, 'cy' => 358, 'rx' => 62, 'ry' => 23, 'rot' => -3, 'branch' => 0],
                    ['cx' => 412, 'cy' => 254, 'rx' => 60, 'ry' => 22, 'rot' => 3, 'branch' => 1],
                    ['cx' => 346, 'cy' => 170, 'rx' => 42, 'ry' => 15, 'rot' => 2, 'tone' => 'back', 'branch' => 2],
                    ['cx' => 172, 'cy' => 150, 'rx' => 38, 'ry' => 15, 'rot' => -3, 'branch' => 3],
                    ['cx' => 258, 'cy' => 88, 'rx' => 44, 'ry' => 25, 'rot' => 0, 'tone' => 'apex'],
                ],
                'triangle' => [[258, 64], [108, 372], [410, 262]],
                'axis' => [258, 60, 396],
                'marks' => [[258, 88], [290, 402], [150, 344]],
                'angle' => '82°', 'note' => 'Gałąź tylna (ura-eda) wychodzi do tyłu, za pniem.',
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
                'pot' => ['kind' => 'shallow', 'x' => 235, 'y' => 394, 'w' => 210, 'h' => 36],
                'trunk' => ['pts' => [[210, 396], [214, 362], [248, 318], [284, 274], [312, 240], [330, 202], [346, 148], [350, 128], [351, 110], [350, 100]], 'w0' => 38, 'w1' => 6, 'flare' => 18],
                'branches' => [
                    ['pts' => [[234, 342], [198, 334], [158, 344], [120, 356]], 'w0' => 9, 'w1' => 2.5],
                    ['pts' => [[302, 258], [344, 248], [384, 254], [418, 264]], 'w0' => 8, 'w1' => 2.3],
                    ['pts' => [[332, 188], [304, 178], [276, 180], [252, 188]], 'w0' => 6, 'w1' => 2],
                ],
                'pads' => [
                    ['cx' => 106, 'cy' => 360, 'rx' => 62, 'ry' => 23, 'rot' => -3, 'branch' => 0],
                    ['cx' => 430, 'cy' => 268, 'rx' => 56, 'ry' => 20, 'rot' => 4, 'branch' => 1],
                    ['cx' => 238, 'cy' => 192, 'rx' => 40, 'ry' => 15, 'rot' => -3, 'branch' => 2],
                    ['cx' => 350, 'cy' => 90, 'rx' => 44, 'ry' => 25, 'rot' => 0, 'tone' => 'apex'],
                ],
                'triangle' => [[350, 64], [104, 360], [430, 260]],
                'axis' => [210, 60, 396],
                'marks' => [[350, 90], [176, 404], [168, 344]],
                'angle' => '68°', 'note' => 'Pień odchylony od pionu o 22°.',
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
                'pot' => ['kind' => 'cascade', 'x' => 214, 'y' => 142, 'w' => 118, 'h' => 124],
                'stand' => ['x' => 214, 'w' => 154, 'top' => 266, 'floor' => 436],
                'trunk' => ['pts' => [[214, 146], [208, 112], [226, 86], [262, 80], [312, 72], [346, 104], [338, 150], [332, 192], [306, 214], [314, 254], [320, 284], [346, 312], [338, 350], [332, 376], [332, 396], [336, 412]], 'w0' => 32, 'w1' => 6, 'flare' => 14],
                'branches' => [
                    ['pts' => [[238, 84], [212, 66], [178, 68], [142, 82]], 'w0' => 8, 'w1' => 2.3],
                    ['pts' => [[336, 196], [368, 198], [398, 208], [426, 222]], 'w0' => 7, 'w1' => 2],
                    ['pts' => [[320, 292], [296, 302], [274, 312], [252, 322]], 'w0' => 6, 'w1' => 2],
                ],
                'pads' => [
                    ['cx' => 130, 'cy' => 86, 'rx' => 58, 'ry' => 20, 'rot' => -4, 'branch' => 0],
                    ['cx' => 440, 'cy' => 226, 'rx' => 46, 'ry' => 18, 'rot' => 6, 'branch' => 1],
                    ['cx' => 240, 'cy' => 328, 'rx' => 38, 'ry' => 15, 'rot' => -6, 'branch' => 2],
                    ['cx' => 336, 'cy' => 420, 'rx' => 40, 'ry' => 24, 'rot' => 0, 'tone' => 'apex'],
                ],
                'triangle' => [[130, 70], [446, 222], [336, 444]],
                'axis' => [214, 60, 436],
                'marks' => [[336, 420], [232, 146], [186, 68]],
                'angle' => '135°', 'note' => 'Wierzchołek zwisa poniżej dna donicy.',
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
                'pot' => ['kind' => 'shallow', 'x' => 205, 'y' => 394, 'w' => 210, 'h' => 36],
                'trunk' => ['pts' => [[172, 396], [170, 352], [190, 308], [226, 276], [262, 248], [308, 230], [358, 216], [370, 212], [378, 208], [388, 204]], 'w0' => 38, 'w1' => 6, 'flare' => 18],
                'branches' => [
                    ['pts' => [[198, 332], [256, 324], [316, 330], [376, 344]], 'w0' => 9, 'w1' => 2.5],
                    ['pts' => [[254, 262], [312, 250], [372, 256], [432, 270]], 'w0' => 8, 'w1' => 2.3],
                ],
                'pads' => [
                    ['cx' => 396, 'cy' => 350, 'rx' => 70, 'ry' => 20, 'rot' => 7, 'branch' => 0],
                    ['cx' => 448, 'cy' => 274, 'rx' => 56, 'ry' => 18, 'rot' => 8, 'branch' => 1],
                    ['cx' => 400, 'cy' => 194, 'rx' => 54, 'ry' => 23, 'rot' => -10, 'tone' => 'apex'],
                ],
                'triangle' => [[400, 166], [176, 344], [458, 268]],
                'axis' => [172, 150, 396],
                'marks' => [[400, 194], [146, 404], [300, 330]],
                'angle' => '55°', 'note' => 'Wszystkie gałęzie biegną w jedną stronę, z wiatrem.',
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

    /**
     * Drzewko do próbnego cięcia: cztery numerowane gałęzie (`group`) i wierzchołek. Pień stoi na osi x = 284
     * (stała AXIS_X w studio.js), a pola koron (rx, ry) i ich środki liczy skrypt z rysunku.
     *
     * @return array<string, mixed>
     */
    public function simulator(): array
    {
        return [
            'pot' => ['kind' => 'shallow', 'x' => 284, 'y' => 438, 'w' => 300, 'h' => 38],
            'trunk' => ['pts' => [[284, 440], [281, 380], [288, 300], [284, 166]], 'w0' => 40, 'w1' => 7, 'flare' => 22],
            'branches' => [
                ['pts' => [[281, 358], [236, 352], [186, 356], [138, 362]], 'w0' => 9, 'w1' => 3, 'group' => 1],
                ['pts' => [[287, 346], [330, 344], [366, 350], [390, 356]], 'w0' => 9, 'w1' => 3, 'group' => 2],
                ['pts' => [[284, 290], [246, 274], [212, 264], [184, 260]], 'w0' => 7, 'w1' => 2.5, 'group' => 3],
                ['pts' => [[287, 254], [326, 246], [358, 240], [392, 236]], 'w0' => 7, 'w1' => 2.5, 'group' => 4],
            ],
            'pads' => [
                ['cx' => 98, 'cy' => 360, 'rx' => 52, 'ry' => 21, 'rot' => -2, 'branch' => 0],
                ['cx' => 420, 'cy' => 358, 'rx' => 46, 'ry' => 19, 'rot' => 3, 'branch' => 1],
                ['cx' => 158, 'cy' => 258, 'rx' => 38, 'ry' => 16, 'rot' => -3, 'branch' => 2],
                ['cx' => 428, 'cy' => 232, 'rx' => 50, 'ry' => 19, 'rot' => 3, 'branch' => 3],
                ['cx' => 284, 'cy' => 148, 'rx' => 60, 'ry' => 24, 'rot' => 0, 'tone' => 'apex'],
            ],
        ];
    }

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
