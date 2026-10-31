<?php
$e = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

$styles = [
    ['number' => '01', 'kanji' => '直幹', 'icon' => 'north', 'name' => 'Chokkan', 'subtitle' => 'Pionowy formalny', 'value' => 92, 'active' => false],
    ['number' => '02', 'kanji' => '模様木', 'icon' => 'show_chart', 'name' => 'Moyogi', 'subtitle' => 'Pionowy swobodny', 'value' => 75, 'active' => true],
    ['number' => '03', 'kanji' => '斜幹', 'icon' => 'trending_up', 'name' => 'Shakan', 'subtitle' => 'Pochylony grawitacyjnie', 'value' => 60, 'active' => false],
    ['number' => '04', 'kanji' => '懸崖', 'icon' => 'south_east', 'name' => 'Kengai', 'subtitle' => 'Pełna kaskada skalna', 'value' => 40, 'active' => false],
    ['number' => '05', 'kanji' => '吹流し', 'icon' => 'air', 'name' => 'Fukinagashi', 'subtitle' => 'Smagane wiatrem wybrzeża', 'value' => 85, 'active' => false],
];

$styleMetrics = [
    ['label' => 'Wierzchołek (Tenjin)', 'value' => 'Pochylenie 8° do przodu', 'tone' => ''],
    ['label' => 'Podstawa (Nebari)', 'value' => 'Promieniste 360°', 'tone' => ''],
    ['label' => 'Dominanta (Sashi-eda)', 'value' => '1/3 wysokości pnia', 'tone' => 'terra'],
];

$styleErrors = [
    ['term' => 'Kuruma-eda (Koła szprychowe):', 'text' => 'Więcej niż jedna gałąź wyrastająca z tej samej wysokości pnia.'],
    ['term' => 'Kaeru-mata (Żabie nogi):', 'text' => "Symetryczne rozgałęzienie 'Y' rozbijające dynamikę stożka."],
    ['term' => 'Kannonuki (Gałąź ryglująca):', 'text' => 'Gałąź rosnąca wprost ku widzowi na linii wzroku.'],
];

$speciesTabs = [
    ['label' => 'Sosna<br>Gęstokwiatowa', 'active' => true],
    ['label' => 'Klon<br>Japoński', 'active' => false],
    ['label' => 'Jałowiec<br>Chiński', 'active' => false],
    ['label' => 'Azalia<br>Satsuki', 'active' => false],
];

$months = ['STY', 'LUT', 'MAR', 'KWI', 'MAJ', 'CZE', 'LIP', 'SIE', 'WRZ', 'PAŹ', 'LIS', 'GRU'];
$currentMonth = 9;

// Cell keys: month index => cell variant (see .studio-gantt__cell--* in studio.css).
$ganttRows = [
    [
        'icon' => 'content_cut',
        'tone' => 'terra',
        'title' => 'Cięcie strukturalne (Grube konary)',
        'note' => 'Najlepiej w okresie uśpienia (XII - II)',
        'cells' => [0 => 'dark', 1 => 'dark', 9 => 'window', 10 => 'muted', 11 => 'dark'],
    ],
    [
        'icon' => 'spa',
        'tone' => 'green',
        'title' => 'Uszczykiwanie pędów (Mekiri / Candling)',
        'note' => 'Równoważenie wigoru (VI - VII)',
        'cells' => [4 => 'pale', 5 => 'green', 6 => 'green', 9 => 'window'],
    ],
    [
        'icon' => 'change_circle',
        'tone' => 'terra',
        'title' => 'Przerzedzanie igieł / Prześwietlanie pąków',
        'note' => 'Jesienna selekcja (X - XI)',
        'cells' => [9 => 'terra', 10 => 'terra'],
    ],
    [
        'icon' => 'all_inclusive',
        'tone' => 'dark',
        'title' => 'Czas bezpiecznego drutowania (Wymóg braku soku)',
        'note' => 'Późna jesień i zima (X - III)',
        'cells' => [0 => 'safe', 1 => 'safe', 2 => 'soft', 9 => 'safe', 10 => 'safe', 11 => 'safe'],
    ],
];

$balanceIndicators = [
    ['label' => 'Równowaga Optyczna (Nebari vs Korona):', 'value' => '74%', 'percent' => 74, 'tone' => 'dark'],
    ['label' => 'Penetracja Światła do Pąków Wewnętrznych:', 'value' => '48%', 'percent' => 48, 'tone' => 'green'],
    ['label' => 'Ryzyko Odumierania Pnia (Cofanie soku):', 'value' => 'Niskie (12%)', 'percent' => 12, 'tone' => 'terra'],
];

