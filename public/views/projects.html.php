<?php
/** @var list<array<string, mixed>> $trees */
/** @var int $treeCount */

$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$treeNoun = static function (int $count): string {
    if ($count === 1) {
        return 'drzewko';
    }
    $lastDigit = $count % 10;
    $lastTwo = $count % 100;

    return ($lastDigit >= 2 && $lastDigit <= 4 && ($lastTwo < 12 || $lastTwo > 14)) ? 'drzewka' : 'drzewek';
};

$statusStyles = [
    'ideal' => ['pill' => 'pill--green', 'icon' => 'check_circle', 'bar' => 'progress__bar--green', 'action' => 'btn--tonal', 'actionIcon' => 'check'],
    'shaping' => ['pill' => 'pill--green', 'icon' => 'architecture', 'bar' => 'progress__bar--green', 'action' => 'btn--tonal', 'actionIcon' => 'tune'],
    'attention' => ['pill' => 'pill--terra', 'icon' => 'humidity_low', 'bar' => 'progress__bar--terra', 'action' => 'btn--tertiary', 'actionIcon' => 'shower'],
    'critical' => ['pill' => 'pill--terra', 'icon' => 'water_drop', 'bar' => 'progress__bar--terra', 'action' => 'btn--primary', 'actionIcon' => 'done_all'],
];

$thirstyTrees = array_values(array_filter($trees, static fn (array $tree): bool => in_array($tree['status'], ['attention', 'critical'], true)));
$wiredTrees = array_values(array_filter($trees, static fn (array $tree): bool => $tree['care'] !== null));
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <?php include __DIR__ . '/partials/head.html.php'; ?>
    <link rel="stylesheet" href="<?= $asset('/css/pages/dashboard.css') ?>">
