<?php
$e = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');



$months = ['STY', 'LUT', 'MAR', 'KWI', 'MAJ', 'CZE', 'LIP', 'SIE', 'WRZ', 'PAŹ', 'LIS', 'GRU'];
$currentMonth = $calendar['currentMonth'];

$balanceIndicators = [
    ['key' => 'balance', 'label' => 'Równowaga Optyczna (Nebari vs Korona):', 'value' => '74%', 'percent' => 74, 'tone' => 'dark'],
    ['key' => 'light', 'label' => 'Penetracja Światła do Pąków Wewnętrznych:', 'value' => '48%', 'percent' => 48, 'tone' => 'green'],
    ['key' => 'risk', 'label' => 'Ryzyko Odumierania Pnia (Cofanie soku):', 'value' => 'Niskie (12%)', 'percent' => 12, 'tone' => 'terra'],
];

$simulatorMetrics = [
    ['key' => 'density', 'label' => 'Gęstość liści', 'value' => '100%', 'tone' => ''],
    ['key' => 'trunk', 'label' => 'Doświetlenie pnia', 'value' => 'Średnie (42%)', 'tone' => ''],
    ['key' => 'sap', 'label' => 'Przepływ soku', 'value' => 'Zbalansowany', 'tone' => 'green'],
    ['key' => 'aesthetic', 'label' => 'Wycena estetyczna', 'value' => '68 / 100', 'tone' => 'terra'],
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
                        <?php foreach ($styles as $item): ?>
                            <li>
                                <a href="/studio?style=<?= $e($item['id']) ?>&amp;species=<?= $e($calendar['id']) ?>#studio-styles-title" class="studio-style<?= $item['active'] ? ' is-active' : '' ?>" data-style="<?= $e($item['id']) ?>"<?= $item['active'] ? ' aria-current="true"' : '' ?>>
                                    <span class="studio-style__top">
                                        <span class="studio-style__number"><?= $e($item['number']) ?> / <span lang="ja"><?= $e($item['kanji']) ?></span></span>
                                        <span class="ms" aria-hidden="true"><?= $e($item['icon']) ?></span>
                                    </span>
                                    <span class="studio-style__name"><?= $e($item['name']) ?></span>
                                    <span class="studio-style__subtitle"><?= $e($item['subtitle']) ?></span>
                                    <span class="studio-style__bar" style="--value: <?= (int) $item['value'] ?>%" aria-hidden="true"></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <article class="card studio-detail" aria-labelledby="studio-detail-title">
                        <figure class="studio-detail__visual">
                            <div class="studio-detail__chips">
                                <span class="studio-chip studio-chip--style"><span lang="ja"><?= $e($style['kanji']) ?></span> · <?= $e($style['name']) ?></span>
                                <span class="studio-chip studio-chip--green">Balans: <?= $e($style['balance']) ?></span>
                                <span class="studio-chip studio-chip--plain"><span class="ms" aria-hidden="true">straighten</span>Kątomierz Gałęzi</span>
                            </div>
                            <?php include __DIR__ . '/partials/studio-diagram.html.php'; ?>
                            <figcaption class="studio-metrics studio-metrics--three">
                                <?php foreach ($style['metrics'] as $metric): ?>
                                    <span class="studio-metric">
                                        <span class="studio-metric__label"><?= $e($metric['label']) ?></span>
                                        <span class="studio-metric__value<?= $metric['tone'] !== '' ? ' studio-metric__value--' . $e($metric['tone']) : '' ?>"><?= $e($metric['value']) ?></span>
                                    </span>
                                <?php endforeach; ?>
                            </figcaption>
                        </figure>

                        <div class="studio-detail__info">
                            <h3 class="studio-detail__title" id="studio-detail-title"><?= $e($style['title']) ?> <span class="badge badge--terra studio-detail__badge"><?= $e($style['badge']) ?></span></h3>
                            <p class="studio-detail__text"><?= $e($style['text']) ?></p>

                            <div class="studio-errors">
                                <h4 class="studio-label">Błędy dyskwalifikujące styl (Imi-eda):</h4>
                                <ul class="studio-errors__list">
                                    <?php foreach ($style['errors'] as $error): ?>
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
                                    <?php foreach ($style['species'] as $name): ?>
                                        <li class="studio-tag"><?= $e($name) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <button type="button" class="btn btn--primary btn--block studio-detail__cta" data-style-load="<?= $e($style['model']) ?>"><span class="ms" aria-hidden="true">tune</span>Wczytaj styl do Symulatora Cięcia</button>
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
                        <nav class="studio-tabs" aria-label="Grupa gatunków">
                            <?php foreach ($speciesTabs as $tab): ?>
                                <a href="/studio?style=<?= $e($style['id']) ?>&amp;species=<?= $e($tab['id']) ?>#studio-calendar-title" class="studio-tabs__tab<?= $tab['active'] ? ' is-active' : '' ?>" data-species="<?= $e($tab['id']) ?>"<?= $tab['active'] ? ' aria-current="true"' : '' ?>><?= $tab['tab'] ?></a>
                            <?php endforeach; ?>
                        </nav>
                    </header>

                    <article class="card studio-calendar" aria-labelledby="studio-species-title">
                        <header class="studio-calendar__head">
                            <span class="icon-tile icon-tile--green studio-calendar__icon"><span class="ms" aria-hidden="true"><?= $e($calendar['icon']) ?></span></span>
                            <div>
                                <h3 class="studio-calendar__title" id="studio-species-title"><?= $e($calendar['title']) ?></h3>
                                <p class="studio-calendar__subtitle"><?= $e($calendar['subtitle']) ?></p>
                            </div>
                        </header>
                        <p class="studio-calendar__now"><span class="studio-calendar__now-dot" aria-hidden="true"></span>Bieżący miesiąc: <?= $e($monthName) ?> (<?= $e($calendar['season']['state']) ?>)</p>

                        <div class="studio-gantt" role="region" aria-label="Wykres sezonowości zabiegów" tabindex="0">
                            <div class="studio-gantt__inner">
                                <ol class="studio-gantt__months" aria-hidden="true">
                                    <?php foreach ($months as $index => $month): ?>
                                        <li<?= $index === $currentMonth ? ' class="is-current"' : '' ?>><?= $e($month) ?></li>
                                    <?php endforeach; ?>
                                </ol>
                                <?php foreach ($calendar['rows'] as $row): ?>
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
                            <p><strong><?= $e($calendar['season']['label']) ?> na <?= $e($monthName) ?>:</strong> <?= $e($calendar['season']['alert']) ?></p>
                            <button type="button" class="btn btn--secondary studio-calendar__print" data-print><span class="ms" aria-hidden="true">print</span>Drukuj Kartę Zabiegów</button>
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
                        <form class="card studio-calc" action="/studio" method="get" aria-labelledby="studio-calc-title" data-wire-calc>
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
                                    <output for="studio-diameter" class="studio-range__value studio-range__value--terra" data-wire-output>12.0 mm</output>
                                </div>
                                <input class="studio-range__input" type="range" id="studio-diameter" name="diameter" min="2" max="35" step="0.5" value="12" style="--value: 30%" data-wire-diameter>
                                <div class="studio-range__scale" aria-hidden="true">
                                    <span>2 mm (młody pęd)</span>
                                    <span>18 mm (konar)</span>
                                    <span>35 mm (gruba nasada)</span>
                                </div>
                            </div>

                            <dl class="studio-result" aria-live="polite">
                                <div class="studio-result__main">
                                    <dt>Rekomendowany przekrój:</dt>
                                    <dd>
                                        <span class="studio-result__size" data-wire-size>4.0 mm</span>
                                        <span class="studio-result__alloy" data-wire-alloy>Anodyzowane Aluminium</span>
                                    </dd>
                                </div>
                                <div class="studio-result__row">
                                    <dt>Maks. czas na roślinie:</dt>
                                    <dd data-wire-time>6-9 miesięcy</dd>
                                </div>
                                <div class="studio-result__row">
                                    <dt>Metoda kotwienia:</dt>
                                    <dd data-wire-anchor>Podwójna spirala (Jin Anchor)</dd>
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
                                <span class="studio-angle__badge" data-angle-badge>Kąt optymalny (45°)</span>
                            </header>

                            <svg class="studio-angle__diagram" viewBox="0 0 467 222" role="img" aria-label="Drut owinięty wokół gałęzi pod kątem 45 stopni">
                                <rect x="45" y="88" width="377" height="48" fill="#dfd7cf"/>
                                <line x1="45" y1="88" x2="422" y2="88" stroke="#5f6661" stroke-width="1.5"/>
                                <line x1="45" y1="136" x2="422" y2="136" stroke="#5f6661" stroke-width="1.5"/>
                                <text x="58" y="116" class="studio-svg-label studio-svg-label--wood">DREWNO KSZTAŁTOWANE (TWARDZIEJ)</text>
                                <defs><clipPath id="studio-angle-clip"><rect x="45" y="88" width="377" height="48"/></clipPath></defs>
                                <g fill="none" stroke="#a7aca6" stroke-width="5" stroke-dasharray="2.5 2.5" clip-path="url(#studio-angle-clip)" data-angle-under>
                                    <?php for ($i = 0; $i < 4; $i++): ?>
                                        <line x1="<?= 120 + 75 * $i ?>" y1="88" x2="<?= 146 + 75 * $i ?>" y2="136"/>
                                    <?php endfor; ?>
                                </g>
                                <g data-angle-diagram>
                                    <g stroke="#1e3a2b" stroke-width="7" stroke-linecap="round" clip-path="url(#studio-angle-clip)" data-angle-wire>
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <line x1="<?= 71 + 75 * $i ?>" y1="136" x2="<?= 120 + 75 * $i ?>" y2="88"/>
                                        <?php endfor; ?>
                                    </g>
                                    <line x1="195" y1="88" x2="243" y2="88" stroke="#944929" stroke-width="1.5" stroke-dasharray="2 2" data-angle-measure/>
                                    <text x="219" y="79" text-anchor="middle" class="studio-svg-label studio-svg-label--angle" data-angle-label>45°</text>
                                </g>
                            </svg>

                            <div class="studio-range">
                                <div class="studio-range__head">
                                    <label for="studio-angle" class="studio-range__label">Regulacja Kąta Przebiegu Drutu:</label>
                                    <output for="studio-angle" class="studio-range__value" data-angle-output>45 stopni</output>
                                </div>
                                <input class="studio-range__input" type="range" id="studio-angle" name="angle" min="15" max="75" step="1" value="45" style="--value: 50%" data-angle-input>
                                <p class="studio-range__legend">
                                    <span class="studio-range__risk">15° (Ryzyko: uduszenie wiązek)</span>
                                    <span class="studio-range__ideal">45° (Idealna mechanika sił)</span>
                                    <span class="studio-range__weak">75° (Brak siły trzymania gałęzi)</span>
                                </p>
                            </div>

                            <p class="studio-angle__note" data-angle-note><span class="ms" aria-hidden="true">check_circle</span><span><strong>Kąt prawidłowy:</strong> Zapewnia optymalne przełożenie wektora gięcia bez punktowego nacisku na wiązki sitowe.</span></p>
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
                                <p class="studio-sim__model"><span class="dot dot--green" aria-hidden="true"></span><span data-sim-model>Model: <?= $e($style['model']) ?></span></p>
                                <div class="studio-sim__actions">
                                    <button type="button" class="studio-sim__reset" data-sim-reset><span class="ms" aria-hidden="true">refresh</span>Reset cięć</button>
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
                                    <g class="studio-sim__branch" data-branch="1">
                                        <path d="M250 350 C 210 350, 170 352, 136 354" fill="none" stroke="#1e3a2b" stroke-width="8" stroke-linecap="round" pathLength="1"/>
                                        <ellipse class="studio-sim__leaf" cx="98" cy="358" rx="46" ry="17.6" fill="#1e3a2b"/>
                                        <ellipse class="studio-sim__leaf" cx="112" cy="352" rx="34" ry="11" fill="#31503f"/>
                                    </g>
                                    <g class="studio-sim__branch" data-branch="2">
                                        <path d="M272 340 C 310 344, 350 350, 384 352" fill="none" stroke="#1e3a2b" stroke-width="8" stroke-linecap="round" pathLength="1"/>
                                        <ellipse class="studio-sim__leaf" cx="418" cy="354" rx="41.6" ry="16" fill="#1e3a2b"/>
                                    </g>
                                    <g class="studio-sim__branch" data-branch="3">
                                        <path d="M262 286 C 236 274, 210 264, 184 258" fill="none" stroke="#1e3a2b" stroke-width="7" stroke-linecap="round" pathLength="1"/>
                                        <ellipse class="studio-sim__leaf" cx="157" cy="256" rx="32" ry="13.6" fill="#476553"/>
                                    </g>
                                    <g class="studio-sim__branch" data-branch="4">
                                        <path d="M292 250 C 330 244, 360 238, 392 234" fill="none" stroke="#1e3a2b" stroke-width="7" stroke-linecap="round" pathLength="1"/>
                                        <ellipse class="studio-sim__leaf" cx="428" cy="230" rx="44" ry="16" fill="#1e3a2b"/>
                                        <ellipse class="studio-sim__leaf" cx="416" cy="226" rx="30" ry="10" fill="#31503f"/>
                                    </g>
                                    <ellipse cx="284" cy="148" rx="56" ry="17.6" fill="#1e3a2b"/>
                                    <ellipse cx="294" cy="142" rx="42" ry="12.8" fill="#31503f"/>
                                    <line x1="284" y1="150" x2="284" y2="448" stroke="#e8907a" stroke-width="1" stroke-dasharray="4 3"/>
                                    <circle cx="278.4" cy="298.4" r="6.4" fill="#f08a65" stroke="#ffffff" stroke-width="1.5"/>
                                    <g class="studio-sim__markers">
                                        <g class="studio-sim__marker" data-branch-toggle="1" role="button" tabindex="0" aria-pressed="false" aria-label="Gałąź 1: symuluj usunięcie"><circle cx="94.4" cy="358" r="12.8"/><text x="94.4" y="362.5">1</text></g>
                                        <g class="studio-sim__marker" data-branch-toggle="2" role="button" tabindex="0" aria-pressed="false" aria-label="Gałąź 2: symuluj usunięcie"><circle cx="416" cy="352.8" r="12.8"/><text x="416" y="357.3">2</text></g>
                                        <g class="studio-sim__marker" data-branch-toggle="3" role="button" tabindex="0" aria-pressed="false" aria-label="Gałąź 3: symuluj usunięcie"><circle cx="156" cy="256.8" r="12.8"/><text x="156" y="261.3">3</text></g>
                                        <g class="studio-sim__marker" data-branch-toggle="4" role="button" tabindex="0" aria-pressed="false" aria-label="Gałąź 4: symuluj usunięcie"><circle cx="426.4" cy="229.6" r="12.8"/><text x="426.4" y="234.1">4</text></g>
                                    </g>
                                </svg>
                                <p class="studio-sim__hint"><span class="ms" aria-hidden="true">touch_app</span>Kliknij numerowaną gałąź (1-4), aby dokonać wirtualnego cięcia.</p>
                            </div>

                            <div class="studio-metrics studio-metrics--four">
                                <?php foreach ($simulatorMetrics as $metric): ?>
                                    <span class="studio-metric">
                                        <span class="studio-metric__label"><?= $e($metric['label']) ?></span>
                                        <span class="studio-metric__value<?= $metric['tone'] !== '' ? ' studio-metric__value--' . $e($metric['tone']) : '' ?>" data-sim-metric="<?= $e($metric['key']) ?>"><?= $e($metric['value']) ?></span>
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
                                            <span class="studio-indicator__value studio-indicator__value--<?= $e($indicator['tone']) ?>" data-sim-indicator="<?= $e($indicator['key']) ?>"><?= $e($indicator['value']) ?></span>
                                        </span>
                                        <span class="studio-indicator__track"><span class="studio-indicator__bar studio-indicator__bar--<?= $e($indicator['tone']) ?>" style="--value: <?= (int) $indicator['percent'] ?>%" data-sim-bar="<?= $e($indicator['key']) ?>"></span></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="studio-advice">
                                <h4 class="studio-advice__title"><span class="ms" aria-hidden="true">tips_and_updates</span>Rekomendacja Mistrza:</h4>
                                <p data-sim-advice>Gałąź #2 tworzy klasyczny błąd Kannonuki (gałąź przeciwległa na tej samej wysokości co gałąź główna #1). Rekomendowane jej całkowite usunięcie lub przekształcenie w suchy sęk Jin.</p>
                            </div>

                            <div class="studio-sim__buttons">
                                <button type="button" class="btn btn--primary studio-sim__button" data-sim-save disabled><span class="ms" aria-hidden="true">bookmark_add</span><span>Zapisz Plan Cięcia w Dzienniku Drzewka</span></button>
                                <button type="button" class="btn btn--tonal studio-sim__button" data-sim-jin aria-pressed="false" disabled><span class="ms" aria-hidden="true">brush</span><span>Konwertuj cięcie na martwe drewno (Jin/Shari)</span></button>
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
    <script src="<?= $asset('/js/studio.js') ?>" defer></script>
</body>
</html>
