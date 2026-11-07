<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * Demonstracyjna kolekcja drzewek wyświetlana w panelu i na kartach okazów.
 * Dane są zapisane w kodzie, bo aplikacja nie ma jeszcze warstwy bazy danych.
 */
final class TreeRepository
{
    private const TREES = [
        1 => [
            'id' => 1,
            'name' => 'Matsu',
            'kanji' => '松',
            'species' => 'Pinus thunbergii',
            'commonName' => 'Sosna czarna japońska',
            'category' => 'conifer',
            'ageLabel' => '24 lata',
            'style' => 'Moyogi',
            'styleDescription' => 'Swobodny pion',
            'substrate' => 'Akadama',
            'substrateMix' => '+ Kiryu (70/30)',
            'pot' => 'Tokoname',
            'potDescription' => 'Ceramika nieszkliwiona',
            'moisture' => 65,
            'moistureLabel' => 'Optymalna',
            'status' => 'ideal',
            'statusLabel' => 'Idealny',
            'lastWatering' => 'wczoraj, 07:15',
            'image' => '/images/garden-pine-stand.jpg',
            'inCollectionSince' => 2018,
            'alert' => null,
            'care' => null,
            'reminder' => 'Uszczykiwanie świeczek za 3 dni',
            'action' => null,
            'actionType' => null,
            'phenology' => [
                'phase' => 'Wiosna – faza pękania świeczek szczytowych',
                'description' => 'Intensywny przepływ soków, wysokie zapotrzebowanie na światło.',
            ],
            'vitality' => [
                'label' => 'Turgor igieł',
                'value' => 'Prawidłowy',
                'description' => 'Barwa soczyście zielona, brak oznak szkodników.',
            ],
            'recommendation' => [
                'month' => 'Kwiecień',
                'text' => 'Nie dopuszczaj do przesuszenia wierzchniej warstwy mchu. Zapewnij pełną ekspozycję południową do południa.',
                'nextStep' => 'Następne nawożenie Biogold: za 12 dni',
            ],
            'history' => [
                [
                    'date' => 'Kwiecień 2024',
                    'type' => 'Prace wiosenne',
                    'title' => 'Mekiri (uszczyknięcie świeczek o 50%)',
                    'description' => 'Selektywne skrócenie pędów szczytowych w celu zbalansowania energii i stymulacji pąków śpiących. Widoczne zagęszczenie poduszek igłowych.',
                    'images' => [
                        ['src' => '/images/garden-pine-fence.jpg', 'label' => 'Przed zabiegiem', 'caption' => 'Świeczki wiosenne (100%)'],
                        ['src' => '/images/garden-pine-stand.jpg', 'label' => 'Po zabiegu (Mekiri)', 'caption' => 'Skrócone pąki o 50%'],
                    ],
                ],
                [
                    'date' => 'Październik 2023',
                    'type' => 'Formowanie',
                    'title' => 'Drutowanie miedzią gałęzi pierwszorzędnej',
                    'description' => 'Założenie wyżarzonego drutu miedzianego (3,5 mm). Obniżenie kąta pierwszej prawej gałęzi (Sashi-eda) o 30° dla uzyskania dojrzałego, naturalnego układu.',
                    'images' => [
                        ['src' => '/images/wiring-copper-branch.jpg', 'label' => 'Drut 3,5 mm', 'caption' => null],
                    ],
                ],
                [
                    'date' => 'Kwiecień 2022',
                    'type' => 'Przesadzanie',
                    'title' => 'Przesadzenie i redukcja bryły korzeniowej',
                    'description' => 'Redukcja bryły o 25%, zachowanie zdrowych pasm mikoryzy. Wymiana podłoża na świeżą mieszankę gruboziarnistej Akadamy i Kiryu.',
                    'images' => [],
                ],
            ],
        ],
        2 => [
            'id' => 2,
            'name' => 'Momiji',
            'kanji' => '紅葉',
            'species' => 'Acer palmatum',
            'commonName' => 'Klon palmowy',
            'category' => 'deciduous',
            'ageLabel' => '12 lat',
            'style' => 'Chokkan',
            'styleDescription' => 'Formalny pion',
            'substrate' => 'Akadama',
            'substrateMix' => '+ Kanuma (50/50)',
            'pot' => 'Shudei',
            'potDescription' => 'Glina szkliwiona',
            'moisture' => 28,
            'moistureLabel' => 'Niska',
            'status' => 'attention',
            'statusLabel' => 'Wymaga działania',
            'lastWatering' => '48 godzin temu',
            'image' => '/images/momiji-red-maple.jpg',
            'inCollectionSince' => 2021,
            'alert' => [
                'title' => 'Lekkie przesuszenie końcówek liści',
                'text' => 'Zalecane natychmiastowe zraszanie miękką wodą zebraną z deszczówki oraz przestawienie z ostrego słońca.',
            ],
            'care' => null,
            'reminder' => 'Nawożenie Biogold: za 4 dni • Przygotuj koszyczki nawozowe',
            'action' => 'Zapisz podlanie',
            'actionType' => 'water',
            'phenology' => [
                'phase' => 'Wiosna – rozwijanie pierwszych liści',
                'description' => 'Młode liście są wrażliwe na wiatr i ostre słońce południowe.',
            ],
            'vitality' => [
                'label' => 'Turgor liści',
                'value' => 'Obniżony',
                'description' => 'Zasychające brzegi blaszek na wierzchołku korony.',
            ],
            'recommendation' => [
                'month' => 'Kwiecień',
                'text' => 'Podlewaj zanurzeniowo przy każdym przeschnięciu wierzchniej warstwy. Ustaw drzewko w półcieniu do czasu stwardnienia liści.',
                'nextStep' => 'Następne nawożenie Biogold: za 4 dni',
            ],
            'history' => [
                [
                    'date' => 'Czerwiec 2024',
                    'type' => 'Defoliacja',
                    'title' => 'Częściowa defoliacja (Ha-kiri)',
                    'description' => 'Usunięcie największych liści z zewnętrznej partii korony w celu zmniejszenia blaszek i doświetlenia wnętrza.',
                    'images' => [
                        ['src' => '/images/garden-red-maple-lantern.jpg', 'label' => 'Po defoliacji', 'caption' => null],
                    ],
                ],
                [
                    'date' => 'Marzec 2023',
                    'type' => 'Przesadzanie',
                    'title' => 'Przesadzenie do donicy Shudei',
                    'description' => 'Wymiana podłoża na mieszankę Akadamy i Kanumy. Rozczesanie korzeni i poprawa nebari.',
                    'images' => [],
                ],
            ],
        ],
        3 => [
            'id' => 3,
            'name' => 'Shimpaku',
            'kanji' => '真柏',
            'species' => 'Juniperus chinensis',
            'commonName' => 'Jałowiec chiński',
            'category' => 'conifer',
            'ageLabel' => '45 lat',
            'style' => 'Kengai',
            'styleDescription' => 'Kaskada',
            'substrate' => 'Akadama',
            'substrateMix' => '+ Pumeks (60/40)',
            'pot' => 'Kaskadowa',
            'potDescription' => 'Wysoka donica ceramiczna',
            'moisture' => 58,
            'moistureLabel' => 'Optymalna',
            'status' => 'shaping',
            'statusLabel' => 'Formowanie',
            'lastWatering' => 'dzisiaj, 06:40',
            'image' => '/images/shimpaku-cascade.jpg',
            'inCollectionSince' => 2015,
            'alert' => null,
            'care' => [
                'title' => 'Formowanie pędów bocznych',
                'meta' => 'Drut miedziany #2.5',
                'text' => '14. tydzień po założeniu. Sprawdź, czy drut nie wrasta w kambium na dolnym załamaniu shari.',
            ],
            'reminder' => null,
            'action' => 'Zapisz korektę drutu',
            'actionType' => 'adjust',
            'phenology' => [
                'phase' => 'Wiosna – wybijanie nowych łusek',
                'description' => 'Aktywny wzrost pędów; kontroluj napięcie drutu co tydzień.',
            ],
            'vitality' => [
                'label' => 'Kondycja łusek',
                'value' => 'Prawidłowa',
                'description' => 'Zwarte, jasnozielone przyrosty na końcach pędów.',
            ],
            'recommendation' => [
                'month' => 'Kwiecień',
                'text' => 'Uszczykuj wydłużone pędy palcami, nie nożyczkami. Zabezpiecz martwe drewno płynem do jin przed sezonem deszczowym.',
                'nextStep' => 'Kontrola drutu: za 6 dni',
            ],
            'history' => [
                [
                    'date' => 'Styczeń 2024',
                    'type' => 'Formowanie',
                    'title' => 'Drutowanie kaskady głównej',
                    'description' => 'Założenie drutu miedzianego #2.5 na pędach bocznych i korekta linii kaskady poniżej krawędzi donicy.',
                    'images' => [
                        ['src' => '/images/wiring-copper-branch-2.jpg', 'label' => 'Drut #2.5', 'caption' => null],
                    ],
                ],
                [
                    'date' => 'Sierpień 2022',
                    'type' => 'Martwe drewno',
                    'title' => 'Odświeżenie shari i jin',
                    'description' => 'Oczyszczenie martwego drewna i konserwacja roztworem polisiarczku wapnia.',
                    'images' => [],
                ],
            ],
        ],
        4 => [
            'id' => 4,
            'name' => 'Zelkova',
            'kanji' => '欅',
            'species' => 'Zelkova serrata',
            'commonName' => 'Wiąz japoński',
            'category' => 'deciduous',
            'ageLabel' => '8 lat',
            'style' => 'Hokidachi',
            'styleDescription' => 'Miotlasty',
            'substrate' => 'Kiryu',
            'substrateMix' => '+ Akadama (50/50)',
            'pot' => 'Owalna',
            'potDescription' => 'Płytka donica nieszkliwiona',
            'moisture' => 18,
            'moistureLabel' => 'Krytyczna',
            'status' => 'critical',
            'statusLabel' => 'Wymaga kąpieli',
            'lastWatering' => '3 dni temu',
            'image' => '/images/zelkova-bare.jpg',
            'inCollectionSince' => 2022,
            'alert' => [
                'title' => 'Zalecana kąpiel korzeniowa',
                'text' => 'Przeschnięty rdzeń podłoża Kiryu. Zanurzyć donicę w wodzie po brzeg na 12 minut.',
            ],
            'care' => null,
            'reminder' => null,
            'action' => 'Zapisz kąpiel korzeni',
            'actionType' => 'water',
            'phenology' => [
                'phase' => 'Wiosna – pękanie pąków liściowych',
                'description' => 'Najwyższe zapotrzebowanie na wodę w całym sezonie.',
            ],
            'vitality' => [
                'label' => 'Turgor pędów',
                'value' => 'Krytyczny',
                'description' => 'Wiotkie młode pędy, podłoże przeschnięte w rdzeniu.',
            ],
            'recommendation' => [
                'month' => 'Kwiecień',
                'text' => 'Wykonaj kąpiel zanurzeniową, a przez kolejne dni kontroluj wilgotność patyczkiem bambusowym rano i wieczorem.',
                'nextStep' => 'Kontrola wilgotności: dzisiaj wieczorem',
            ],
            'history' => [
                [
                    'date' => 'Luty 2024',
                    'type' => 'Cięcie',
                    'title' => 'Cięcie strukturalne korony miotlastej',
                    'description' => 'Skrócenie konarów do dwóch międzywęźli i wyrównanie sylwetki parasola.',
                    'images' => [
                        ['src' => '/images/zelkova-bare.jpg', 'label' => 'Sylwetka zimowa', 'caption' => null],
                    ],
                ],
            ],
        ],
    ];

    /**
     * @return list<array<string, mixed>>
     */
    public function findAll(): array
    {
        return array_values(self::TREES);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array
    {
        return self::TREES[$id] ?? null;
    }
}
