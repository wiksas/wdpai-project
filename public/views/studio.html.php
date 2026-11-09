<?php
// Układ: layouts/app.html.php
$layout = 'app';
$mainClass = 'studio';
$pageStyles = ['studio'];
$pageScripts = ['studio'];

/**
 * Studio formowania. Symulator cięcia liczy tylko geometrię rysunku.
 *
 * @var list<array<string, mixed>> $styles
 * @var array<string, mixed> $style
 * @var list<array<string, mixed>> $speciesTabs
 * @var array<string, mixed> $calendar
 * @var string $monthName
 */
$e = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

$months = ['sty', 'lut', 'mar', 'kwi', 'maj', 'cze', 'lip', 'sie', 'wrz', 'paź', 'lis', 'gru'];
$currentMonth = $calendar['currentMonth'];
$cellLabels = ['best' => 'najlepszy termin', 'possible' => 'możliwy, ostrożnie'];
$season = $calendar['season'];
$monthLower = mb_strtolower($monthName);

$techniques = [
    [
        'image' => '/images/wiring-copper-branch-2.jpg',
        'alt' => 'Gałąź sosny owinięta drutem miedzianym',
        'title' => 'Drutowanie miedzią',
        'text' => 'Miedź wyżarzana twardnieje po owinięciu, więc dobrze trzyma sztywne gałęzie iglaków. Drut prowadź pod kątem około 45° i zdejmuj, zanim zacznie wrastać w korę.',
    ],
    [
        'image' => '/images/bonsai-table-scissors.jpg',
        'alt' => 'Klon japoński po defoliacji na drewnianym stole obok nożyc',
        'title' => 'Defoliacja',
        'text' => 'Usunięcie liści na początku lata pobudza drugi przyrost z drobniejszymi liśćmi. Tylko dla zdrowych, silnych drzew liściastych.',
    ],
    [
        'image' => '/images/bonsai-jin-portrait.jpg',
        'alt' => 'Jałowiec z wyrzeźbionym martwym drewnem na szczycie',
        'title' => 'Jin i shari',
        'text' => 'Zbędny konar można zamiast odcinać zamienić w martwe drewno: zdjąć korę i zabezpieczyć drewno wapnem siarkowym.',
    ],
];
?>
<header class="studio-hero" aria-labelledby="studio-title">
    <p class="eyebrow">Formowanie, cięcie i drutowanie</p>
    <h1 class="studio-hero__title" id="studio-title">Studio formowania <span class="term studio-hero__term">Zōkei <span lang="ja">造形</span></span></h1>
    <p class="studio-hero__lead">Pięć klasycznych stylów z rysunkami drzewek, kalendarz zabiegów dla gatunku, dobór drutu i próbne cięcie na rysunku, zanim sięgniesz po nożyce.</p>
    <nav class="studio-jump" aria-label="Narzędzia na tej stronie">
        <a href="#studio-styles-title">Style</a>
        <a href="#studio-calendar-title">Kalendarz zabiegów</a>
        <a href="#studio-wiring-title">Drutowanie</a>
        <a href="#studio-simulator-title">Próbne cięcie</a>
    </nav>
</header>

