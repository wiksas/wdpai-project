<?php

declare(strict_types=1);

namespace App\Service;

use App\Support\PolishDate;
use DateInterval;
use DateTimeImmutable;

/**
 * Harmonogram zabiegów generowany z reguł (cykle podlewania, nawożenia, cięcia) dla dowolnej daty
 * oraz zadań dodanych przez użytkownika. Dane kalendarza sezonów (sekki) i okien zabiegowych są stałe.
 */
final class ScheduleService
{
    public const TYPE_WATER = 'water';
    public const TYPE_SHAPE = 'shape';
    public const TYPE_CARE = 'care';

    /** Kolory kropek w kalendarzu: nawadnianie / cięcie i formowanie / pielęgnacja. */
    public const TYPE_TONES = [self::TYPE_WATER => 'dark', self::TYPE_SHAPE => 'terra', self::TYPE_CARE => 'green'];
    public const TYPE_LABELS = [
        self::TYPE_WATER => 'Nawadnianie',
        self::TYPE_SHAPE => 'Cięcie i formowanie',
        self::TYPE_CARE => 'Pielęgnacja',
    ];

    /** 24 sekki: [miesiąc, dzień rozpoczęcia, kanji, romaji, polska nazwa, kategoria pory roku]. */
    private const SEKKI = [
        [2, 4, '立春', 'Risshun', 'Początek wiosny', 'spring'],
        [2, 19, '雨水', 'Usui', 'Deszczowa woda', 'spring'],
        [3, 6, '啓蟄', 'Keichitsu', 'Przebudzenie owadów', 'spring'],
        [3, 21, '春分', 'Shunbun', 'Równonoc wiosenna', 'spring'],
        [4, 5, '清明', 'Seimei', 'Czysta jasność', 'spring'],
        [4, 20, '穀雨', 'Kokuu', 'Deszcz zbóż', 'spring'],
        [5, 5, '立夏', 'Rikka', 'Początek lata', 'summer'],
        [5, 21, '小満', 'Shōman', 'Małe pełnienie', 'summer'],
        [6, 6, '芒種', 'Bōshu', 'Kłosy zbóż', 'summer'],
        [6, 21, '夏至', 'Geshi', 'Przesilenie letnie', 'summer'],
        [7, 7, '小暑', 'Shōsho', 'Małe upały', 'summer'],
        [7, 23, '大暑', 'Taisho', 'Wielkie upały', 'summer'],
        [8, 7, '立秋', 'Risshū', 'Początek jesieni', 'autumn'],
        [8, 23, '処暑', 'Shosho', 'Kres upałów', 'autumn'],
        [9, 8, '白露', 'Hakuro', 'Biała rosa', 'autumn'],
        [9, 23, '秋分', 'Shūbun', 'Równonoc jesienna', 'autumn'],
        [10, 8, '寒露', 'Kanro', 'Zimna rosa', 'autumn'],
        [10, 23, '霜降', 'Sōkō', 'Opadanie szronu', 'autumn'],
        [11, 7, '立冬', 'Rittō', 'Początek zimy', 'winter'],
        [11, 22, '小雪', 'Shōsetsu', 'Mały śnieg', 'winter'],
        [12, 7, '大雪', 'Taisetsu', 'Wielki śnieg', 'winter'],
        [12, 22, '冬至', 'Tōji', 'Przesilenie zimowe', 'winter'],
        [1, 5, '小寒', 'Shōkan', 'Mały chłód', 'winter'],
        [1, 20, '大寒', 'Daikan', 'Wielki chłód', 'winter'],
    ];

    private const SEASON_BLURBS = [
        'spring' => 'Soki ruszają, a pąki pękają: czas przesadzania, drutowania i pierwszych nawozów.',
        'summer' => 'Pierwsze żary i żywy wzrost pędów: pilnuj podlewania, osłony przed południowym słońcem i pinczowania.',
        'autumn' => 'Drzewa gromadzą zapasy: ogranicz azot, przygotuj stanowisko do zimowania i kontroluj druty.',
        'winter' => 'Okres spoczynku: rzadkie podlewanie, ochrona korzeni przed mrozem i planowanie cięcia strukturalnego.',
    ];

