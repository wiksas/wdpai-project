<?php
/**
 * @var list<array<string, mixed>> $trees          drzewka po filtrach
 * @var int $treeCount                              wszystkie drzewka
 * @var array<string, int> $counts                  liczniki zakładek filtrów
 * @var list<array<string, mixed>> $rituals         drzewka z zabiegiem do wykonania
 * @var array<string, mixed> $summary
 * @var array{category: string, status: string, q: string} $criteria
 * @var list<array<string, mixed>> $journal
 * @var bool $misting
 */
use App\Support\PolishDate;

$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$statusStyles = [
    'ideal' => ['pill' => 'pill--green', 'icon' => 'check_circle', 'bar' => 'progress__bar--green', 'action' => 'btn--tonal', 'actionIcon' => 'check'],
    'shaping' => ['pill' => 'pill--green', 'icon' => 'architecture', 'bar' => 'progress__bar--green', 'action' => 'btn--tonal', 'actionIcon' => 'tune'],
    'attention' => ['pill' => 'pill--terra', 'icon' => 'humidity_low', 'bar' => 'progress__bar--terra', 'action' => 'btn--tertiary', 'actionIcon' => 'shower'],
    'critical' => ['pill' => 'pill--terra', 'icon' => 'water_drop', 'bar' => 'progress__bar--terra', 'action' => 'btn--primary', 'actionIcon' => 'done_all'],
];

$treeNoun = static fn (int $count): string => PolishDate::plural($count, 'drzewko', 'drzewka', 'drzewek');

$journalWhen = static function (int $timestamp): string {
    $moment = PolishDate::now()->setTimestamp($timestamp);
    $isToday = $moment->format('Y-m-d') === PolishDate::today()->format('Y-m-d');

    return ($isToday ? 'Dzisiaj' : PolishDate::dayMonth($moment)) . ', ' . $moment->format('H:i');
};