$simulatorMetrics = [
    ['label' => 'Gęstość liści', 'value' => '100%', 'tone' => ''],
    ['label' => 'Doświetlenie pnia', 'value' => 'Średnie (42%)', 'tone' => ''],
    ['label' => 'Przepływ soku', 'value' => 'Zbalansowany', 'tone' => 'green'],
    ['label' => 'Wycena estetyczna', 'value' => '68 / 100', 'tone' => 'terra'],
];

$caseStudies = [
    [
        'image' => '/images/wiring-copper-branch-2.jpg',
        'alt' => 'Gałąź sosny owinięta podwójnym splotem drutu miedzianego',
        'tag' => 'Pinus thunbergii · 48 lat',
        'tagTone' => 'dark',
        'category' => 'Drutowanie miedzią #8',
        'title' => 'Formowanie Gałęzi Pierwszorzędnej (Moyogi)',
        'text' => 'Zastosowanie podwójnego splotu dla obniżenia kąta gałęzi o 32 stopnie w kierunku podłoża bez pęknięcia kambium.',
    ],
    [
        'image' => '/images/bonsai-table-scissors.jpg',
        'alt' => 'Klon japoński po defoliacji na drewnianym stole z nożycami',
        'tag' => 'Acer palmatum · 24 lata',
        'tagTone' => 'terra',
        'category' => 'Defoliacja sezonowa',
        'title' => 'Redukcja Blaszki Liściowej i Drugi Wypust',
        'text' => 'Efekt kontrolowanego usuwania 80% ulistnienia w czerwcu. Powstanie mikrorozgałęzień międzywęźli o długości poniżej 8 mm.',
    ],
    [
        'image' => '/images/bonsai-jin-portrait.jpg',
        'alt' => 'Jałowiec chiński z wyrzeźbionym martwym drewnem Jin',
        'tag' => 'Juniperus chinensis · 85 lat',
        'tagTone' => 'muted',
        'category' => 'Rzeźbienie martwego drewna',
        'title' => 'Transformacja Zbędnego Konaru w Jin',
        'text' => 'Ściągnięcie kory szczypcami Jin i zabezpieczenie polysiarczkiem wapna (Jin Seal) dające kontrast z żywą wstęgą pnia.',
    ],
];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <?php include __DIR__ . '/partials/head.html.php'; ?>
    <link rel="stylesheet" href="<?= $asset('/css/pages/studio.css') ?>">