    /** Okna zabiegowe: [początek m-d, koniec m-d, tytuł, gatunek, opis]. Okno może przechodzić przez Nowy Rok. */
    private const WINDOWS = [
        ['03-01', '04-10', 'Przesadzanie klonów i wiązów', 'Acer palmatum, Ulmus parvifolia', 'Przesadzaj, zanim pąki zaczną się rozwijać.'],
        ['04-11', '05-09', 'Drutowanie młodych pędów', 'Juniperus chinensis (jałowiec Shimpaku)', 'Zakładaj drut, zanim pędy zdrewnieją.'],
        ['05-10', '06-05', 'Mekiri: wycinanie świec sosny', 'Pinus thunbergii (sosna czarna)', 'Skracanie tegorocznych świec wyrównuje wigor korony.'],
        ['06-06', '08-19', 'Letnie pinczowanie i osłona od słońca', 'Acer palmatum, Zelkova serrata', 'Czas intensywnego wzrostu: skracaj pędy i osłaniaj liście przed południowym słońcem.'],
        ['08-20', '10-24', 'Drutowanie jesienne iglastych', 'Pinus thunbergii, Juniperus chinensis', 'Drutuj iglaste przed spoczynkiem zimowym.'],
        ['10-25', '11-30', 'Przygotowanie do zimowania', 'cała kolekcja', 'Zabezpiecz donice i stanowisko przed pierwszymi przymrozkami.'],
        ['12-01', '02-28', 'Cięcie strukturalne w spoczynku', 'drzewa liściaste', 'Tnij gałęzie strukturalne, dopóki drzewa są w spoczynku.'],
    ];

    /** Zasada miesiąca: [tytuł (miejscownik w UI), treść]. */
    private const TIPS = [
        1 => 'Nie podlewaj zamarzniętej bryły: poczekaj, aż podłoże odmarznie, i chroń korzenie w donicach przed przemarznięciem.',
        2 => 'Nie przesadzaj przed ruszeniem pąków; cięcie strukturalne wykonuj tylko w suche, bezmroźne dni.',
        3 => 'Nie nawoź drzew przed rozwinięciem pierwszych liści: zimne korzenie nie pobiorą składników.',
        4 => 'Nie wystawiaj świeżo przesadzonych drzew na pełne słońce i wiatr; chroń młode liście klonów.',
        5 => 'Nie przycinaj pędów klonów w pełnym słońcu: grozi to wyciekiem soków i zaschnięciem kambium. Tnij wczesnym rankiem lub po zmierzchu.',
        6 => 'Nie podlewaj w upalne południe zimną wodą. Rób to rano lub wieczorem, najlepiej odstaną deszczówką.',
        7 => 'Nie dopuszczaj do przesuszenia bryły w upały; na parapecie południowym sprawdzaj wilgotność dwa razy dziennie.',
        8 => 'Nie stosuj nawozów z dużą ilością azotu: pędy nie zdążą zdrewnieć przed jesienią.',
        9 => 'Nie wykonuj mocnych cięć na drzewach iglastych; ogranicz nawożenie i kontroluj wrastanie drutu.',
        10 => 'Nie zwlekaj z przygotowaniem stanowiska zimowego: drzewa liściaste potrzebują chłodu, ale nie mrozu poniżej -8°C.',
        11 => 'Nie trzymaj drzew liściastych w ciepłym mieszkaniu: spoczynek zimowy jest niezbędny do wiosennego wzrostu.',
        12 => 'Nie nawoź w okresie spoczynku i nie podlewaj „na kalendarz”. Sprawdzaj wilgotność przed każdym podlaniem.',
    ];

    public function __construct(private readonly CollectionService $collection)
    {
    }

    /**
     * Zadania na wskazany dzień (reguły + własne), z oznaczeniem wykonania.
     *
     * @param array<string, mixed> $state stan użytkownika
     * @return list<array<string, mixed>>
     */
    public function tasksFor(int $userId, DateTimeImmutable $date, array $state): array
    {
        $trees = $this->collection->all($userId);
        $today = PolishDate::today();
        $isToday = $date->format('Y-m-d') === $today->format('Y-m-d');
        $tasks = $this->generate($date, $trees, $isToday);

        foreach ($state['customTasks'] as $custom) {
            if (($custom['date'] ?? '') !== $date->format('Y-m-d')) {
                continue;
            }
            $tasks[] = [
                'key' => 'custom-' . $custom['id'],
                'type' => in_array($custom['type'] ?? '', array_keys(self::TYPE_TONES), true) ? $custom['type'] : self::TYPE_CARE,
                'urgent' => false,
                'time' => (string) ($custom['time'] ?? 'Cały dzień'),
                'title' => (string) $custom['title'],
                'text' => (string) ($custom['note'] ?? ''),
                'treeId' => isset($custom['treeId']) ? (int) $custom['treeId'] : null,
                'meta' => 'Własny zabieg',
                'note' => '',
                'custom' => true,
                'sort' => 40,
            ];
        }

        usort($tasks, static fn (array $a, array $b): int => [$b['urgent'], $a['sort'], $a['title']] <=> [$a['urgent'], $b['sort'], $b['title']]);

        $done = $state['tasksDone'];
        foreach ($tasks as &$task) {
            $task['id'] = $date->format('Y-m-d') . ':' . $task['key'];
            $task['done'] = in_array($task['id'], $done, true);
            $task['date'] = $date->format('Y-m-d');
        }
        unset($task);

        return $tasks;
    }

