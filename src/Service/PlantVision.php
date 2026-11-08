<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Ocena kondycji drzewka ze zdjęcia. Model wizyjny w Ollama (usługa `ollama`) odpowiada tylko „tak/nie” na konkretne
 * pytania o to, co widać (barwa, struktura liści, podłoże, nalot…). Przyczynę (przesuszenie, przelanie, szkodniki…)
 * wyciąga z tych odpowiedzi reguła w `diagnose()`, więc wynik da się wyjaśnić, a polskie teksty pochodzą z szablonów.
 *
 * @phpstan-type Analysis array{
 *     plantVisible: bool,
 *     condition: string,
 *     vitality: int,
 *     cause: string,
 *     confidence: string,
 *     summary: string,
 *     observations: list<string>,
 *     actions: list<string>,
 *     verify: string
 * }
 */
final class PlantVision
{
    public const CAUSES = [
        'healthy' => 'Brak widocznych problemów',
        'underwatering' => 'Podejrzenie przesuszenia',
        'overwatering' => 'Podejrzenie przelania',
        'pests_or_disease' => 'Podejrzenie szkodników lub choroby',
        'light_or_heat' => 'Podejrzenie poparzenia słońcem lub upału',
        'nutrients' => 'Podejrzenie niedoboru składników',
        'unclear' => 'Przyczyna niejasna',
    ];

    public const CONDITIONS = ['good', 'watch', 'warning', 'critical'];
    public const CONFIDENCE = ['low' => 'niska', 'medium' => 'średnia', 'high' => 'wysoka'];

    private const FOCUS = [
        'leaves' => 'the foliage or needles',
        'bark' => 'the trunk and bark',
        'roots' => 'the soil surface and the roots',
        'wiring' => 'the wire on the branches and the bark under it',
    ];

    private const TIMEOUT = 150;

    /**
     * Pytania do modelu w stałej kolejności (numer pytania = pozycja + 1). `blurry` i `plant` nie liczą się do przyczyn.
     */
    private const QUESTIONS = [
        'plant' => 'Is the main subject a plant, tree or bonsai (a close-up of its leaves counts)?',
        'yellow' => 'Do many leaves or needles look yellow?',
        'pale' => 'Are the leaves pale or washed out compared with a healthy green?',
        'brown' => 'Do many leaves or needles look brown or burnt?',
        'crisp' => 'Do leaves look dry, brittle, papery or shrivelled?',
        'limp' => 'Do leaves look limp, drooping or soft?',
        'mushy' => 'Are there black or dark brown soft, mushy parts?',
        'tips' => 'Does yellowing or browning start at the tips or edges of needles and leaves?',
        'inner' => 'Does it start on the older inner or lower leaves?',
        'drop' => 'Has the plant lost many leaves, or are branches bare?',
        'soilDry' => 'Is soil visible and light coloured, dry or cracked?',
        'soilWet' => 'Is soil visible and dark, soaked, or covered with moss or mould?',
        'trunkRot' => 'Is the base of the trunk dark, soft or rotting?',
        'coating' => 'Is there a white, grey or rusty powdery coating, or ringed spots, on the leaves?',
        'insects' => 'Are there insects, webbing or sticky scale bumps?',
        'oneSide' => 'Is the damage only on the side facing the light or sun?',
        'blurry' => 'Is the photo too blurry, dark or distant to judge the leaves?',
    ];