</head>
<body>
    <div class="app">
        <?php include __DIR__ . '/partials/app-sidebar.html.php'; ?>
        <div class="app-main">
            <?php include __DIR__ . '/partials/app-topbar.html.php'; ?>
            <main class="app-content dash">
                <h1 class="visually-hidden">Moja Kolekcja</h1>

                <!-- Mobile: nagłówek ogrodu -->
                <header class="dash-garden dash-mobile-only">
                    <div class="dash-garden__top">
                        <p class="dash-garden__place">
                            <span class="ms" aria-hidden="true">nest_eco_leaf</span>
                            Warszawa • Ogród wschodni
                        </p>
                        <p class="dash-garden__weather">
                            <span class="ms" aria-hidden="true">light_mode</span>
                            <strong>19°C</strong> • 64%
                        </p>
                    </div>
                    <div class="dash-garden__bottom">
                        <p class="dash-garden__title" role="heading" aria-level="2">Kolekcja Kenjiro</p>
                        <p class="dash-garden__wind">Lekki zefir (2 m/s)</p>
                    </div>
                </header>

                <section class="dash-vitality dash-mobile-only" aria-label="Witalność ogrodu">
                    <span class="dash-vitality__ring" style="--value: 96%"><span>96%</span></span>
                    <div class="dash-vitality__text">
                        <p class="dash-vitality__title"><span class="dot dot--dark" aria-hidden="true"></span>Witalność ogrodu: Doskonała</p>
                        <p class="dash-vitality__note">
                            <span class="ms" aria-hidden="true">water_drop</span>
                            1 drzewko oczekuje na podlanie
                        </p>
                    </div>
                    <button type="button" class="dash-vitality__refresh" aria-label="Odśwież odczyty">
                        <span class="ms" aria-hidden="true">sync</span>
                    </button>
                </section>

                <section class="dash-rituals dash-mobile-only" aria-labelledby="dash-rituals-title">
                    <div class="dash-rituals__head">
                        <h2 class="dash-rituals__title" id="dash-rituals-title">
                            <span class="dot dot--terra" aria-hidden="true"></span>
                            Pilne Rytuały
                            <span class="dash-rituals__count">2</span>
                        </h2>
                        <p class="dash-rituals__when">Przed zachodem słońca</p>
                    </div>
                    <div class="dash-rituals__track">
                        <article class="dash-ritual">
                            <div class="dash-ritual__head">
                                <span class="icon-tile icon-tile--sm icon-tile--terra" aria-hidden="true"><span class="ms">water_drop</span></span>
                                <div>
                                    <h3 class="dash-ritual__name">Klon Palmowy (Momiji)</h3>
                                    <p class="dash-ritual__meta dash-ritual__meta--terra">Wilgotność krytyczna: 28%</p>
                                </div>
                            </div>
                            <p class="dash-ritual__text">Zalecana kąpiel podsiąkowa w odstanej deszczówce (15 min).</p>
                            <div class="dash-ritual__foot">
                                <span class="dash-ritual__priority">Priorytet: Wysoki</span>
                                <button type="button" class="btn btn--tertiary btn--sm dash-ritual__btn">
                                    <span class="ms" aria-hidden="true">check</span>
                                    Zrobione
                                    <span class="ms" aria-hidden="true">check</span>
                                </button>
                            </div>
                        </article>
                        <article class="dash-ritual">
                            <div class="dash-ritual__head">
                                <span class="icon-tile icon-tile--sm icon-tile--green" aria-hidden="true"><span class="ms">show_chart</span></span>
                                <div>
                                    <h3 class="dash-ritual__name">Jałowiec Shimpaku</h3>
                                    <p class="dash-ritual__meta">Tydzień 14 po drutowaniu</p>
                                </div>
                            </div>
                            <p class="dash-ritual__text">Kambium gałęzi wymaga inspekcji przed zdjęciem drutu.</p>
                            <div class="dash-ritual__foot">
                                <span class="dash-ritual__priority">Formowanie gałęzi</span>
                                <button type="button" class="btn btn--tonal btn--sm dash-ritual__btn">Sprawdź</button>
                            </div>
                        </article>
                    </div>
                </section>

                <section class="dash-station dash-mobile-only" aria-label="Stacja Donicowa BLE">
                    <span class="dash-station__icon" aria-hidden="true"><span class="ms">sensors</span></span>
                    <div class="dash-station__info">
                        <h2 class="dash-station__title">Stacja Donicowa BLE</h2>
                        <p class="dash-station__sub">Sensor SoilScan v3 • Połączono</p>
                    </div>
                    <span class="dot dot--dark dash-station__dot" aria-hidden="true"></span>
                    <div class="dash-station__reading">
                        <p class="dash-station__value">22.4°C • 68% RH</p>
                        <p class="dash-station__time">Odświeżono 3m temu</p>
                    </div>
                </section>

                <!-- Desktop: statystyki kolekcji -->
                <section class="dash-stats" aria-label="Podsumowanie kolekcji">
                    <article class="dash-stat">
                        <div class="dash-stat__body">
                            <h2 class="dash-stat__label">Kondycja kolekcji</h2>
                            <p class="dash-stat__value">
                                <span class="dash-stat__number">96%</span>
                                <span class="dash-stat__trend"><span class="ms" aria-hidden="true">trending_up</span>Optymalna</span>
                            </p>
                        </div>
                        <span class="dash-stat__ring" aria-hidden="true"><span class="ms">potted_plant</span></span>
                    </article>
                    <article class="dash-stat">
                        <div class="dash-stat__body">
                            <h2 class="dash-stat__label">Okazy pod opieką</h2>
                            <p class="dash-stat__value">
                                <span class="dash-stat__number"><?= $e($treeCount) ?></span>
                                <span class="dash-stat__unit"><?= $e($treeNoun((int) $treeCount)) ?></span>
                            </p>
                        </div>
                        <span class="dash-stat__icon" aria-hidden="true"><span class="ms">yard</span></span>
                    </article>
                    <article class="dash-stat dash-stat--terra">
                        <div class="dash-stat__body">
                            <h2 class="dash-stat__label">Nawadnianie dziś</h2>
                            <p class="dash-stat__value">
                                <span class="dash-stat__number"><?= $e(count($thirstyTrees)) ?></span>
                                <span class="dash-stat__unit">wymaga wody</span>
                            </p>
                        </div>
                        <span class="dash-stat__icon dash-stat__icon--terra" aria-hidden="true"><span class="ms">water_drop</span></span>
                    </article>
                    <article class="dash-stat">
                        <div class="dash-stat__body">
                            <h2 class="dash-stat__label">Drutowanie</h2>
                            <p class="dash-stat__value">
                                <span class="dash-stat__number"><?= $e(count($wiredTrees)) ?></span>
                                <span class="dash-stat__unit">do przeglądu</span>
                            </p>
                        </div>
                        <span class="dash-stat__icon" aria-hidden="true"><span class="ms">straighten</span></span>
                    </article>
                </section>

                <div class="dash-layout">
                    <div class="dash-primary">
                        <section class="dash-filters" aria-label="Filtry kolekcji">
                            <nav class="dash-filters__tabs" aria-label="Kategorie okazów">
                                <a href="/projects" class="dash-filter dash-filter--all is-active" aria-current="page">Wszystkie (<?= $e($treeCount) ?>)</a>
                                <a href="/projects?category=conifer" class="dash-filter dash-filter--conifer">Iglaste</a>
                                <a href="/projects?category=deciduous" class="dash-filter dash-filter--deciduous">Liściaste</a>
                                <a href="/projects?status=attention" class="dash-filter dash-filter--attention"><span class="dot dot--terra" aria-hidden="true"></span>Wymagające uwagi</a>
                            </nav>
                            <button type="button" class="btn btn--primary btn--sm dash-filters__add">
                                <span class="ms" aria-hidden="true">add</span>
                                Dodaj drzewko
                            </button>
                        </section>

                        <section class="dash-trees" aria-label="Okazy w kolekcji">
                            <?php foreach ($trees as $tree): ?>
                                <?php
                                $status = (string) $tree['status'];
                                $style = $statusStyles[$status] ?? $statusStyles['ideal'];
                                $url = '/project/' . (int) $tree['id'];
                                $alert = $tree['alert'];
                                $care = $tree['care'];
                                $moistureLabel = match ($status) {
                                    'attention' => 'Poziom nawodnienia',
                                    'critical' => 'Wilgotność bryły',
                                    default => 'Wilgotność ' . $tree['substrate'],
                                };
                                $moistureIcon = match ($status) {
                                    'attention' => 'humidity_low',
                                    'critical' => 'water_drop',
                                    default => 'waves',
                                };
                                ?>
                                <article class="dash-tree dash-tree--<?= $e($status) ?>">
                                    <div class="dash-tree__head">
                                        <a href="<?= $e($url) ?>" class="dash-tree__media" tabindex="-1" aria-hidden="true">
                                            <img src="<?= $e($tree['image']) ?>" alt="" loading="lazy">
                                            <?php if ($alert !== null): ?>
                                                <span class="dash-tree__overlay dash-tree__overlay--alert"><span class="ms">warning</span><?= $e($alert['title']) ?></span>
                                            <?php else: ?>
                                                <span class="dash-tree__overlay"><?= $e($tree['style']) ?> • <?= $e($tree['styleDescription']) ?></span>
                                            <?php endif; ?>
                                            <span class="dash-tree__overlay dash-tree__overlay--age"><span class="ms">park</span><?= $e($tree['ageLabel']) ?></span>
                                        </a>
                                        <div class="dash-tree__titles">
                                            <h3 class="dash-tree__name">
                                                <a href="<?= $e($url) ?>"><?= $e($tree['name']) ?><span class="dash-tree__kanji"> (<span lang="ja"><?= $e($tree['kanji']) ?></span>)</span></a>
                                            </h3>
                                            <span class="dash-tree__pot-tag"><?= $e($tree['pot']) ?></span>
                                            <span class="pill <?= $e($style['pill']) ?> dash-tree__status">
                                                <span class="dot dot--terra dash-tree__status-dot" aria-hidden="true"></span>
                                                <span class="ms dash-tree__status-icon" aria-hidden="true"><?= $e($style['icon']) ?></span>
                                                <?= $e($tree['statusLabel']) ?>
                                            </span>
                                            <p class="dash-tree__species">
                                                <span class="dash-tree__latin"><?= $e($tree['species']) ?></span><span class="dash-tree__sep"> • </span><span class="dash-tree__common"><?= $e($tree['commonName']) ?></span>
                                            </p>
                                        </div>
                                        <button type="button" class="dash-tree__more" aria-label="Więcej opcji: <?= $e($tree['name']) ?>">
                                            <span class="ms" aria-hidden="true">more_vert</span>
                                        </button>
                                    </div>

                                    <p class="dash-tree__summary"><?= $e($tree['ageLabel']) ?> · <?= $e($tree['style']) ?> (<?= $e($tree['styleDescription']) ?>)</p>

                                    <?php if ($alert !== null): ?>
                                        <p class="dash-tree__alert">
                                            <span class="ms" aria-hidden="true"><?= $status === 'critical' ? 'pool' : 'warning' ?></span>
                                            <?= $e($alert['title']) ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if ($care !== null): ?>
                                        <div class="dash-tree__care">
                                            <div class="dash-tree__care-head">
                                                <p class="dash-tree__care-title">
                                                    <span class="ms" aria-hidden="true">architecture</span>
                                                    <?= $e($care['title']) ?>
                                                </p>
                                                <p class="dash-tree__care-meta"><?= $e($care['meta']) ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="dash-tree__moisture">
                                        <p class="dash-tree__moisture-row">
                                            <span class="dash-tree__moisture-label">
                                                <span class="ms" aria-hidden="true"><?= $e($moistureIcon) ?></span>
                                                <?= $e($moistureLabel) ?>
                                            </span>
                                            <span class="dash-tree__moisture-value"><?= $e($tree['moisture']) ?>% (<?= $e($tree['moistureLabel']) ?>)</span>
                                        </p>
                                        <div class="progress progress--thin" role="progressbar" aria-label="<?= $e($moistureLabel) ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $e($tree['moisture']) ?>">
                                            <span class="progress__bar <?= $e($style['bar']) ?>" style="--value: <?= (int) $tree['moisture'] ?>%"></span>
                                        </div>
                                    </div>

                                    <div class="dash-tree__bottom">
                                        <?php if ($tree['action'] !== null): ?>
                                            <div class="dash-tree__actions">
                                                <button type="button" class="btn <?= $e($style['action']) ?> dash-tree__action">
                                                    <span class="ms" aria-hidden="true"><?= $e($style['actionIcon']) ?></span>
                                                    <?= $e($tree['action']) ?>
                                                </button>
                                                <?php if ($status === 'attention'): ?>
                                                    <button type="button" class="dash-tree__vitals" aria-label="Parametry życiowe: <?= $e($tree['name']) ?>">
                                                        <span class="ms" aria-hidden="true">vital_signs</span>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <p class="dash-tree__foot">
                                            <span class="dash-tree__foot-water">
                                                <span class="ms" aria-hidden="true">schedule</span>
                                                Podlano: <?= $e($tree['lastWatering']) ?>
                                            </span>
                                            <a href="<?= $e($url) ?>" class="dash-tree__link">Karta okazu<span class="ms" aria-hidden="true">chevron_right</span></a>
                                        </p>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </section>

                        <section class="dash-season" aria-labelledby="dash-season-title">
                            <div class="dash-season__text">
                                <p class="eyebrow eyebrow--green dash-season__eyebrow">Cykl rozwojowy • Wiosna 2025</p>
                                <h2 class="dash-season__title" id="dash-season-title">Defoliacja i zagęszczanie pąków</h2>
                                <p class="dash-season__desc">Okno biologiczne zamyka się za 18 dni.</p>
                            </div>
                            <div class="dash-season__actions">
                                <button type="button" class="btn dash-season__export">Eksportuj dziennik</button>
                                <a href="/schedule" class="btn btn--primary dash-season__guide">
                                    <span class="ms" aria-hidden="true">menu_book</span>
                                    Przewodnik sezonu
                                </a>
                            </div>
                        </section>
                    </div>

                    <aside class="dash-side" aria-label="Szklarnia i kalendarz">
                        <section class="dash-panel dash-climate" aria-labelledby="dash-climate-title">
                            <div class="dash-panel__head">
                                <h2 class="dash-panel__title" id="dash-climate-title">
                                    <span class="ms" aria-hidden="true">thermostat_auto</span>
                                    Mikroklimat
                                </h2>
                                <span class="dash-climate__live">Live BLE</span>
                            </div>
                            <dl class="dash-climate__grid">
                                <div class="dash-climate__tile">
                                    <dt>Temperatura</dt>
                                    <dd class="dash-climate__value">22.4 <small>°C</small></dd>
                                </div>
                                <div class="dash-climate__tile dash-climate__tile--terra">
                                    <dt>Wilgotność powietrza</dt>
                                    <dd class="dash-climate__value">48%</dd>
                                </div>
                                <div class="dash-climate__tile">
                                    <dt>Nasłonecznienie</dt>
                                    <dd class="dash-climate__value">6.2 <small>UV</small></dd>
                                </div>
                                <div class="dash-climate__tile">
                                    <dt>Punkt rosy</dt>
                                    <dd class="dash-climate__value">11.1 <small>°C</small></dd>
                                </div>
                            </dl>
                            <label class="dash-toggle">
                                <span class="ms" aria-hidden="true">humidity_high</span>
                                <span class="dash-toggle__label">Automatyczne zamgławianie</span>
                                <input type="checkbox" class="dash-toggle__input" checked>
                                <span class="dash-toggle__switch" aria-hidden="true"></span>
                            </label>
                        </section>

                        <section class="dash-panel dash-calendar" aria-labelledby="dash-calendar-title">
                            <div class="dash-panel__head">
                                <div>
                                    <p class="eyebrow eyebrow--muted">Kalendarz prac</p>
                                    <h2 class="dash-panel__title" id="dash-calendar-title">Maj • Okres Świeczek</h2>
                                </div>
                                <span class="ms dash-calendar__icon" aria-hidden="true">calendar_today</span>
                            </div>
                            <ol class="dash-timeline">
                                <li class="dash-timeline__item">
                                    <span class="dash-timeline__marker dash-timeline__marker--terra" aria-hidden="true"><span class="ms">eco</span></span>
                                    <div class="dash-timeline__head">
                                        <h3 class="dash-timeline__title">Uszczykiwanie świeczek</h3>
                                        <p class="dash-timeline__date dash-timeline__date--terra">Maj 15–22</p>
                                    </div>
                                </li>
                                <li class="dash-timeline__item">
                                    <span class="dash-timeline__marker dash-timeline__marker--green" aria-hidden="true"><span class="ms">grain</span></span>
                                    <div class="dash-timeline__head">
                                        <h3 class="dash-timeline__title">Nawożenie Biogold</h3>
                                        <p class="dash-timeline__date">Maj 26</p>
                                    </div>
                                </li>
                                <li class="dash-timeline__item">
                                    <span class="dash-timeline__marker" aria-hidden="true"><span class="ms">content_cut</span></span>
                                    <div class="dash-timeline__head">
                                        <h3 class="dash-timeline__title">Częściowa defoliacja</h3>
                                        <p class="dash-timeline__date">Czerwiec 05</p>
                                    </div>
                                </li>
                            </ol>
                            <a href="/schedule" class="btn btn--block dash-calendar__more">Cały harmonogram</a>
                        </section>

                        <section class="dash-panel dash-journal" aria-labelledby="dash-journal-title">
                            <div class="dash-panel__head">
                                <h2 class="dash-panel__title" id="dash-journal-title">Szybki dziennik</h2>
                                <span class="dash-journal__autosave">Autosave</span>
                            </div>
                            <label class="visually-hidden" for="dash-journal-entry">Nowy wpis w dzienniku</label>
                            <textarea class="textarea dash-journal__input" id="dash-journal-entry" rows="3" placeholder="Zanotuj przyrost pąków, stan gleby lub korektę drutu…"></textarea>
                            <div class="dash-journal__tools">
                                <button type="button" class="dash-journal__tool" aria-label="Dodaj zdjęcie"><span class="ms" aria-hidden="true">photo_camera</span></button>
                                <button type="button" class="dash-journal__tool" aria-label="Dodaj tag"><span class="ms" aria-hidden="true">tag</span></button>
                                <button type="button" class="dash-journal__tool" aria-label="Nagraj notatkę głosową"><span class="ms" aria-hidden="true">mic</span></button>
                                <button type="button" class="btn btn--primary btn--sm dash-journal__save">Zapisz wpis</button>
                            </div>
                        </section>
                    </aside>
                </div>

                <button type="button" class="dash-fab dash-mobile-only">
                    <span class="ms" aria-hidden="true">add_a_photo</span>
                    Nowe drzewko
                </button>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/partials/app-bottom-nav.html.php'; ?>
</body>
</html>