    /**
     * Zadania jednego okazu na najbliższe dni (od dziś), pogrupowane po dniach; dni bez zadań są pomijane.
     *
     * @param array<string, mixed> $state
     * @return list<array{date: string, label: string, isToday: bool, tasks: list<array<string, mixed>>}>
     */
    public function forTree(int $userId, int $treeId, array $state, int $days = 14): array
    {
        $today = PolishDate::today();
        $groups = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $today->add(new DateInterval('P' . $i . 'D'));
            $tasks = array_values(array_filter(
                $this->tasksFor($userId, $date, $state),
                static fn (array $task): bool => ($task['treeId'] ?? null) === $treeId,
            ));
            if ($tasks === []) {
                continue;
            }

            $groups[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $i === 0 ? 'Dziś' : ($i === 1 ? 'Jutro' : PolishDate::full($date)),
                'isToday' => $i === 0,
                'tasks' => $tasks,
            ];
        }

        return $groups;
    }

    /**
     * Tydzień (pon–ndz) zawierający podaną datę, z kropkami rodzajów zabiegów.
     *
     * @param array<string, mixed> $state
     * @return array{number: int, range: string, prev: string, next: string, days: list<array<string, mixed>>}
     */
    public function week(int $userId, DateTimeImmutable $selected, array $state): array
    {
        $monday = $selected->sub(new DateInterval('P' . ((int) $selected->format('N') - 1) . 'D'));
        $today = PolishDate::today()->format('Y-m-d');
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $monday->add(new DateInterval('P' . $i . 'D'));
            $tasks = $this->tasksFor($userId, $date, $state);
            $types = array_values(array_unique(array_column($tasks, 'type')));
            usort($types, static fn (string $a, string $b): int => array_search($a, array_keys(self::TYPE_TONES), true) <=> array_search($b, array_keys(self::TYPE_TONES), true));

            $days[] = [
                'date' => $date->format('Y-m-d'),
                'label' => PolishDate::dayShort($date),
                'name' => PolishDate::dayName($date),
                'day' => (int) $date->format('j'),
                'tones' => array_map(static fn (string $type): string => self::TYPE_TONES[$type], $types),
                'total' => count($tasks),
                'open' => count(array_filter($tasks, static fn (array $t): bool => !$t['done'])),
                'isToday' => $date->format('Y-m-d') === $today,
                'isSelected' => $date->format('Y-m-d') === $selected->format('Y-m-d'),
            ];
        }

        $sunday = $monday->add(new DateInterval('P6D'));

        return [
            'number' => (int) $selected->format('W'),
            'range' => PolishDate::dayMonth($monday) . ' – ' . PolishDate::dayMonth($sunday),
            'prev' => $monday->sub(new DateInterval('P7D'))->format('Y-m-d'),
            'next' => $monday->add(new DateInterval('P7D'))->format('Y-m-d'),
            'days' => $days,
        ];
    }

    /**
     * Najbliższe zabiegi w ciągu 14 dni od dziś (bez podlewania). Najpierw formowanie, nawożenie i własne
     * zabiegi, a rutynowe zraszanie tylko jako uzupełnienie (raz na drzewko), żeby nie zalewało listy.
     *
     * @param array<string, mixed> $state
     * @return list<array<string, mixed>>
     */
    public function upcoming(int $userId, array $state, int $limit = 3): array
    {
        $today = PolishDate::today();
        $main = [];
        $routine = [];
        $seen = [];

        for ($i = 1; $i <= 14; $i++) {
            $date = $today->add(new DateInterval('P' . $i . 'D'));
            foreach ($this->tasksFor($userId, $date, $state) as $task) {
                if ($task['type'] === self::TYPE_WATER || isset($seen[$task['key']])) {
                    continue;
                }
                $seen[$task['key']] = true;

                $event = [
                    'tone' => self::TYPE_TONES[$task['type']],
                    'title' => $task['title'],
                    'text' => $task['meta'],
                    'in' => PolishDate::inDays($i),
                    'date' => PolishDate::dayMonth($date),
                    'iso' => $date->format('Y-m-d'),
                ];

                if (str_starts_with($task['key'], 'mist-')) {
                    $routine[] = $event;
                } else {
                    $main[] = $event;
                }
            }
        }

        return array_slice([...$main, ...$routine], 0, $limit);
    }

    /**
     * Bieżąca pora roku wg 24 sekki.
     *
     * @return array<string, mixed>
     */
    public function sekki(DateTimeImmutable $date): array
    {
        $year = (int) $date->format('Y');
        $starts = [];

        foreach (self::SEKKI as $index => $entry) {
            // Sekki 23–24 (styczeń) należą do poprzedniego „roku sekki”, więc sprawdzamy trzy kolejne lata.
            foreach ([$year - 1, $year, $year + 1] as $candidateYear) {
                $start = DateTimeImmutable::createFromFormat('!Y-n-j', $candidateYear . '-' . $entry[0] . '-' . $entry[1], $date->getTimezone());
                $starts[] = ['start' => $start, 'index' => $index];
            }
        }

        usort($starts, static fn (array $a, array $b): int => $a['start'] <=> $b['start']);

        $current = null;
        $next = null;
        foreach ($starts as $position => $item) {
            if ($item['start'] <= $date) {
                $current = $item;
                $next = $starts[$position + 1] ?? null;
            }
        }

        $entry = self::SEKKI[$current['index']];
        $following = self::SEKKI[($current['index'] + 1) % 24];
        $length = max(1, (int) $current['start']->diff($next['start'])->days);
        $dayNumber = (int) $current['start']->diff($date)->days + 1;

        return [
            'number' => $current['index'] + 1,
            'kanji' => $entry[2],
            'romaji' => $entry[3],
            'name' => $entry[4],
            'blurb' => self::SEASON_BLURBS[$entry[5]],
            'month' => PolishDate::month($date) . ' ' . $date->format('Y'),
            'dayNumber' => $dayNumber,
            'length' => $length,
            'next' => $following[3],
        ];
    }

    /**
     * Aktualne (lub najbliższe) okno zabiegowe.
     *
     * @return array<string, mixed>
     */
    public function window(DateTimeImmutable $date): array
    {
        $best = null;

        foreach (self::WINDOWS as [$from, $to, $title, $species, $text]) {
            foreach ([-1, 0, 1] as $shift) {
                $year = (int) $date->format('Y') + $shift;
                $start = DateTimeImmutable::createFromFormat('!Y-m-d', $year . '-' . $from, $date->getTimezone());
                $endYear = $to < $from ? $year + 1 : $year;
                $end = DateTimeImmutable::createFromFormat('!Y-m-d', $endYear . '-' . $to, $date->getTimezone());

                if ($start <= $date && $date <= $end) {
                    $days = (int) $date->diff($end)->days;

                    return [
                        'state' => 'open',
                        'days' => $days,
                        'title' => $title,
                        'species' => $species,
                        'text' => 'Okno trwa do ' . PolishDate::dayMonth($end) . '. ' . $text,
                    ];
                }

                if ($start > $date && ($best === null || $start < $best['start'])) {
                    $best = ['start' => $start, 'title' => $title, 'species' => $species, 'text' => $text];
                }
            }
        }

        $days = (int) $date->diff($best['start'])->days;

        return [
            'state' => 'soon',
            'days' => $days,
            'title' => $best['title'],
            'species' => $best['species'],
            'text' => 'Okno otwiera się ' . PolishDate::dayMonth($best['start']) . '. ' . $best['text'],
        ];
    }

    /**
     * @return array{title: string, text: string}
     */
    public function tip(DateTimeImmutable $date): array
    {
        return [
            'title' => 'Czego unikać w ' . mb_strtolower(PolishDate::monthLocative($date)) . '?',
            'text' => self::TIPS[(int) $date->format('n')],
            'eyebrow' => 'Zasada miesiąca',
        ];
    }

    /**
     * Zadania wynikające z reguł pielęgnacji.
     *
     * @param list<array<string, mixed>> $trees drzewka po nałożeniu stanu
     * @return list<array<string, mixed>>
     */
    private function generate(DateTimeImmutable $date, array $trees, bool $isToday): array
    {
        $tasks = [];
        $dayIndex = (int) floor($date->getTimestamp() / 86400);
        $weekday = (int) $date->format('N');
        $dayOfMonth = (int) $date->format('j');

        foreach ($trees as $tree) {
            $thirsty = in_array($tree['status'], ['attention', 'critical'], true);
            $interval = $thirsty ? 1 : 2 + ((int) $tree['id'] % 2);

            if ((($dayIndex + (int) $tree['id']) % $interval) === 0 || ($isToday && $thirsty)) {
                $tasks[] = [
                    'key' => 'water-' . $tree['id'],
                    'type' => self::TYPE_WATER,
                    'urgent' => $isToday && $thirsty,
                    'time' => $isToday && $thirsty ? 'Przed 11:30' : ((int) $tree['id'] % 2 === 0 ? 'Popołudnie' : 'Wieczorem'),
                    'title' => 'Podlanie: ' . $tree['name'] . ' (' . $tree['commonName'] . ')',
                    'text' => match ($tree['status']) {
                        'critical' => 'Podłoże przeschnięte w rdzeniu: kąpiel zanurzeniowa przez około 12 minut.',
                        'attention' => 'Niska wilgotność: kąpiel podsiąkowa w odstanej deszczówce przez 15 minut.',
                        default => 'Sprawdź wilgotność patyczkiem i podlej, gdy górny centymetr podłoża przeschnie.',
                    },
                    'treeId' => $tree['id'],
                    'meta' => $tree['species'] . ' #' . sprintf('%02d', $tree['id']) . ' • ' . $tree['ageLabel'],
                    'note' => 'Wilgotność ' . $tree['moisture'] . '%',
                    'sort' => $thirsty ? 5 : 20 + (int) $tree['id'],
                ];
            }

            if ($tree['actionType'] === 'adjust' && $weekday === 5) {
                $tasks[] = [
                    'key' => 'wire-' . $tree['id'],
                    'type' => self::TYPE_SHAPE,
                    'urgent' => false,
                    'time' => 'Popołudnie',
                    'title' => 'Kontrola wcinania drutu: ' . $tree['name'],
                    'text' => 'Gałąź boczna poddana silnym naprężeniom; sprawdź przyrost kalusa i w razie potrzeby zdejmij drut.',
                    'treeId' => $tree['id'],
                    'meta' => $tree['species'] . ' • Kontrola drutu',
                    'note' => '',
                    'sort' => 15,
                ];
            }

            if ($tree['id'] === 1 && ($dayOfMonth === 15 || $dayOfMonth === 16)) {
                $tasks[] = [
                    'key' => 'candles-' . $tree['id'],
                    'type' => self::TYPE_SHAPE,
                    'urgent' => false,
                    'time' => 'Rano',
                    'title' => 'Uszczykiwanie świeczek: ' . $tree['name'],
                    'text' => 'Wyrównanie wigoru: najpierw silne świeczki u góry, po 5 dniach dolne partie.',
                    'treeId' => $tree['id'],
                    'meta' => $tree['species'] . ' • Mekiri',
                    'note' => 'Palcami, nie nożyczkami',
                    'sort' => 12,
                ];
            }

            if ($tree['category'] === 'deciduous' && $dayOfMonth === 12) {
                $tasks[] = [
                    'key' => 'defoliation-' . $tree['id'],
                    'type' => self::TYPE_SHAPE,
                    'urgent' => false,
                    'time' => 'Wczesny ranek',
                    'title' => 'Defoliacja selektywna: ' . $tree['name'],
                    'text' => 'Redukcja wielkości liści w zewnętrznej partii korony; wykonuj poza pełnym słońcem.',
                    'treeId' => $tree['id'],
                    'meta' => $tree['species'] . ' • Redukcja liści',
                    'note' => 'Nożyczki Satsuki',
                    'sort' => 13,
                ];
            }

            if ($tree['category'] === 'conifer' && in_array($weekday, [1, 3, 6], true)) {
                $tasks[] = [
                    'key' => 'mist-' . $tree['id'],
                    'type' => self::TYPE_CARE,
                    'urgent' => false,
                    'time' => 'Rano',
                    'title' => 'Zraszanie korony: ' . $tree['name'],
                    'text' => 'Delikatne zraszanie igieł miękką wodą poprawia turgor i zmywa kurz.',
                    'treeId' => $tree['id'],
                    'meta' => $tree['species'] . ' • Zraszanie',
                    'note' => 'Bez zwilżania pnia',
                    'sort' => 30 + (int) $tree['id'],
                ];
            }
        }

        if ($dayIndex % 14 === 3) {
            $tasks[] = [
                'key' => 'feed',
                'type' => self::TYPE_CARE,
                'urgent' => false,
                'time' => 'Przedpołudnie',
                'title' => 'Nawożenie organiczne',
                'text' => 'Rozmieść koszyczki z granulatem wokół krawędzi donic u wszystkich dojrzałych okazów.',
                'treeId' => null,
                'meta' => 'Wszystkie dojrzałe okazy liściaste i iglaste',
                'note' => '',
                'sort' => 18,
            ];
        }

        return $tasks;
    }
}