    /**
     * Odpowiedź „tak” na pytanie: punkty dla przyczyn (p), waga uszkodzenia (s) i opis do pokazania (t).
     *
     * @var array<string, array{p: array<string, int>, s: int, t: string}>
     */
    private const SIGNALS = [
        'yellow' => ['p' => ['overwatering' => 1, 'nutrients' => 1], 's' => 2, 't' => 'Wiele liści lub igieł żółknie'],
        'pale' => ['p' => ['nutrients' => 2], 's' => 1, 't' => 'Zieleń jest blada i wyblakła'],
        'brown' => ['p' => ['underwatering' => 1], 's' => 3, 't' => 'Wiele liści lub igieł jest brązowych lub przypalonych'],
        'crisp' => ['p' => ['underwatering' => 3], 's' => 2, 't' => 'Liście lub igły są suche, kruche i papierowe'],
        'limp' => ['p' => ['overwatering' => 2, 'underwatering' => 1], 's' => 2, 't' => 'Liście są miękkie i oklapnięte'],
        'mushy' => ['p' => ['overwatering' => 3], 's' => 3, 't' => 'Są czarne lub ciemnobrązowe, miękkie fragmenty'],
        'tips' => ['p' => ['underwatering' => 1], 's' => 1, 't' => 'Przebarwienia zaczynają się od końców igieł i brzegów liści'],
        'inner' => ['p' => ['overwatering' => 2], 's' => 1, 't' => 'Przebarwienia zaczynają się od starszych, wewnętrznych lub dolnych liści'],
        'drop' => ['p' => [], 's' => 2, 't' => 'Drzewko straciło dużo liści albo ma gołe gałęzie'],
        'soilDry' => ['p' => ['underwatering' => 3], 's' => 1, 't' => 'Podłoże jest jasne, suche lub spękane'],
        'soilWet' => ['p' => ['overwatering' => 3], 's' => 1, 't' => 'Podłoże jest ciemne, przemoczone albo porośnięte mchem lub pleśnią'],
        'trunkRot' => ['p' => ['overwatering' => 3], 's' => 2, 't' => 'Podstawa pnia jest ciemna i miękka'],
        'coating' => ['p' => ['pests_or_disease' => 4], 's' => 2, 't' => 'Na liściach widać mączysty nalot albo plamy z obwódką'],
        'insects' => ['p' => ['pests_or_disease' => 4], 's' => 2, 't' => 'Widać owady, pajęczynki lub lepkie tarczki'],
        'oneSide' => ['p' => ['light_or_heat' => 3], 's' => 2, 't' => 'Uszkodzenia są tylko po stronie nasłonecznionej'],
    ];

    private const ACTIONS = [
        'healthy' => [
            'Nie zmieniaj obecnej pielęgnacji',
            'Podlewaj, gdy wierzch podłoża zaczyna jaśnieć',
            'Powtórz skan za dwa tygodnie, żeby wychwycić zmiany',
        ],
        'underwatering' => [
            'Zanurz doniczkę w wodzie na 10-15 minut, aż przestaną wychodzić bąbelki',
            'Na 2-3 dni odstaw drzewko z pełnego słońca i wiatru',
            'Nie przycinaj suchych gałązek od razu, część może się odbić',
            'Odtąd podlewaj, gdy wierzch podłoża zaczyna jaśnieć',
            'Nie nawoź, dopóki drzewko się nie odbije',
        ],
        'overwatering' => [
            'Wstrzymaj podlewanie, aż podłoże przeschnie na 1-2 cm w głąb',
            'Upewnij się, że woda swobodnie wycieka przez otwory w doniczce',
            'Zdejmij drzewko z podstawki i postaw w jasnym, przewiewnym miejscu',
            'Nie nawoź, dopóki drzewko się nie odbije',
        ],
        'pests_or_disease' => [
            'Odizoluj drzewko od innych roślin',
            'Obejrzyj lupą spód liści i gałązek, zbierz szkodniki ręcznie',
            'Usuń mocno porażone liście i wyrzuć je poza kompost',
            'Powtórz oględziny po tygodniu',
        ],
        'light_or_heat' => [
            'W najgorętszych godzinach daj drzewku cień lub rozproszone światło',
            'Podlewaj rano i sprawdzaj podłoże dwa razy dziennie w upał',
            'Zostaw poparzone liście, aż pojawi się nowy przyrost',
            'Obracaj drzewko co kilka dni, żeby słońce padało równomiernie',
        ],
        'nutrients' => [
            'Podlewaj wodą odstaną lub miękką, bez wapnia',
            'Zastosuj nawóz do bonsai z żelazem i mikroelementami',
            'Nawóz podawaj na wilgotne podłoże, w połowie dawki z etykiety',
            'Przesadź drzewko, jeśli podłoże nie było wymieniane od 2-3 lat',
        ],
        'unclear' => [
            'Wbij patyczek lub palec na 2-3 cm w podłoże i oceń wilgotność',
            'Podlej dopiero wtedy, gdy podłoże jest suche na głębokość palca',
            'Zrób ostre zdjęcie liści i wierzchu podłoża w świetle dziennym',
            'Za 2-3 dni powtórz skan i porównaj wyniki',
        ],
    ];