</head>
<body>
    <div class="app">
        <?php include __DIR__ . '/partials/app-sidebar.html.php'; ?>

        <div class="app-main">
            <?php include __DIR__ . '/partials/app-topbar.html.php'; ?>

            <main class="app-content studio">
                <!-- Hero -->
                <section class="studio-hero" aria-labelledby="studio-title">
                    <div class="studio-hero__content">
                        <p class="studio-hero__eyebrow">
                            <span class="studio-hero__seal" lang="ja" aria-hidden="true">造</span>
                            <span class="eyebrow">Warsztat Mistrza · Zōkei · Formowanie Pędów</span>
                        </p>
                        <h1 class="studio-hero__title" id="studio-title">Studio Formowania, Cięcia i Drutowania</h1>
                        <p class="studio-hero__lead">Praktyka <em>wabi-sabi</em> w harmonii z fizjologią rośliny. Narzędzie analityczne chroniące tkanki kambium przed martwicą, zapobiegające wrastaniu stopów miedzi i modelujące równowagę energetyczną korony.</p>
                        <ul class="studio-hero__tools" aria-label="Narzędzia studia">
                            <li class="studio-hero__tool"><span class="ms" aria-hidden="true">verified</span>Protokół Nippon Bonsai Kyokai</li>
                            <li class="studio-hero__tool"><span class="ms" aria-hidden="true">speed</span>Kalkulator Sił Naciągu Drutu</li>
                            <li class="studio-hero__tool"><span class="ms" aria-hidden="true">contact_support</span>Wizualizator Tachiagari</li>
                        </ul>
                    </div>
                    <svg class="studio-hero__art" viewBox="0 0 260 260" aria-hidden="true" focusable="false">
                        <circle cx="130" cy="130" r="122" fill="none" stroke="currentColor" stroke-width="5" stroke-dasharray="11 8"/>
                        <path class="studio-hero__leaf" d="M130 32 C 96 84, 92 168, 130 228 C 136 170, 138 90, 130 32 Z"/>
                        <path class="studio-hero__stem" d="M122 116 C 160 92, 202 100, 222 132" fill="none" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </section>

                <!-- Moduł 01: Kanony estetyki -->
                <section class="studio-section" aria-labelledby="studio-styles-title">
                    <header class="studio-head">
                        <div class="studio-head__main">
                            <p class="studio-kicker"><span class="studio-kicker__module">Moduł 01</span><span class="dot dot--terra" aria-hidden="true"></span><span class="studio-kicker__topic">Kanony Estetyki</span></p>
                            <h2 class="studio-head__title" id="studio-styles-title">Klasyfikacja Pięciu Form Klasycznych</h2>
                        </div>
                        <p class="studio-head__desc studio-head__desc--styles">Wybierz styl, aby załadować kanoniczne proporcje kątowe gałęzi, wektory przepływu energii oraz geometrię punktu równowagi korony.</p>
                    </header>

                    <ul class="studio-styles" aria-label="Style klasyczne">
                        <?php foreach ($styles as $style): ?>
                            <li>
                                <a href="/studio" class="studio-style<?= $style['active'] ? ' is-active' : '' ?>"<?= $style['active'] ? ' aria-current="true"' : '' ?>>
                                    <span class="studio-style__top">
                                        <span class="studio-style__number"><?= $e($style['number']) ?> / <span lang="ja"><?= $e($style['kanji']) ?></span></span>
                                        <span class="ms" aria-hidden="true"><?= $e($style['icon']) ?></span>
                                    </span>
                                    <span class="studio-style__name"><?= $e($style['name']) ?></span>
                                    <span class="studio-style__subtitle"><?= $e($style['subtitle']) ?></span>
                                    <span class="studio-style__bar" style="--value: <?= (int) $style['value'] ?>%" aria-hidden="true"></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <article class="card studio-detail" aria-labelledby="studio-detail-title">
                        <figure class="studio-detail__visual">
                            <div class="studio-detail__chips">
                                <span class="studio-chip studio-chip--style"><span lang="ja">模様木</span> · Moyogi</span>
                                <span class="studio-chip studio-chip--green">Balans: Trójkąt Asymetryczny</span>
                                <span class="studio-chip studio-chip--plain"><span class="ms" aria-hidden="true">straighten</span>Kątomierz Gałęzi</span>
                            </div>
                            <svg class="studio-detail__diagram" viewBox="24 48 446 412" role="img" aria-label="Diagram stylu Moyogi: trójkąt asymetryczny wierzchołka i gałęzi">
                                <polygon points="248,158 122,322 366,266" fill="#fe9e77" fill-opacity="0.08"/>
                                <line x1="246" y1="150" x2="246" y2="394" stroke="#c2c8c1" stroke-width="1" stroke-dasharray="3 3"/>
                                <line x1="111" y1="382" x2="383" y2="382" stroke="#727973" stroke-width="1.5"/>
                                <path d="M214 382 C 234 378, 240 362, 246 348 C 252 362, 258 378, 278 382 Z" fill="#486554"/>
                                <path d="M244 268 C 290 256, 330 262, 362 266" fill="none" stroke="#1e3a2b" stroke-width="4" stroke-linecap="round"/>
                                <path d="M240 236 L 294 214" fill="none" stroke="#8e9a92" stroke-width="3" stroke-linecap="round"/>
                                <ellipse cx="302" cy="213" rx="19" ry="6.5" fill="#b7c4ba"/>
                                <path d="M232 314 C 200 316, 168 318, 136 318" fill="none" stroke="#1e3a2b" stroke-width="4" stroke-linecap="round"/>
                                <path d="M246 352 C 224 322, 226 290, 240 262 C 256 232, 258 200, 248 160" fill="none" stroke="#1e3a2b" stroke-width="12" stroke-linecap="round"/>
                                <ellipse cx="126" cy="320" rx="27" ry="10.5" fill="#3d5a4a"/>
                                <ellipse cx="132" cy="316" rx="17" ry="6" fill="#4d6b5a"/>
                                <ellipse cx="362" cy="266" rx="32" ry="12" fill="#3d5a4a"/>
                                <ellipse cx="352" cy="262" rx="21" ry="7" fill="#4d6b5a"/>
                                <ellipse cx="248" cy="156" rx="34" ry="13" fill="#1e3a2b"/>
                                <ellipse cx="253" cy="151" rx="24" ry="8" fill="#2f4d3d"/>
                                <g fill="none" stroke="#fe9e77" stroke-width="1" stroke-dasharray="3 3">
                                    <line x1="248" y1="158" x2="122" y2="322"/>
                                    <line x1="248" y1="158" x2="366" y2="266"/>
                                    <line x1="122" y1="322" x2="366" y2="266"/>
                                    <path d="M206 316 A 10 10 0 0 0 214 306"/>
                                </g>
                                <circle cx="248" cy="158" r="2.5" fill="#e8efe9"/>
                                <text x="196" y="306" class="studio-svg-label studio-svg-label--terra">82°</text>
                                <text x="313" y="254" class="studio-svg-label studio-svg-label--muted">Uke-eda (+15°)</text>
                            </svg>
                            <figcaption class="studio-metrics studio-metrics--three">
                                <?php foreach ($styleMetrics as $metric): ?>
                                    <span class="studio-metric">
                                        <span class="studio-metric__label"><?= $e($metric['label']) ?></span>
                                        <span class="studio-metric__value<?= $metric['tone'] !== '' ? ' studio-metric__value--' . $e($metric['tone']) : '' ?>"><?= $e($metric['value']) ?></span>
                                    </span>
                                <?php endforeach; ?>
                            </figcaption>
                        </figure>

                        <div class="studio-detail__info">
                            <h3 class="studio-detail__title" id="studio-detail-title">Moyogi (Drzewo Faliste) <span class="badge badge--terra studio-detail__badge">Popularny</span></h3>
                            <p class="studio-detail__text">Styl odwzorowujący drzewa z nizin i lasów, które walcząc o światło wykształciły sinusoidalny pień. Kluczowa zasada: kolejne wygięcia pnia muszą maleć ku górze, a wierzchołek zawsze wraca pionowo ponad nasadę korzeniową (środek ciężkości).</p>

                            <div class="studio-errors">
                                <h4 class="studio-label">Błędy dyskwalifikujące styl (Imi-eda):</h4>
                                <ul class="studio-errors__list">
                                    <?php foreach ($styleErrors as $error): ?>
                                        <li class="studio-errors__item">
                                            <span class="ms" aria-hidden="true">do_not_disturb_on</span>
                                            <p><strong><?= $e($error['term']) ?></strong> <?= $e($error['text']) ?></p>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <div class="studio-species">
                                <h4 class="studio-label">Polecane gatunki dla tego stylu:</h4>
                                <ul class="studio-species__list">
                                    <li class="studio-tag">Pinus densiflora (Sosna)</li>
                                    <li class="studio-tag">Acer palmatum (Klon)</li>
                                    <li class="studio-tag">Juniperus chinensis (Jałowiec)</li>
                                </ul>
                            </div>

                            <button type="button" class="btn btn--primary btn--block studio-detail__cta"><span class="ms" aria-hidden="true">tune</span>Wczytaj styl do Symulatora Cięcia</button>
                        </div>
                    </article>
                </section>

                <!-- Moduł 02: Kalendarz sezonowości -->
                <section class="studio-section" aria-labelledby="studio-calendar-title">
                    <header class="studio-head studio-head--center">
                        <div class="studio-head__main">
                            <p class="studio-kicker"><span class="studio-kicker__module">Moduł 02</span><span class="dot dot--terra" aria-hidden="true"></span><span class="studio-kicker__topic">Fizjologia &amp; Okna Czasowe</span></p>
                            <h2 class="studio-head__title studio-head__title--calendar" id="studio-calendar-title">Kalendarz Sezonowości Cięcia i Zabiegów</h2>
                        </div>
                        <div class="studio-tabs" role="group" aria-label="Grupa gatunków">
                            <?php foreach ($speciesTabs as $tab): ?>
                                <button type="button" class="studio-tabs__tab<?= $tab['active'] ? ' is-active' : '' ?>" aria-pressed="<?= $tab['active'] ? 'true' : 'false' ?>"><?= $tab['label'] ?></button>
                            <?php endforeach; ?>
                        </div>
                    </header>

                    <article class="card studio-calendar" aria-labelledby="studio-species-title">
                        <header class="studio-calendar__head">
                            <span class="icon-tile icon-tile--green studio-calendar__icon"><span class="ms" aria-hidden="true">park</span></span>
                            <div>
                                <h3 class="studio-calendar__title" id="studio-species-title">Sosna Gęstokwiatowa (Pinus densiflora / Akamatsu)</h3>
                                <p class="studio-calendar__subtitle">Gatunek iglasty dwuigielny o silnej apikalnej dominacji pąków szczytowych.</p>
                            </div>
                        </header>
                        <p class="studio-calendar__now"><span class="studio-calendar__now-dot" aria-hidden="true"></span>Bieżący miesiąc: Październik (Stan spoczynku / Drutowanie)</p>

                        <div class="studio-gantt" role="region" aria-label="Wykres sezonowości zabiegów" tabindex="0">
                            <div class="studio-gantt__inner">
                                <ol class="studio-gantt__months" aria-hidden="true">
                                    <?php foreach ($months as $index => $month): ?>
                                        <li<?= $index === $currentMonth ? ' class="is-current"' : '' ?>><?= $e($month) ?></li>
                                    <?php endforeach; ?>
                                </ol>
                                <?php foreach ($ganttRows as $row): ?>
                                    <div class="studio-gantt__row">
                                        <div class="studio-gantt__label">
                                            <h4 class="studio-gantt__title"><span class="ms studio-gantt__icon studio-gantt__icon--<?= $e($row['tone']) ?>" aria-hidden="true"><?= $e($row['icon']) ?></span><?= $e($row['title']) ?></h4>
                                            <span class="studio-gantt__note"><?= $e($row['note']) ?></span>
                                        </div>
                                        <ol class="studio-gantt__track">
                                            <?php foreach ($months as $index => $month): ?>
                                                <?php $variant = $row['cells'][$index] ?? 'empty'; ?>
                                                <li class="studio-gantt__cell studio-gantt__cell--<?= $e($variant) ?><?= $index === $currentMonth ? ' is-current' : '' ?>" title="<?= $e($month) ?>">
                                                    <?php if ($variant === 'safe'): ?><span class="ms" aria-hidden="true">check</span><?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ol>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <aside class="studio-calendar__alert">
                            <span class="ms" aria-hidden="true">warning</span>
                            <p><strong>Krytyczne ostrzeżenie na Październik:</strong> Drewno twardnieje przed przymrozkami. Zastosuj maść gojącą z hormonem kalusa (Kiyonal) na każde cięcie powyżej 4 mm średnicy, by zapobiec wyciekowi żywicy.</p>
                            <button type="button" class="btn btn--secondary studio-calendar__print"><span class="ms" aria-hidden="true">print</span>Drukuj Kartę Zabiegów</button>
                        </aside>
                    </article>
                </section>

                <!-- Moduł 03: Drutowanie -->
                <section class="studio-section" aria-labelledby="studio-wiring-title">
                    <header class="studio-head">
                        <div class="studio-head__main">
                            <p class="studio-kicker"><span class="studio-kicker__module">Moduł 03</span><span class="dot dot--terra" aria-hidden="true"></span><span class="studio-kicker__topic">Inżynieria Gięcia</span></p>
                            <h2 class="studio-head__title" id="studio-wiring-title">Zasady Bezpiecznego Drutowania Bez Wrastania</h2>
                        </div>
                        <p class="studio-head__desc studio-head__desc--wiring">Precyzyjne wyliczenie siły naciągu i kąta splotu chroni tkankę przewodzącą florę i ksylem przed zduszeniem.</p>
                    </header>

                    <div class="studio-wiring">
                        <form class="card studio-calc" action="/studio" method="get" aria-labelledby="studio-calc-title">
                            <header class="studio-calc__head">
                                <span class="ms" aria-hidden="true">calculate</span>
                                <h3 class="studio-card-title" id="studio-calc-title">Kalkulator Grubości Drutu</h3>
                                <span class="studio-calc__rule">Reguła 1/3 (D3 Rule)</span>
                            </header>

                            <fieldset class="studio-calc__alloys">
                                <legend class="studio-calc__legend">Typ stopu przewodnika:</legend>
                                <label class="studio-alloy">
                                    <input type="radio" name="alloy" value="aluminium" checked>
                                    <span class="studio-alloy__text">
                                        <span class="studio-alloy__name">Aluminium</span>
                                        <span class="studio-alloy__hint">Drzewa liściaste</span>
                                    </span>
                                </label>
                                <label class="studio-alloy">
                                    <input type="radio" name="alloy" value="copper">
                                    <span class="studio-alloy__text">
                                        <span class="studio-alloy__name">Miedź Wyżarzana</span>
                                        <span class="studio-alloy__hint">Iglaki / Sztywny opór</span>
                                    </span>
                                </label>
                            </fieldset>

                            <div class="studio-range">
                                <div class="studio-range__head">
                                    <label for="studio-diameter" class="studio-range__label">Średnica gałęzi w punkcie naciągu:</label>
                                    <output for="studio-diameter" class="studio-range__value studio-range__value--terra">12.0 mm</output>
                                </div>
                                <input class="studio-range__input" type="range" id="studio-diameter" name="diameter" min="2" max="35" step="0.5" value="12" style="--value: 30%">
                                <div class="studio-range__scale" aria-hidden="true">
                                    <span>2 mm (młody pęd)</span>
                                    <span>18 mm (konar)</span>
                                    <span>35 mm (gruba nasada)</span>
                                </div>
                            </div>

                            <dl class="studio-result">
                                <div class="studio-result__main">
                                    <dt>Rekomendowany przekrój:</dt>
                                    <dd>
                                        <span class="studio-result__size">4.0 mm</span>
                                        <span class="studio-result__alloy">Anodyzowane Aluminium</span>
                                    </dd>
                                </div>
                                <div class="studio-result__row">
                                    <dt>Maks. czas na roślinie:</dt>
                                    <dd>6-9 miesięcy</dd>
                                </div>
                                <div class="studio-result__row">
                                    <dt>Metoda kotwienia:</dt>
                                    <dd>Podwójna spirala (Jin Anchor)</dd>
                                </div>
                            </dl>

                            <p class="studio-calc__warning"><span class="ms" aria-hidden="true">timer</span><span><strong>Monitoruj co 14 dni:</strong> W fazie czerwcowej kambium przyrasta trzykrotnie szybciej. W razie wrastania rozetnij drut kleszczami <em>Shinji</em> — nigdy go nie odwijaj ręcznie!</span></p>
                        </form>

                        <article class="card studio-angle" aria-labelledby="studio-angle-title">
                            <header class="studio-angle__head">
                                <div>
                                    <h3 class="studio-card-title" id="studio-angle-title">Interaktywny Wzorzec Kąta Owijania (Kanon 45°)</h3>
                                    <p class="studio-angle__text">Sprawdź zachowanie splotu przy zbyt ostrym lub zbyt łagodnym nachyleniu spirali.</p>
                                </div>
                                <span class="studio-angle__badge">Kąt optymalny (45°)</span>
                            </header>

                            <svg class="studio-angle__diagram" viewBox="0 0 467 222" role="img" aria-label="Drut owinięty wokół gałęzi pod kątem 45 stopni">
                                <rect x="45" y="88" width="377" height="48" fill="#dfd7cf"/>
                                <line x1="45" y1="88" x2="422" y2="88" stroke="#5f6661" stroke-width="1.5"/>
                                <line x1="45" y1="136" x2="422" y2="136" stroke="#5f6661" stroke-width="1.5"/>
                                <text x="58" y="116" class="studio-svg-label studio-svg-label--wood">DREWNO KSZTAŁTOWANE (TWARDZIEJ)</text>
                                <g fill="none" stroke="#a7aca6" stroke-width="5" stroke-dasharray="2.5 2.5">
                                    <?php for ($i = 0; $i < 4; $i++): ?>
                                        <line x1="<?= 120 + 75 * $i ?>" y1="88" x2="<?= 146 + 75 * $i ?>" y2="136"/>
                                    <?php endfor; ?>
                                </g>
                                <g stroke="#1e3a2b" stroke-width="7" stroke-linecap="round">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <line x1="<?= 71 + 75 * $i ?>" y1="136" x2="<?= 120 + 75 * $i ?>" y2="88"/>
                                    <?php endfor; ?>
                                </g>
                                <line x1="195" y1="88" x2="243" y2="88" stroke="#944929" stroke-width="1.5" stroke-dasharray="2 2"/>
                                <text x="219" y="79" text-anchor="middle" class="studio-svg-label studio-svg-label--angle">45°</text>
                            </svg>

                            <div class="studio-range">
                                <div class="studio-range__head">
                                    <label for="studio-angle" class="studio-range__label">Regulacja Kąta Przebiegu Drutu:</label>
                                    <output for="studio-angle" class="studio-range__value">45 stopni</output>
                                </div>
                                <input class="studio-range__input" type="range" id="studio-angle" name="angle" min="15" max="75" step="1" value="45" style="--value: 50%">
                                <p class="studio-range__legend">
                                    <span class="studio-range__risk">15° (Ryzyko: uduszenie wiązek)</span>
                                    <span class="studio-range__ideal">45° (Idealna mechanika sił)</span>
                                    <span class="studio-range__weak">75° (Brak siły trzymania gałęzi)</span>
                                </p>
                            </div>

                            <p class="studio-angle__note"><span class="ms" aria-hidden="true">check_circle</span><span><strong>Kąt prawidłowy:</strong> Zapewnia optymalne przełożenie wektora gięcia bez punktowego nacisku na wiązki sitowe.</span></p>
                        </article>
                    </div>
                </section>

                <!-- Moduł 04: Symulator balansu -->
                <section class="studio-section" aria-labelledby="studio-simulator-title">
                    <header class="studio-head">
                        <div class="studio-head__main">
                            <p class="studio-kicker"><span class="studio-kicker__module">Moduł 04</span><span class="dot dot--terra" aria-hidden="true"></span><span class="studio-kicker__topic">Wirtualny Warsztat</span></p>
                            <h2 class="studio-head__title studio-head__title--wide" id="studio-simulator-title">Symulator Balansu Korony · Nebari i Tachiagari</h2>
                        </div>
                        <p class="studio-head__desc studio-head__desc--simulator">Kliknij gałęzie drzewka, aby zasymulować ich usunięcie. Obserwuj w czasie rzeczywistym przesunięcie środka ciężkości, dystrybucję energii i estetykę linii pnia.</p>
                    </header>

                    <article class="card studio-sim" aria-label="Symulator balansu korony">
                        <div class="studio-sim__stage">
                            <div class="studio-sim__toolbar">
                                <p class="studio-sim__model"><span class="dot dot--green" aria-hidden="true"></span>Model: Jałowiec Shin-Moyogi #204</p>
                                <div class="studio-sim__actions">
                                    <button type="button" class="studio-sim__reset"><span class="ms" aria-hidden="true">refresh</span>Reset cięć</button>
                                    <span class="studio-sim__mode">Tryb Aktywny</span>
                                </div>
                            </div>

                            <div class="studio-sim__canvas">
                                <svg class="studio-sim__tree" viewBox="24 48 520 510" role="img" aria-label="Model drzewka z czterema numerowanymi gałęziami do wirtualnego cięcia">
                                    <ellipse cx="284" cy="442" rx="140" ry="12" fill="#1b1c19" fill-opacity="0.12"/>
                                    <polygon points="152,452 416,452 404,468 164,468" fill="#6b5446"/>
                                    <rect x="130" y="441" width="308" height="9" rx="2" fill="#3d2a22"/>
                                    <path d="M258 396 C 262 420, 238 438, 206 446 L 362 446 C 330 438, 290 420, 278 396 Z" fill="#2c4637"/>
                                    <path d="M262 404 L 208 444" stroke="#1e3a2b" stroke-width="6" stroke-linecap="round"/>
                                    <path d="M298 410 L 360 444" stroke="#1e3a2b" stroke-width="6" stroke-linecap="round"/>
                                    <path d="M272 164 C 271 200, 272 226, 266 252 C 256 276, 244 296, 244 330 C 244 360, 252 386, 260 402 L 276 402 C 280 380, 280 360, 282 330 C 284 300, 300 270, 300 232 C 300 200, 298 180, 298 164 Z" fill="#1e3a2b"/>
                                    <path d="M274 262 C 262 290, 256 330, 260 370 C 270 340, 276 300, 274 262 Z" fill="#0b130f" stroke="#c5c7c1" stroke-width="3" stroke-linejoin="round"/>
                                    <g fill="none" stroke="#1e3a2b" stroke-linecap="round">
                                        <path d="M250 350 C 210 350, 170 352, 136 354" stroke-width="8"/>
                                        <path d="M272 340 C 310 344, 350 350, 384 352" stroke-width="8"/>
                                        <path d="M262 286 C 236 274, 210 264, 184 258" stroke-width="7"/>
                                        <path d="M292 250 C 330 244, 360 238, 392 234" stroke-width="7"/>
                                    </g>
                                    <ellipse cx="98" cy="358" rx="46" ry="17.6" fill="#1e3a2b"/>
                                    <ellipse cx="112" cy="352" rx="34" ry="11" fill="#31503f"/>
                                    <ellipse cx="418" cy="354" rx="41.6" ry="16" fill="#1e3a2b"/>
                                    <ellipse cx="157" cy="256" rx="32" ry="13.6" fill="#476553"/>
                                    <ellipse cx="428" cy="230" rx="44" ry="16" fill="#1e3a2b"/>
                                    <ellipse cx="416" cy="226" rx="30" ry="10" fill="#31503f"/>
                                    <ellipse cx="284" cy="148" rx="56" ry="17.6" fill="#1e3a2b"/>
                                    <ellipse cx="294" cy="142" rx="42" ry="12.8" fill="#31503f"/>
                                    <line x1="284" y1="150" x2="284" y2="448" stroke="#e8907a" stroke-width="1" stroke-dasharray="4 3"/>
                                    <circle cx="278.4" cy="298.4" r="6.4" fill="#f08a65" stroke="#ffffff" stroke-width="1.5"/>
                                    <g class="studio-sim__markers">
                                        <circle cx="94.4" cy="358" r="12.8"/><text x="94.4" y="362.5">1</text>
                                        <circle cx="416" cy="352.8" r="12.8"/><text x="416" y="357.3">2</text>
                                        <circle cx="156" cy="256.8" r="12.8"/><text x="156" y="261.3">3</text>
                                        <circle cx="426.4" cy="229.6" r="12.8"/><text x="426.4" y="234.1">4</text>
                                    </g>
                                </svg>
                                <p class="studio-sim__hint"><span class="ms" aria-hidden="true">touch_app</span>Kliknij numerowaną gałąź (1-4), aby dokonać wirtualnego cięcia.</p>
                            </div>

                            <div class="studio-metrics studio-metrics--four">
                                <?php foreach ($simulatorMetrics as $metric): ?>
                                    <span class="studio-metric">
                                        <span class="studio-metric__label"><?= $e($metric['label']) ?></span>
                                        <span class="studio-metric__value<?= $metric['tone'] !== '' ? ' studio-metric__value--' . $e($metric['tone']) : '' ?>"><?= $e($metric['value']) ?></span>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <aside class="studio-sim__panel" aria-labelledby="studio-balance-title">
                            <h3 class="studio-sim__title" id="studio-balance-title"><span class="ms" aria-hidden="true">analytics</span>Wskaźniki Balansu (In &amp; Yo)</h3>
                            <p class="studio-sim__intro">W japońskiej estetyce cięcie nie jest stratą, lecz otwarciem przestrzeni negatywnej (Ma).</p>

                            <ul class="studio-indicators">
                                <?php foreach ($balanceIndicators as $indicator): ?>
                                    <li class="studio-indicator">
                                        <span class="studio-indicator__head">
                                            <span><?= $e($indicator['label']) ?></span>
                                            <span class="studio-indicator__value studio-indicator__value--<?= $e($indicator['tone']) ?>"><?= $e($indicator['value']) ?></span>
                                        </span>
                                        <span class="studio-indicator__track"><span class="studio-indicator__bar studio-indicator__bar--<?= $e($indicator['tone']) ?>" style="--value: <?= (int) $indicator['percent'] ?>%"></span></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="studio-advice">
                                <h4 class="studio-advice__title"><span class="ms" aria-hidden="true">tips_and_updates</span>Rekomendacja Mistrza:</h4>
                                <p>Gałąź #2 tworzy klasyczny błąd Kannonuki (gałąź przeciwległa na tej samej wysokości co gałąź główna #1). Rekomendowane jej całkowite usunięcie lub przekształcenie w suchy sęk Jin.</p>
                            </div>

                            <div class="studio-sim__buttons">
                                <button type="button" class="btn btn--primary studio-sim__button"><span class="ms" aria-hidden="true">bookmark_add</span><span>Zapisz Plan Cięcia w Dzienniku Drzewka</span></button>
                                <button type="button" class="btn btn--tonal studio-sim__button"><span class="ms" aria-hidden="true">brush</span><span>Konwertuj cięcie na martwe drewno (Jin/Shari)</span></button>
                            </div>
                        </aside>
                    </article>
                </section>

                <!-- Archiwum: ewolucja okazów -->
                <section class="studio-section" aria-labelledby="studio-cases-title">
                    <header class="studio-head studio-head--end">
                        <div class="studio-head__main">
                            <p class="studio-kicker"><span class="studio-kicker__module">Archiwum Mistrzowskie</span><span class="dot dot--terra" aria-hidden="true"></span><span class="studio-kicker__topic">Przykłady Referencyjne</span></p>
                            <h2 class="studio-head__title studio-head__title--wide" id="studio-cases-title">Ewolucja Okazów po Cięciu Strukturalnym</h2>
                        </div>
                        <a href="/studio" class="studio-head__link">Zobacz pełne studia przypadków<span class="ms" aria-hidden="true">arrow_forward</span></a>
                    </header>

                    <div class="studio-cases">
                        <?php foreach ($caseStudies as $case): ?>
                            <article class="studio-case">
                                <div class="studio-case__media">
                                    <img src="<?= $e($case['image']) ?>" alt="<?= $e($case['alt']) ?>" loading="lazy" width="512" height="390">
                                    <span class="studio-case__tag studio-case__tag--<?= $e($case['tagTone']) ?>"><?= $e($case['tag']) ?></span>
                                </div>
                                <div class="studio-case__body">
                                    <p class="studio-case__category"><?= $e($case['category']) ?></p>
                                    <h3 class="studio-case__title"><?= $e($case['title']) ?></h3>
                                    <p class="studio-case__text"><?= $e($case['text']) ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/partials/app-bottom-nav.html.php'; ?>
    <?php include __DIR__ . '/partials/foot.html.php'; ?>
</body>
</html>