<!-- Style klasyczne -->
<section class="studio-section" aria-labelledby="studio-styles-title">
    <header class="studio-head">
        <h2 class="studio-head__title" id="studio-styles-title">Pięć stylów klasycznych</h2>
        <p class="studio-head__desc">Wybierz styl, żeby zobaczyć rysunek drzewka z trójkątem korony, numerowane cechy i gatunki, które go znoszą.</p>
    </header>

    <ul class="studio-styles" aria-label="Style klasyczne">
        <?php foreach ($styles as $item): ?>
            <li>
                <a href="/studio?style=<?= $e($item['id']) ?>&amp;species=<?= $e($calendar['id']) ?>#studio-styles-title" class="studio-style<?= $item['active'] ? ' is-active' : '' ?>" data-style="<?= $e($item['id']) ?>"<?= $item['active'] ? ' aria-current="true"' : '' ?>>
                    <span class="studio-style__kanji" lang="ja"><?= $e($item['kanji']) ?></span>
                    <span class="studio-style__name"><?= $e($item['name']) ?></span>
                    <span class="studio-style__subtitle"><?= $e($item['subtitle']) ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <article class="studio-detail" aria-labelledby="studio-detail-title">
        <figure class="studio-detail__visual">
            <label class="studio-detail__toggle">
                <input type="checkbox" checked>
                Pokaż trójkąt korony i numery
            </label>
            <?php include __DIR__ . '/partials/studio-diagram.html.php'; ?>
            <figcaption class="studio-detail__caption">
                Trójkąt korony: <?= $e($style['balance']) ?>. Linia przerywana to pion nad nasadą, a numery odpowiadają opisom obok. <?= $e($style['diagram']['note']) ?>
            </figcaption>
        </figure>

        <div class="studio-detail__info">
            <p class="term studio-detail__term"><?= $e($style['name']) ?> <span lang="ja"><?= $e($style['kanji']) ?></span></p>
            <h3 class="studio-detail__title" id="studio-detail-title"><?= $e($style['title']) ?></h3>
            <p class="studio-detail__text"><?= $e($style['text']) ?></p>

            <dl class="studio-facts">
                <?php foreach ($style['metrics'] as $index => $metric): ?>
                    <div class="studio-facts__row">
                        <dt><span class="studio-facts__n" aria-hidden="true"><?= $index + 1 ?></span><?= $e($metric['label']) ?></dt>
                        <dd><?= $e($metric['value']) ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>

            <div class="studio-detail__block">
                <h4 class="studio-label">Gatunki, które dobrze go znoszą</h4>
                <ul class="studio-species">
                    <?php foreach ($style['species'] as $name): ?>
                        <li><?= $e($name) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="studio-detail__block">
                <h4 class="studio-label">Gałęzie do usunięcia w każdym stylu (imi-eda)</h4>
                <ul class="studio-errors">
                    <?php foreach ($style['errors'] as $error): ?>
                        <li><span class="term"><?= $e($error['term']) ?> <span lang="ja"><?= $e($error['kanji']) ?></span></span> <?= $e($error['text']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </article>
</section>

<!-- Kalendarz zabiegów -->
<section class="studio-section" aria-labelledby="studio-calendar-title">
    <header class="studio-head">
        <h2 class="studio-head__title" id="studio-calendar-title">Kalendarz zabiegów</h2>
        <nav class="studio-tabs" aria-label="Gatunek">
            <?php foreach ($speciesTabs as $tab): ?>
                <a href="/studio?style=<?= $e($style['id']) ?>&amp;species=<?= $e($tab['id']) ?>#studio-calendar-title" class="studio-tabs__tab<?= $tab['active'] ? ' is-active' : '' ?>" data-species="<?= $e($tab['id']) ?>"<?= $tab['active'] ? ' aria-current="true"' : '' ?>><?= $e($tab['tab']) ?></a>
            <?php endforeach; ?>
        </nav>
    </header>

    <article class="studio-calendar" aria-labelledby="studio-species-title">
        <header class="studio-calendar__head">
            <div>
                <h3 class="studio-calendar__title" id="studio-species-title"><?= $e($calendar['title']) ?> <span class="studio-calendar__latin"><?= $e($calendar['latin']) ?></span></h3>
                <p class="studio-calendar__subtitle"><span class="term"><?= $e($calendar['jaName']) ?> <span lang="ja"><?= $e($calendar['jaKanji']) ?></span></span> <?= $e($calendar['subtitle']) ?></p>
            </div>
            <button type="button" class="btn btn--secondary btn--sm studio-calendar__print" data-print><span class="ms" aria-hidden="true">print</span>Drukuj kalendarz</button>
        </header>

        <div class="studio-season<?= $season['urgent'] ? ' is-urgent' : '' ?>">
            <span class="ms" aria-hidden="true"><?= $season['urgent'] ? 'warning' : 'event' ?></span>
            <p><strong><?= $season['urgent'] ? 'Uwaga' : 'Teraz' ?>, <?= $e($monthLower) ?> (<?= $e($season['state']) ?>):</strong> <?= $e($season['alert']) ?></p>
        </div>

        <div class="studio-gantt" role="region" aria-label="Terminy zabiegów w ciągu roku" tabindex="0">
            <div class="studio-gantt__inner">
                <ol class="studio-gantt__months" aria-hidden="true">
                    <?php foreach ($months as $index => $month): ?>
                        <li<?= $index === $currentMonth ? ' class="is-current"' : '' ?>><?= $e($month) ?></li>
                    <?php endforeach; ?>
                </ol>
                <?php foreach ($calendar['rows'] as $row): ?>
                    <div class="studio-gantt__row">
                        <div class="studio-gantt__label">
                            <h4 class="studio-gantt__title"><?= $e($row['title']) ?></h4>
                            <span class="studio-gantt__note"><?= $e($row['note']) ?></span>
                        </div>
                        <ol class="studio-gantt__track">
                            <?php foreach ($months as $index => $month): ?>
                                <?php $variant = $row['cells'][$index] ?? 'none'; ?>
                                <li class="studio-gantt__cell studio-gantt__cell--<?= $e($variant) ?><?= $index === $currentMonth ? ' is-current' : '' ?>">
                                    <?php if (isset($cellLabels[$variant])): ?><span class="visually-hidden"><?= $e($month) ?>: <?= $e($cellLabels[$variant]) ?></span><?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <ul class="studio-legend" aria-label="Legenda">
            <li><span class="studio-legend__swatch studio-gantt__cell--best" aria-hidden="true"></span>Najlepszy termin</li>
            <li><span class="studio-legend__swatch studio-gantt__cell--possible" aria-hidden="true"></span>Możliwy, ostrożnie</li>
            <li><span class="studio-legend__swatch studio-legend__swatch--now" aria-hidden="true"></span>Bieżący miesiąc</li>
        </ul>
    </article>
</section>

<!-- Drutowanie -->
<section class="studio-section" aria-labelledby="studio-wiring-title">
    <header class="studio-head">
        <h2 class="studio-head__title" id="studio-wiring-title">Drutowanie bez wrastania</h2>
        <p class="studio-head__desc">Za cienki drut nie utrzyma gałęzi, za gruby i za ciasny zostawi blizny na korze.</p>
    </header>

    <div class="studio-wiring">
        <form class="studio-panel studio-calc" action="/studio" method="get" aria-labelledby="studio-calc-title" data-wire-calc>
            <h3 class="studio-panel__title" id="studio-calc-title">Dobór grubości drutu</h3>
            <p class="studio-panel__text">Reguła: drut ma około 1/3 średnicy gałęzi (aluminium) albo 1/4 (miedź).</p>

            <fieldset class="studio-calc__alloys">
                <legend class="studio-label">Materiał</legend>
                <label class="studio-alloy">
                    <input type="radio" name="alloy" value="aluminium" checked>
                    <span class="studio-alloy__name">Aluminium</span>
                    <span class="studio-alloy__hint">miękkie, do drzew liściastych</span>
                </label>
                <label class="studio-alloy">
                    <input type="radio" name="alloy" value="copper">
                    <span class="studio-alloy__name">Miedź wyżarzana</span>
                    <span class="studio-alloy__hint">sztywna, do iglaków</span>
                </label>
            </fieldset>

            <div class="studio-range">
                <div class="studio-range__head">
                    <label for="studio-diameter" class="studio-label">Średnica gałęzi w miejscu gięcia</label>
                    <output for="studio-diameter" class="studio-range__value" data-wire-output>12.0 mm</output>
                </div>
                <input class="studio-range__input" type="range" id="studio-diameter" name="diameter" min="2" max="35" step="0.5" value="12" style="--value: 30%" data-wire-diameter>
                <div class="studio-range__scale" aria-hidden="true">
                    <span>2 mm, młody pęd</span>
                    <span>35 mm, gruby konar</span>
                </div>
            </div>

            <dl class="studio-result" aria-live="polite">
                <div class="studio-result__main">
                    <dt>Drut</dt>
                    <dd><span class="studio-result__size" data-wire-size>4.0 mm</span> <span data-wire-alloy>aluminium</span></dd>
                </div>
                <div class="studio-result__row">
                    <dt>Sprawdzaj i zdejmij po</dt>
                    <dd data-wire-time>6–9 miesiącach</dd>
                </div>
                <div class="studio-result__row">
                    <dt>Zakotwiczenie</dt>
                    <dd data-wire-anchor>o sąsiednią gałąź (dwie gałęzie jednym drutem)</dd>
                </div>
            </dl>

            <p class="studio-note"><span class="ms" aria-hidden="true">content_cut</span><span>Latem gałęzie szybko grubieją, więc oglądaj drut co dwa tygodnie. Wrastający drut rozetnij szczypcami przy każdym zwoju, nie odwijaj go.</span></p>
        </form>

        <article class="studio-panel studio-angle" aria-labelledby="studio-angle-title">
            <div class="studio-angle__head">
                <h3 class="studio-panel__title" id="studio-angle-title">Kąt owijania</h3>
                <span class="studio-angle__status" data-angle-badge>Dobry kąt (45°)</span>
            </div>

            <svg class="studio-angle__diagram" viewBox="0 0 467 222" role="img" aria-label="Drut owinięty wokół gałęzi, kąt ustawiany suwakiem poniżej">
                <defs><clipPath id="studio-angle-clip"><rect x="45" y="88" width="377" height="48"/></clipPath></defs>
                <rect class="studio-svg-wood" x="45" y="88" width="377" height="48"/>
                <line class="studio-svg-edge" x1="45" y1="88" x2="422" y2="88"/>
                <line class="studio-svg-edge" x1="45" y1="136" x2="422" y2="136"/>
                <g class="studio-svg-wire-back" clip-path="url(#studio-angle-clip)" data-angle-under>
                    <?php for ($i = 0; $i < 4; $i++): ?>
                        <line x1="<?= 120 + 75 * $i ?>" y1="88" x2="<?= 146 + 75 * $i ?>" y2="136"/>
                    <?php endfor; ?>
                </g>
                <g class="studio-svg-wire" clip-path="url(#studio-angle-clip)" data-angle-wire>
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <line x1="<?= 71 + 75 * $i ?>" y1="136" x2="<?= 120 + 75 * $i ?>" y2="88"/>
                    <?php endfor; ?>
                </g>
                <line class="studio-svg-measure" x1="195" y1="88" x2="243" y2="88" data-angle-measure/>
                <text x="219" y="79" text-anchor="middle" class="studio-svg-label" data-angle-label>45°</text>
                <text x="58" y="168" class="studio-svg-label studio-svg-label--muted">gałąź</text>
            </svg>

            <div class="studio-range">
                <div class="studio-range__head">
                    <label for="studio-angle" class="studio-label">Kąt zwojów względem gałęzi</label>
                    <output for="studio-angle" class="studio-range__value" data-angle-output>45°</output>
                </div>
                <input class="studio-range__input" type="range" id="studio-angle" name="angle" min="15" max="75" step="1" value="45" style="--value: 50%" data-angle-input>
                <div class="studio-range__scale" aria-hidden="true">
                    <span>15°, za ciasno</span>
                    <span>45°</span>
                    <span>75°, za luźno</span>
                </div>
            </div>

            <p class="studio-note" data-angle-note><span class="ms" aria-hidden="true">check_circle</span><span><strong>Dobry kąt.</strong> Zwoje trzymają gałąź w nowym położeniu i nie uciskają kory w jednym miejscu.</span></p>
        </article>
    </div>
</section>

<!-- Próbne cięcie -->
<section class="studio-section" aria-labelledby="studio-simulator-title">
    <header class="studio-head">
        <h2 class="studio-head__title" id="studio-simulator-title">Próbne cięcie</h2>
        <p class="studio-head__desc">Zaznacz gałęzie do usunięcia na rysunku. Wynik pokazuje, ile ulistnienia zostaje i w którą stronę przesuwa się środek masy korony.</p>
    </header>

    <article class="studio-sim" aria-label="Próbne cięcie">
        <div class="studio-sim__stage">
            <svg class="studio-sim__tree" viewBox="24 48 520 510" role="img" aria-label="Rysunek drzewka z czterema numerowanymi gałęziami">
                <?= \App\Support\BonsaiArt::tree($simulator, 'simulator', true) ?>
                <line class="studio-svg-axis" x1="284" y1="120" x2="284" y2="438"/>
                <circle class="studio-svg-center" cx="284" cy="300" r="7" data-sim-center/>
                <g class="studio-sim__markers">
                    <?php foreach ($simulator['branches'] as $branchIndex => $branch): ?>
                        <?php foreach ($simulator['pads'] as $pad): ?>
                            <?php if (($pad['branch'] ?? null) === $branchIndex): ?>
                                <g class="studio-sim__marker" data-branch-toggle="<?= (int) $branch['group'] ?>" role="button" tabindex="0" aria-pressed="false" aria-label="Gałąź <?= (int) $branch['group'] ?>: zaznacz do usunięcia"><circle class="studio-sim__hit" cx="<?= $pad['cx'] ?>" cy="<?= $pad['cy'] ?>" r="36"/><circle cx="<?= $pad['cx'] ?>" cy="<?= $pad['cy'] ?>" r="14"/><text x="<?= $pad['cx'] ?>" y="<?= $pad['cy'] + 5 ?>"><?= (int) $branch['group'] ?></text></g>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </g>
            </svg>
            <p class="studio-sim__hint">Kliknij numer gałęzi albo wybierz go klawiszem Tab i naciśnij Enter. Linia pionowa to oś pnia, kropka to środek masy korony.</p>
        </div>

        <aside class="studio-sim__panel" aria-labelledby="studio-sim-result">
            <h3 class="studio-panel__title" id="studio-sim-result">Wynik cięcia</h3>
            <dl class="studio-sim__metrics" aria-live="polite">
                <div>
                    <dt>Zostaje ulistnienia</dt>
                    <dd data-sim-metric="foliage">100%</dd>
                </div>
                <div>
                    <dt>Środek masy korony</dt>
                    <dd data-sim-metric="shift">na osi pnia</dd>
                </div>
            </dl>
            <div class="progress studio-sim__bar" aria-hidden="true">
                <span class="progress__bar progress__bar--green" style="--value: 100%" data-sim-bar></span>
            </div>

            <p class="studio-sim__advice" data-sim-advice>Gałęzie 1 i 2 wyrastają naprzeciw siebie prawie na tej samej wysokości (kannuki-eda). Zwykle usuwa się jedną z nich.</p>

            <div class="studio-sim__buttons">
                <button type="button" class="btn btn--primary" data-sim-save disabled><span class="ms" aria-hidden="true">event_available</span>Dodaj plan cięcia do harmonogramu</button>
                <button type="button" class="btn btn--tonal" data-sim-jin aria-pressed="false" disabled><span class="ms" aria-hidden="true">brush</span>Pokaż usunięte jako jin</button>
                <button type="button" class="btn btn--ghost" data-sim-reset><span class="ms" aria-hidden="true">undo</span>Cofnij wszystkie cięcia</button>
            </div>
        </aside>
    </article>
</section>

<!-- Techniki na zdjęciach -->
<section class="studio-section" aria-labelledby="studio-cases-title">
    <header class="studio-head">
        <h2 class="studio-head__title" id="studio-cases-title">Techniki na zdjęciach</h2>
    </header>

    <div class="studio-cases">
        <?php foreach ($techniques as $index => $case): ?>
            <article class="studio-case<?= $index === 0 ? ' studio-case--lead' : '' ?>">
                <div class="studio-case__media">
                    <img src="<?= $e($case['image']) ?>" alt="<?= $e($case['alt']) ?>" loading="lazy" width="512" height="390">
                </div>
                <div class="studio-case__body">
                    <h3 class="studio-case__title"><?= $e($case['title']) ?></h3>
                    <p class="studio-case__text"><?= $e($case['text']) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