    private const VERIFY = [
        'healthy' => '',
        'underwatering' => 'Wbij patyczek na 5 cm w podłoże. Jeśli wyjdzie suchy i jasny, drzewko jest przesuszone.',
        'overwatering' => 'Wbij patyczek na 5 cm w podłoże. Jeśli wyjdzie ciemny, mokry i oblepiony ziemią, podłoże jest przelane.',
        'pests_or_disease' => 'Potrząśnij gałązką nad białą kartką: drobne, poruszające się kropki to szkodniki.',
        'light_or_heat' => 'Sprawdź, czy uszkodzenia są tylko po stronie nasłonecznionej.',
        'nutrients' => 'Sprawdź, czy młode liście są blade z zielonymi nerwami, a podłoże ma normalną wilgotność.',
        'unclear' => 'Weź doniczkę w ręce: lekka oznacza suche podłoże, ciężka i chłodna mokre.',
    ];

    private const SUMMARY = [
        'underwatering' => 'Objawy wskazują na przesuszenie',
        'overwatering' => 'Objawy wskazują na przelanie',
        'pests_or_disease' => 'Na zdjęciu widać ślady szkodników lub choroby',
        'light_or_heat' => 'Uszkodzenia wyglądają na poparzenie słońcem lub upał',
        'nutrients' => 'Objawy przypominają niedobór składników pokarmowych',
    ];

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $model,
    ) {
    }

    public static function fromEnvironment(): self
    {
        $url = getenv('OLLAMA_URL');
        $model = getenv('VISION_MODEL');

        return new self(
            rtrim($url !== false && $url !== '' ? $url : 'http://ollama:11434', '/'),
            $model !== false && $model !== '' ? $model : 'qwen2.5vl:7b',
        );
    }

    /**
     * Pokazuje zdjęcie modelowi, zbiera odpowiedzi „tak/nie” i zamienia je na ocenę.
     *
     * @return Analysis
     */
    public function analyze(string $imagePath, string $part): array
    {
        $image = @file_get_contents($imagePath);
        if ($image === false || $image === '') {
            throw new VisionException('Nie udało się odczytać zdjęcia do analizy.');
        }

        $focus = self::FOCUS[$part] ?? self::FOCUS['leaves'];
        $reply = $this->chat([
            'model' => $this->model,
            'stream' => false,
            'keep_alive' => '15m',
            'options' => ['temperature' => 0, 'seed' => 7],
            'messages' => [[
                'role' => 'user',
                'content' => self::prompt($focus),
                'images' => [base64_encode($image)],
            ]],
        ]);

        $text = (string) ($reply['message']['content'] ?? '');
        $answers = self::parseAnswers($text);
        if (!isset($answers['plant'])) {
            error_log('PlantVision: model returned unusable answers: ' . substr($text, 0, 300));
            throw new VisionException('Model nie zwrócił czytelnego wyniku. Spróbuj z ostrzejszym zdjęciem.');
        }

        return self::diagnose($answers);
    }

    /**
     * Zamienia odpowiedzi „tak/nie” na ocenę: przyczyna, powaga, pewność oraz polskie opisy i zalecenia.
     *
     * @param array<string, bool> $answers pytanie (klucz z QUESTIONS) => odpowiedź
     * @return Analysis
     */
    public static function diagnose(array $answers): array
    {
        if (($answers['plant'] ?? false) !== true) {
            return [
                'plantVisible' => false,
                'condition' => 'watch',
                'vitality' => 0,
                'cause' => 'unclear',
                'confidence' => 'low',
                'summary' => 'Na zdjęciu nie widać wyraźnie drzewka, więc nie ma czego oceniać.',
                'observations' => [],
                'actions' => ['Zrób zbliżenie liści lub igieł w świetle dziennym i powtórz skan'],
                'verify' => '',
            ];
        }

        $scores = array_fill_keys(['underwatering', 'overwatering', 'pests_or_disease', 'light_or_heat', 'nutrients'], 0);
        $severity = 0;
        $found = [];
        foreach (self::SIGNALS as $id => $signal) {
            if (($answers[$id] ?? false) !== true) {
                continue;
            }
            $found[] = $signal['t'];
            $severity += $signal['s'];
            foreach ($signal['p'] as $cause => $points) {
                $scores[$cause] += $points;
            }
        }

        arsort($scores);
        $causes = array_keys($scores);
        $top = $scores[$causes[0]];
        $margin = $top - $scores[$causes[1]];

        if ($found === []) {
            $cause = 'healthy';
        } elseif ($top >= 2 && $margin >= 2) {
            $cause = $causes[0];
        } else {
            $cause = 'unclear';
        }

        $blurry = ($answers['blurry'] ?? false) === true;
        $confidence = match (true) {
            $blurry, $cause === 'unclear' => 'low',
            $cause === 'healthy' => 'medium',
            $top >= 6 && $margin >= 4 => 'high',
            $top >= 4 => 'medium',
            default => 'low',
        };

        $observations = $found === []
            ? ['Liście lub igły są zielone i jędrne, bez przebarwień', 'Nie widać nalotu, plam ani szkodników']
            : array_slice($found, 0, 5);
        $actions = self::actions($cause, $answers);
        if ($blurry) {
            $observations[] = 'Zdjęcie jest nieostre, ciemne lub zbyt dalekie, więc wynik jest mniej pewny';
            $actions[] = 'Powtórz skan na ostrzejszym zdjęciu w świetle dziennym';
        }

        return [
            'plantVisible' => true,
            'condition' => $cause === 'healthy' ? 'good' : ($severity <= 2 ? 'watch' : ($severity <= 5 ? 'warning' : 'critical')),
            'vitality' => $cause === 'healthy' ? 92 : max(5, 88 - $severity * 7),
            'cause' => $cause,
            'confidence' => $confidence,
            'summary' => self::summary($cause, $found, $scores),
            'observations' => array_slice($observations, 0, 6),
            'actions' => array_slice($actions, 0, 5),
            'verify' => self::VERIFY[$cause],
        ];
    }

    /**
     * Czyści i ogranicza dane oceny przysłane z formularza; null, gdy brakuje wymaganych pól.
     *
     * @param array<mixed> $raw
     * @return Analysis|null
     */
    public static function normalize(array $raw): ?array
    {
        if (!isset($raw['plantVisible'], $raw['condition'], $raw['cause'])) {
            return null;
        }

        $cause = (string) $raw['cause'];
        $condition = (string) $raw['condition'];
        $confidence = (string) ($raw['confidence'] ?? 'low');
        if (!isset(self::CAUSES[$cause]) || !in_array($condition, self::CONDITIONS, true)) {
            return null;
        }

        return [
            'plantVisible' => (bool) $raw['plantVisible'],
            'condition' => $condition,
            'vitality' => max(0, min(100, (int) ($raw['vitality'] ?? 0))),
            'cause' => $cause,
            'confidence' => isset(self::CONFIDENCE[$confidence]) ? $confidence : 'low',
            'summary' => self::text($raw['summary'] ?? '', 320),
            'observations' => self::list($raw['observations'] ?? [], 6, 160),
            'actions' => self::list($raw['actions'] ?? [], 5, 80),
            'verify' => self::text($raw['verify'] ?? '', 220),
        ];
    }

    private static function prompt(string $focus): string
    {
        $lines = [];
        $number = 1;
        foreach (self::QUESTIONS as $question) {
            $lines[] = $number++ . '. ' . $question;
        }

        return "Look closely at this photo, which mainly shows {$focus}. Answer each question with yes or no, then a few words of evidence. "
            . "Answer only from what is visible; if something is not visible, answer no.\n"
            . implode("\n", $lines)
            . "\nReply with " . count($lines) . ' numbered lines.';
    }

    /**
     * @return array<string, bool> klucz pytania => odpowiedź; pytania bez czytelnej odpowiedzi są pomijane
     */
    private static function parseAnswers(string $text): array
    {
        $ids = array_keys(self::QUESTIONS);
        $answers = [];
        if (preg_match_all('/^\s*(\d{1,2})\s*[.)]\s*\**\s*(yes|no)\b/imu', $text, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as [, $number, $answer]) {
                $id = $ids[(int) $number - 1] ?? null;
                if ($id !== null) {
                    $answers[$id] = strtolower($answer) === 'yes';
                }
            }
        }

        return $answers;
    }

    /**
     * @param array<string, bool> $answers
     * @return list<string>
     */
    private static function actions(string $cause, array $answers): array
    {
        $actions = self::ACTIONS[$cause];

        if ($cause === 'pests_or_disease') {
            $treatment = ($answers['coating'] ?? false) === true && ($answers['insects'] ?? false) !== true
                ? 'Opryskaj preparatem grzybobójczym, bez moczenia liści przy podlewaniu'
                : 'Opryskaj preparatem na danego szkodnika, najlepiej wieczorem';
            array_splice($actions, 2, 0, [$treatment]);
        }

        if ($cause === 'overwatering') {
            if (($answers['soilWet'] ?? false) === true) {
                array_splice($actions, 3, 0, ['Zbierz mech i pleśń z powierzchni podłoża']);
            }
            if (($answers['trunkRot'] ?? false) === true || ($answers['mushy'] ?? false) === true) {
                array_splice($actions, 3, 0, ['Przy czarnych, miękkich korzeniach przesadź w świeży, przepuszczalny grys']);
            }
        }

        return $actions;
    }

    /**
     * @param list<string> $found
     * @param array<string, int> $scores
     */
    private static function summary(string $cause, array $found, array $scores): string
    {
        if ($cause === 'healthy') {
            return 'Na zdjęciu nie widać objawów przesuszenia, przelania ani szkodników.';
        }

        if ($cause === 'unclear') {
            return $scores['underwatering'] >= 2 && $scores['overwatering'] >= 2
                ? 'Objawy są sprzeczne: pasują zarówno do przesuszenia, jak i do przelania. Sprawdź podłoże, zanim podlejesz.'
                : 'Zdjęcie nie pozwala wskazać przyczyny. Sprawdź podłoże i powtórz skan z lepszym ujęciem.';
        }

        $reasons = array_map(
            static fn (string $text): string => mb_strtolower(mb_substr($text, 0, 1)) . mb_substr($text, 1),
            array_slice($found, 0, 3),
        );

        return self::SUMMARY[$cause] . ($reasons === [] ? '.' : ': ' . implode('; ', $reasons) . '.');
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function chat(array $payload): array
    {
        $curl = curl_init($this->baseUrl . '/api/chat');
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => self::TIMEOUT,
        ]);

        $body = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($body === false) {
            error_log('PlantVision: Ollama request failed: ' . $error);
            throw new VisionException('Analiza zdjęcia jest teraz niedostępna. Spróbuj za chwilę.');
        }

        if ($status === 404) {
            error_log("PlantVision: model {$this->model} is not pulled; run `docker compose exec ollama ollama pull {$this->model}`");
            throw new VisionException('Model do analizy zdjęć nie jest jeszcze pobrany.');
        }

        $reply = json_decode((string) $body, true);
        if ($status !== 200 || !is_array($reply)) {
            error_log("PlantVision: Ollama answered HTTP {$status}: " . substr((string) $body, 0, 300));
            throw new VisionException('Analiza zdjęcia nie powiodła się. Spróbuj ponownie.');
        }

        return $reply;
    }

    private static function text(mixed $value, int $max): string
    {
        $text = preg_replace('/[\p{Cc}\s]+/u', ' ', is_scalar($value) ? (string) $value : '') ?? '';

        return mb_substr(trim($text), 0, $max);
    }

    /**
     * @return list<string>
     */
    private static function list(mixed $value, int $limit, int $max): array
    {
        if (!is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $item) {
            $text = self::text($item, $max);
            if (mb_strlen($text) >= 3) {
                $items[] = $text;
            }
            if (count($items) === $limit) {
                break;
            }
        }

        return $items;
    }
}