$firstName = explode(' ', trim((string) ($currentUser['name'] ?? 'Hodowca')))[0];
$activeFilter = $criteria['status'] === 'attention' ? 'attention' : ($criteria['category'] !== '' ? $criteria['category'] : 'all');
$hasFilters = $criteria['category'] !== '' || $criteria['status'] !== '' || $criteria['q'] !== '';
$query = static fn (array $params): string => '/collection' . ($params === [] ? '' : '?' . http_build_query($params));
$keepQ = $criteria['q'] !== '' ? ['q' => $criteria['q']] : [];
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
                        <p class="dash-garden__title" role="heading" aria-level="2">Kolekcja: <?= $e($firstName) ?></p>
                        <p class="dash-garden__wind">Lekki zefir (2 m/s)</p>
                    </div>
                </header>

                <section class="dash-vitality dash-mobile-only" aria-label="Witalność ogrodu">
                    <span class="dash-vitality__ring" style="--value: <?= (int) $summary['health'] ?>%"><span><?= (int) $summary['health'] ?>%</span></span>
                    <div class="dash-vitality__text">
                        <p class="dash-vitality__title"><span class="dot dot--dark" aria-hidden="true"></span>Witalność ogrodu: <?= $summary['health'] >= 90 ? 'Doskonała' : ($summary['health'] >= 75 ? 'Dobra' : 'Wymaga uwagi') ?></p>
                        <p class="dash-vitality__note">
                            <span class="ms" aria-hidden="true">water_drop</span>
                            <?= $summary['thirsty'] === 0 ? 'Wszystkie drzewka nawodnione' : (int) $summary['thirsty'] . ' ' . $e($treeNoun((int) $summary['thirsty'])) . ' oczekuje na podlanie' ?>
                        </p>
                    </div>
                    <a href="/collection" class="dash-vitality__refresh" aria-label="Odśwież odczyty" data-refresh-link>
                        <span class="ms" aria-hidden="true">sync</span>
                    </a>
                </section>

                <section class="dash-rituals dash-mobile-only" aria-labelledby="dash-rituals-title">
                    <div class="dash-rituals__head">
                        <h2 class="dash-rituals__title" id="dash-rituals-title">
                            <span class="dot dot--terra" aria-hidden="true"></span>
                            Pilne Rytuały
                            <span class="dash-rituals__count"><?= count($rituals) ?></span>
                        </h2>
                        <p class="dash-rituals__when">Przed zachodem słońca</p>
                    </div>
                    <?php if ($rituals === []): ?>
                        <p class="dash-empty">Wszystkie rytuały wykonane. Ogród jest spokojny.</p>
                    <?php else: ?>
                        <div class="dash-rituals__track">
                            <?php foreach ($rituals as $ritual): ?>
                                <?php $urgent = in_array($ritual['status'], ['attention', 'critical'], true); ?>
                                <article class="dash-ritual">
                                    <div class="dash-ritual__head">
                                        <span class="icon-tile icon-tile--sm <?= $urgent ? 'icon-tile--terra' : 'icon-tile--green' ?>" aria-hidden="true"><span class="ms"><?= $urgent ? 'water_drop' : 'show_chart' ?></span></span>
                                        <div>
                                            <h3 class="dash-ritual__name"><a href="/collection/<?= (int) $ritual['id'] ?>"><?= $e($ritual['commonName']) ?> (<?= $e($ritual['name']) ?>)</a></h3>
                                            <p class="dash-ritual__meta<?= $urgent ? ' dash-ritual__meta--terra' : '' ?>"><?= $urgent ? 'Wilgotność ' . ($ritual['moisture'] < 30 ? 'krytyczna' : 'niska') . ': ' . (int) $ritual['moisture'] . '%' : $e($ritual['care']['meta'] ?? '') ?></p>
                                        </div>
                                    </div>
                                    <p class="dash-ritual__text"><?= $e($ritual['alert']['title'] ?? ($ritual['care']['title'] ?? '')) ?>.</p>
                                    <form class="dash-ritual__foot" action="/collection/<?= (int) $ritual['id'] ?>/care" method="POST" data-care-form>
                                        <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                        <input type="hidden" name="type" value="<?= $e($ritual['actionType']) ?>">
                                        <input type="hidden" name="_back" value="/collection">
                                        <span class="dash-ritual__priority"><?= $urgent ? 'Priorytet: Wysoki' : 'Formowanie gałęzi' ?></span>
                                        <button type="submit" class="btn <?= $urgent ? 'btn--tertiary' : 'btn--tonal' ?> btn--sm dash-ritual__btn">
                                            <span class="ms" aria-hidden="true">check</span>
                                            <?= $urgent ? 'Zrobione' : 'Sprawdź' ?>
                                        </button>
                                    </form>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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
                                <span class="dash-stat__number"><?= (int) $summary['health'] ?>%</span>
                                <span class="dash-stat__trend"><span class="ms" aria-hidden="true">trending_up</span><?= $e($summary['healthLabel']) ?></span>
                            </p>
                        </div>
                        <span class="dash-stat__ring" aria-hidden="true"><span class="ms">potted_plant</span></span>
                    </article>
                    <article class="dash-stat">
                        <div class="dash-stat__body">
                            <h2 class="dash-stat__label">Okazy pod opieką</h2>
                            <p class="dash-stat__value">
                                <span class="dash-stat__number"><?= (int) $summary['total'] ?></span>
                                <span class="dash-stat__unit"><?= $e($treeNoun((int) $summary['total'])) ?></span>
                            </p>
                        </div>
                        <span class="dash-stat__icon" aria-hidden="true"><span class="ms">yard</span></span>
                    </article>
                    <article class="dash-stat dash-stat--terra">
                        <div class="dash-stat__body">
                            <h2 class="dash-stat__label">Nawadnianie dziś</h2>
                            <p class="dash-stat__value">
                                <span class="dash-stat__number"><?= (int) $summary['thirsty'] ?></span>
                                <span class="dash-stat__unit">wymaga wody</span>
                            </p>
                        </div>
                        <span class="dash-stat__icon dash-stat__icon--terra" aria-hidden="true"><span class="ms">water_drop</span></span>
                    </article>
                    <a class="dash-stat dash-stat--link" href="/schedule">
                        <div class="dash-stat__body">
                            <h2 class="dash-stat__label">Zabiegi dziś</h2>
                            <p class="dash-stat__value">
                                <span class="dash-stat__number"><?= (int) $tasksToday ?></span>
                                <span class="dash-stat__unit">do wykonania</span>
                            </p>
                        </div>
                        <span class="dash-stat__icon" aria-hidden="true"><span class="ms">event_available</span></span>
                    </a>
                </section>

                <div class="dash-layout">
                    <div class="dash-primary">
                        <div class="dash-collection" data-collection>
                            <section class="dash-filters" aria-label="Filtry kolekcji">
                                <nav class="dash-filters__tabs" aria-label="Kategorie okazów">
                                    <a href="<?= $e($query($keepQ)) ?>" class="dash-filter dash-filter--all<?= $activeFilter === 'all' ? ' is-active' : '' ?>" data-filter-link<?= $activeFilter === 'all' ? ' aria-current="page"' : '' ?>>Wszystkie (<?= (int) $counts['all'] ?>)</a>
                                    <a href="<?= $e($query($keepQ + ['category' => 'conifer'])) ?>" class="dash-filter dash-filter--conifer<?= $activeFilter === 'conifer' ? ' is-active' : '' ?>" data-filter-link<?= $activeFilter === 'conifer' ? ' aria-current="page"' : '' ?>>Iglaste (<?= (int) $counts['conifer'] ?>)</a>
                                    <a href="<?= $e($query($keepQ + ['category' => 'deciduous'])) ?>" class="dash-filter dash-filter--deciduous<?= $activeFilter === 'deciduous' ? ' is-active' : '' ?>" data-filter-link<?= $activeFilter === 'deciduous' ? ' aria-current="page"' : '' ?>>Liściaste (<?= (int) $counts['deciduous'] ?>)</a>
                                    <a href="<?= $e($query($keepQ + ['status' => 'attention'])) ?>" class="dash-filter dash-filter--attention<?= $activeFilter === 'attention' ? ' is-active' : '' ?>" data-filter-link<?= $activeFilter === 'attention' ? ' aria-current="page"' : '' ?>><span class="dot dot--terra" aria-hidden="true"></span>Wymagające uwagi (<?= (int) $counts['attention'] ?>)</a>
                                </nav>
                                <a href="/clinic" class="btn btn--primary btn--sm dash-filters__add">
                                    <span class="ms" aria-hidden="true">add_a_photo</span>
                                    Zeskanuj nowy okaz
                                </a>
                                <?php if ($criteria['q'] !== ''): ?>
                                    <p class="dash-filters__query">
                                        Wyniki dla „<strong><?= $e($criteria['q']) ?></strong>”
                                        <a href="<?= $e($query(array_filter(['category' => $criteria['category'], 'status' => $criteria['status']]))) ?>" data-filter-link class="dash-filters__clear">Wyczyść</a>
                                    </p>
                                <?php endif; ?>
                            </section>

                            <?php if ($trees === []): ?>
                                <div class="dash-empty dash-empty--card" role="status">
                                    <span class="ms" aria-hidden="true">search_off</span>
                                    <p>Nic nie pasuje do wybranych filtrów.</p>
                                    <?php if ($hasFilters): ?>
                                        <a href="/collection" class="btn btn--tonal btn--sm" data-filter-link>Pokaż wszystkie okazy</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <section class="dash-trees" aria-label="Okazy w kolekcji" data-trees>
                                <?php foreach ($trees as $tree): ?>
                                    <?php
                                    $status = (string) $tree['status'];
                                    $style = $statusStyles[$status] ?? $statusStyles['ideal'];
                                    $url = '/collection/' . (int) $tree['id'];
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
                                    <article class="dash-tree dash-tree--<?= $e($status) ?>" data-tree="<?= (int) $tree['id'] ?>">
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
                                            <a href="<?= $e($url) ?>" class="dash-tree__more" aria-label="Otwórz kartę okazu: <?= $e($tree['name']) ?>">
                                                <span class="ms" aria-hidden="true">arrow_outward</span>
                                            </a>
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
                                                <form class="dash-tree__actions" action="/collection/<?= (int) $tree['id'] ?>/care" method="POST" data-care-form>
                                                    <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                                    <input type="hidden" name="type" value="<?= $e($tree['actionType']) ?>">
                                                    <input type="hidden" name="_back" value="/collection">
                                                    <button type="submit" class="btn <?= $e($style['action']) ?> dash-tree__action">
                                                        <span class="ms" aria-hidden="true"><?= $e($style['actionIcon']) ?></span>
                                                        <?= $e($tree['action']) ?>
                                                    </button>
                                                    <?php if ($status === 'attention'): ?>
                                                        <a href="<?= $e($url) ?>" class="dash-tree__vitals" aria-label="Parametry życiowe: <?= $e($tree['name']) ?>">
                                                            <span class="ms" aria-hidden="true">vital_signs</span>
                                                        </a>
                                                    <?php endif; ?>
                                                </form>
                                            <?php elseif ($tree['done'] !== null): ?>
                                                <p class="dash-tree__done">
                                                    <span class="ms" aria-hidden="true">task_alt</span>
                                                    <?= $tree['done'] === 'water' ? 'Podlanie zarejestrowane dziś' : 'Korekta zapisana dziś' ?>
                                                </p>
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
                        </div>

                        <section class="dash-season" aria-labelledby="dash-season-title">
                            <div class="dash-season__text">
                                <p class="eyebrow eyebrow--green dash-season__eyebrow">Cykl rozwojowy • <?= $e(PolishDate::month(PolishDate::today())) ?></p>
                                <h2 class="dash-season__title" id="dash-season-title">Plan sezonu</h2>
                                <p class="dash-season__desc">Zobacz najbliższe okna zabiegowe i zadania na każdy dzień.</p>
                            </div>
                            <div class="dash-season__actions">
                                <a href="/studio" class="btn dash-season__export">Studio formowania</a>
                                <a href="/schedule" class="btn btn--primary dash-season__guide">
                                    <span class="ms" aria-hidden="true">menu_book</span>
                                    Harmonogram
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
                            <form action="/misting" method="POST" data-misting-form>
                                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                <input type="hidden" name="_back" value="/collection">
                                <input type="hidden" name="on" value="0">
                                <label class="dash-toggle">
                                    <span class="ms" aria-hidden="true">humidity_high</span>
                                    <span class="dash-toggle__label">Automatyczne zamgławianie</span>
                                    <input type="checkbox" class="dash-toggle__input" name="on" value="1"<?= $misting ? ' checked' : '' ?>>
                                    <span class="dash-toggle__switch" aria-hidden="true"></span>
                                </label>
                                <button type="submit" class="btn btn--tonal btn--sm btn--block dash-nojs-save">Zapisz ustawienie</button>
                            </form>
                        </section>

                        <section class="dash-panel dash-calendar" aria-labelledby="dash-calendar-title">
                            <div class="dash-panel__head">
                                <div>
                                    <p class="eyebrow eyebrow--muted">Kalendarz prac</p>
                                    <h2 class="dash-panel__title" id="dash-calendar-title"><?= $e(PolishDate::month(PolishDate::today())) ?> • Okres <?= $e($sekkiLabel ?? 'sezonowy') ?></h2>
                                </div>
                                <span class="ms dash-calendar__icon" aria-hidden="true">calendar_today</span>
                            </div>
                            <ol class="dash-timeline">
                                <?php foreach ($upcoming as $index => $event): ?>
                                    <li class="dash-timeline__item">
                                        <span class="dash-timeline__marker<?= $event['tone'] === 'terra' ? ' dash-timeline__marker--terra' : ($event['tone'] === 'green' ? ' dash-timeline__marker--green' : '') ?>" aria-hidden="true"><span class="ms"><?= $event['tone'] === 'terra' ? 'content_cut' : ($event['tone'] === 'green' ? 'eco' : 'water_drop') ?></span></span>
                                        <div class="dash-timeline__head">
                                            <h3 class="dash-timeline__title"><a href="/schedule?date=<?= $e($event['iso']) ?>"><?= $e($event['title']) ?></a></h3>
                                            <p class="dash-timeline__date<?= $event['tone'] === 'terra' ? ' dash-timeline__date--terra' : '' ?>"><?= $e($event['date']) ?></p>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                                <?php if ($upcoming === []): ?>
                                    <li class="dash-empty">Brak zaplanowanych zabiegów w najbliższych dniach.</li>
                                <?php endif; ?>
                            </ol>
                            <a href="/schedule" class="btn btn--block dash-calendar__more">Cały harmonogram</a>
                        </section>

                        <section class="dash-panel dash-journal" aria-labelledby="dash-journal-title">
                            <div class="dash-panel__head">
                                <h2 class="dash-panel__title" id="dash-journal-title">Szybki dziennik</h2>
                                <span class="dash-journal__autosave" data-autosave>Autosave</span>
                            </div>
                            <form action="/journal" method="POST" data-journal-form>
                                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                <input type="hidden" name="_back" value="/collection">
                                <label class="visually-hidden" for="dash-journal-entry">Nowy wpis w dzienniku</label>
                                <textarea class="textarea dash-journal__input" id="dash-journal-entry" name="text" rows="3" maxlength="500" minlength="3" required placeholder="Zanotuj przyrost pąków, stan gleby lub korektę drutu…"></textarea>
                                <div class="dash-journal__tools">
                                    <span class="dash-journal__counter" data-journal-counter aria-live="polite">0 / 500</span>
                                    <button type="submit" class="btn btn--primary btn--sm dash-journal__save">Zapisz wpis</button>
                                </div>
                            </form>
                            <div class="dash-journal__list" data-journal-list>
                                <?php foreach ($journal as $entry): ?>
                                    <article class="dash-journal__entry">
                                        <p class="dash-journal__entry-time"><?= $e($journalWhen((int) $entry['at'])) ?></p>
                                        <p class="dash-journal__entry-text"><?= $e($entry['text']) ?></p>
                                    </article>
                                <?php endforeach; ?>
                                <p class="dash-empty" data-journal-empty<?= $journal === [] ? '' : ' hidden' ?>>Brak wpisów. Zanotuj pierwszą obserwację.</p>
                            </div>
                        </section>
                    </aside>
                </div>

                <a href="/clinic" class="dash-fab dash-mobile-only">
                    <span class="ms" aria-hidden="true">add_a_photo</span>
                    Zeskanuj okaz
                </a>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/partials/app-bottom-nav.html.php'; ?>
    <?php include __DIR__ . '/partials/foot.html.php'; ?>
    <script src="<?= $asset('/js/collection.js') ?>" defer></script>
</body>
</html>
