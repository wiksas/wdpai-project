<?php
// Układ: layouts/app.html.php
$layout = 'app';
$mainClass = 'dash';
$pageStyles = ['dashboard'];
$pageScripts = ['collection'];

/**
 * @var list<array<string, mixed>> $trees          drzewka po filtrach
 * @var int $treeCount                              wszystkie drzewka
 * @var array<string, int> $counts                  liczniki zakładek filtrów
 * @var list<array<string, mixed>> $dueTrees        drzewka z zabiegiem do wykonania, od najpilniejszego
 * @var int $doneToday                              drzewka z zabiegiem zapisanym dzisiaj
 * @var list<array<string, mixed>> $upcoming        najbliższe zabiegi z harmonogramu
 * @var array<string, mixed> $sekki                 bieżąca pora roku (24 sekki)
 * @var array{category: string, status: string, q: string} $criteria
 * @var list<array<string, mixed>> $journal
 * @var int $tasksToday                             otwarte zadania harmonogramu na dziś
 */
use App\Support\PolishDate;

$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$treeNoun = static fn (int $count): string => PolishDate::plural($count, 'drzewko', 'drzewka', 'drzewek');
$alertStatuses = ['attention', 'critical'];

$journalWhen = static function (int $timestamp): string {
    $moment = PolishDate::now()->setTimestamp($timestamp);
    $isToday = $moment->format('Y-m-d') === PolishDate::today()->format('Y-m-d');

    return ($isToday ? 'Dzisiaj' : PolishDate::dayMonth($moment)) . ', ' . $moment->format('H:i');
};

$waterDue = count(array_filter($dueTrees, static fn (array $t): bool => $t['actionType'] === 'water'));
$adjustDue = count($dueTrees) - $waterDue;

$activeFilter = $criteria['status'] === 'attention' ? 'attention' : ($criteria['category'] !== '' ? $criteria['category'] : 'all');
$hasFilters = $criteria['category'] !== '' || $criteria['status'] !== '';
$query = static fn (array $params): string => '/collection' . ($params === [] ? '' : '?' . http_build_query($params));
$keepQ = $criteria['q'] !== '' ? ['q' => $criteria['q']] : [];
$keepFilters = array_filter(['category' => $criteria['category'], 'status' => $criteria['status']]);
$filters = [
    'all' => ['label' => 'Wszystkie', 'params' => []],
    'conifer' => ['label' => 'Iglaste', 'params' => ['category' => 'conifer']],
    'deciduous' => ['label' => 'Liściaste', 'params' => ['category' => 'deciduous']],
    'attention' => ['label' => 'Wymagające uwagi', 'params' => ['status' => 'attention']],
];
?>
<header class="dash-head">
    <h1 class="dash-head__title">Kolekcja</h1>
    <p class="dash-head__summary" data-summary>
        <?= (int) $treeCount ?> <?= $e($treeNoun((int) $treeCount)) ?> w kolekcji.
        <?php if ($dueTrees === []): ?>
            Dziś nic nie czeka na podlanie ani korektę.
        <?php else: ?>
            Teraz: <?= implode(', ', array_filter([
                $waterDue > 0 ? $waterDue . ' do podlania' : '',
                $adjustDue > 0 ? $adjustDue . ' do korekty drutu' : '',
            ])) ?>.
        <?php endif; ?>
        <a href="/schedule">Harmonogram na dziś: <?= (int) $tasksToday ?> <?= $e(PolishDate::plural((int) $tasksToday, 'otwarte zadanie', 'otwarte zadania', 'otwartych zadań')) ?></a>
    </p>
    <a href="/collection/new" class="btn btn--primary dash-head__add">
        <span class="ms" aria-hidden="true">add</span>
        Dodaj drzewko
    </a>
</header>

<div class="dash-layout">
    <div class="dash-primary">
        <section class="dash-due" aria-labelledby="dash-due-title" data-due>
            <h2 class="dash-section-title" id="dash-due-title">Wymaga zabiegu teraz</h2>
            <?php if ($dueTrees === []): ?>
                <div class="dash-empty dash-empty--due">
                    <p class="dash-empty__title"><?= $doneToday > 0 ? 'Dzisiejsze zabiegi są zapisane.' : 'Żadne drzewko nie czeka na zabieg.' ?></p>
                    <p>Wilgotność podłoża i drut są w normie. Kolejne prace (nawożenie, uszczykiwanie) znajdziesz w harmonogramie.</p>
                    <a href="/schedule" class="btn btn--tonal btn--sm">Otwórz harmonogram</a>
                </div>
            <?php else: ?>
                <ol class="dash-due__list">
                    <?php foreach ($dueTrees as $index => $tree): ?>
                        <?php
                        $url = '/collection/' . (int) $tree['id'];
                        $isLead = $index === 0;
                        $isAlert = in_array($tree['status'], $alertStatuses, true);
                        $isWater = $tree['actionType'] === 'water';
                        $reason = $tree['alert']['title'] ?? ($tree['care']['title'] ?? '');
                        $detail = $tree['alert']['text'] ?? ($tree['care']['text'] ?? '');
                        ?>
                        <li class="dash-due__item<?= $isLead ? ' dash-due__item--lead' : '' ?><?= $isAlert ? ' is-alert' : '' ?>">
                            <?php if ($isLead): ?>
                                <a href="<?= $e($url) ?>" class="dash-due__media" tabindex="-1" aria-hidden="true">
                                    <img src="<?= $e($tree['image']) ?>" alt="" width="640" height="480">
                                </a>
                            <?php endif; ?>
                            <div class="dash-due__body">
                                <h3 class="dash-due__name">
                                    <a href="<?= $e($url) ?>"><span class="term"><?= $e($tree['name']) ?> <span lang="ja"><?= $e($tree['kanji']) ?></span></span></a>
                                </h3>
                                <p class="dash-due__species"><?= $e($tree['commonName']) ?>, <i><?= $e($tree['species']) ?></i></p>
                                <p class="dash-due__reason"><?= $e($reason) ?></p>
                                <?php if ($isLead && $detail !== ''): ?>
                                    <p class="dash-due__detail"><?= $e($detail) ?></p>
                                <?php endif; ?>
                                <?php if ($isWater): ?>
                                    <p class="dash-due__facts">Wilgotność podłoża <?= (int) $tree['moisture'] ?>% (<?= $e(mb_strtolower((string) $tree['moistureLabel'])) ?>), podlano: <?= $e($tree['lastWatering']) ?></p>
                                    <?php if ($isLead): ?>
                                        <div class="progress progress--thin dash-due__meter" role="progressbar" aria-label="Wilgotność podłoża: <?= $e($tree['name']) ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= (int) $tree['moisture'] ?>">
                                            <span class="progress__bar <?= $isAlert ? 'progress__bar--terra' : 'progress__bar--green' ?>" style="--value: <?= (int) $tree['moisture'] ?>%"></span>
                                        </div>
                                    <?php endif; ?>
                                <?php elseif (isset($tree['care']['meta'])): ?>
                                    <p class="dash-due__facts"><?= $e($tree['care']['meta']) ?></p>
                                <?php endif; ?>
                            </div>
                            <form class="dash-due__form" action="/collection/<?= (int) $tree['id'] ?>/care" method="POST" data-care-form>
                                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                <input type="hidden" name="type" value="<?= $e($tree['actionType']) ?>">
                                <input type="hidden" name="_back" value="/collection">
                                <button type="submit" class="btn <?= $isLead && $isAlert ? 'btn--tertiary' : 'btn--tonal' ?> dash-due__btn">
                                    <?= $e($tree['action']) ?><span class="visually-hidden">: <?= $e($tree['name']) ?></span>
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </section>

        <section class="dash-trees" aria-labelledby="dash-trees-title">
            <div class="dash-trees__head">
                <h2 class="dash-section-title" id="dash-trees-title">Wszystkie okazy</h2>
                <form class="dash-search" action="/collection" method="GET" role="search">
                    <label class="dash-search__label" for="dash-search">Szukaj w kolekcji</label>
                    <div class="search dash-search__field">
                        <span class="ms" aria-hidden="true">search</span>
                        <input class="search__input" type="search" id="dash-search" name="q" value="<?= $e($criteria['q']) ?>" placeholder="Nazwa, gatunek, styl lub donica" autocomplete="off" data-search>
                    </div>
                    <?php foreach ($keepFilters as $name => $value): ?>
                        <input type="hidden" name="<?= $e($name) ?>" value="<?= $e($value) ?>">
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn--tonal dash-search__submit">Szukaj</button>
                </form>
            </div>

            <div class="dash-collection" data-collection>
                <nav class="dash-filters" aria-label="Filtr okazów">
                    <?php foreach ($filters as $key => $filter): ?>
                        <a href="<?= $e($query($keepQ + $filter['params'])) ?>" class="dash-filter<?= $activeFilter === $key ? ' is-active' : '' ?>" data-filter-link<?= $activeFilter === $key ? ' aria-current="page"' : '' ?>>
                            <?= $e($filter['label']) ?> <span class="dash-filter__count"><?= (int) $counts[$key] ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <?php if ($criteria['q'] !== '' && $trees !== []): ?>
                    <p class="dash-query" role="status">
                        Wyniki dla „<?= $e($criteria['q']) ?>”: <?= count($trees) ?> <?= $e($treeNoun(count($trees))) ?>.
                        <a href="<?= $e($query($keepFilters)) ?>" data-filter-link>Wyczyść wyszukiwanie</a>
                    </p>
                <?php endif; ?>

                <?php if ($trees === []): ?>
                    <div class="dash-empty dash-empty--trees" role="status">
                        <?php if ($treeCount === 0): ?>
                            <p class="dash-empty__title">Kolekcja jest pusta.</p>
                            <p>Dodaj pierwsze drzewko: nazwę, gatunek, styl i wiek. Zdjęcie możesz dołożyć od razu albo później na karcie okazu.</p>
                            <div class="dash-empty__actions">
                                <a href="/collection/new" class="btn btn--primary btn--sm">Dodaj drzewko</a>
                            </div>
                        <?php elseif ($criteria['q'] !== ''): ?>
                            <p class="dash-empty__title">Żaden okaz nie pasuje do „<?= $e($criteria['q']) ?>”<?= $hasFilters ? ' w wybranym filtrze' : '' ?>.</p>
                            <p>Szukamy w nazwie, kanji, gatunku, stylu i donicy. Sprawdź pisownię albo wyczyść wyszukiwanie.</p>
                        <?php else: ?>
                            <p class="dash-empty__title">Brak okazów w tym filtrze.</p>
                            <p>Żadne drzewko w kolekcji nie spełnia tego warunku.</p>
                        <?php endif; ?>
                        <?php if ($treeCount > 0): ?>
                            <div class="dash-empty__actions">
                                <?php if ($criteria['q'] !== '' && $hasFilters): ?>
                                    <a href="<?= $e($query($keepFilters)) ?>" class="btn btn--tonal btn--sm" data-filter-link>Wyczyść wyszukiwanie</a>
                                <?php endif; ?>
                                <a href="/collection" class="btn btn--primary btn--sm" data-filter-link>Pokaż wszystkie okazy</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <ul class="dash-list" data-trees>
                        <?php foreach ($trees as $tree): ?>
                            <?php
                            $url = '/collection/' . (int) $tree['id'];
                            $isAlert = in_array($tree['status'], $alertStatuses, true);
                            ?>
                            <li class="dash-row" data-tree="<?= (int) $tree['id'] ?>">
                                <a href="<?= $e($url) ?>" class="dash-row__thumb" tabindex="-1" aria-hidden="true">
                                    <img src="<?= $e($tree['image']) ?>" alt="" width="80" height="80" loading="lazy">
                                </a>
                                <div class="dash-row__main">
                                    <h3 class="dash-row__name">
                                        <a href="<?= $e($url) ?>"><span class="term"><?= $e($tree['name']) ?> <span lang="ja"><?= $e($tree['kanji']) ?></span></span></a>
                                    </h3>
                                    <p class="dash-row__species"><?= $e($tree['commonName']) ?>, <i><?= $e($tree['species']) ?></i></p>
                                    <p class="dash-row__meta"><?= $e($tree['ageLabel']) ?> · <?= $e($tree['style']) ?> (<?= $e(mb_strtolower((string) $tree['styleDescription'])) ?>) · donica <?= $e($tree['pot']) ?></p>
                                </div>
                                <div class="dash-row__state">
                                    <p class="dash-row__status<?= $isAlert ? ' is-alert' : '' ?>"><?= $e($tree['statusLabel']) ?></p>
                                    <p class="dash-row__moisture">Wilgotność <?= (int) $tree['moisture'] ?>%</p>
                                    <div class="progress progress--thin dash-row__meter" role="progressbar" aria-label="Wilgotność podłoża: <?= $e($tree['name']) ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= (int) $tree['moisture'] ?>">
                                        <span class="progress__bar <?= $isAlert ? 'progress__bar--terra' : 'progress__bar--green' ?>" style="--value: <?= (int) $tree['moisture'] ?>%"></span>
                                    </div>
                                    <p class="dash-row__water">Podlano: <?= $e($tree['lastWatering']) ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <aside class="dash-side" aria-label="Harmonogram i dziennik">
        <section class="dash-panel dash-calendar" aria-labelledby="dash-calendar-title">
            <h2 class="dash-panel__title" id="dash-calendar-title">Najbliższe zabiegi</h2>
            <p class="dash-calendar__season">
                Pora roku: <span class="term"><?= $e($sekki['romaji']) ?> <span lang="ja"><?= $e($sekki['kanji']) ?></span></span>
                <span class="dash-calendar__season-name">(<?= $e($sekki['name']) ?>)</span>
            </p>
            <?php if ($upcoming === []): ?>
                <p class="dash-empty dash-empty--inline">Na najbliższe 14 dni nie ma zabiegów poza podlewaniem. Własny zabieg dodasz w harmonogramie.</p>
            <?php else: ?>
                <ol class="dash-timeline">
                    <?php foreach ($upcoming as $event): ?>
                        <li class="dash-timeline__item">
                            <a class="dash-timeline__title" href="/schedule?date=<?= $e($event['iso']) ?>"><?= $e($event['title']) ?></a>
                            <p class="dash-timeline__date"><?= $e($event['date']) ?>, <?= $e($event['in']) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
            <a href="/schedule" class="btn btn--tonal btn--sm btn--block">Otwórz harmonogram</a>
        </section>

        <section class="dash-panel dash-journal" aria-labelledby="dash-journal-title">
            <h2 class="dash-panel__title" id="dash-journal-title">Dziennik obserwacji</h2>
            <form action="/journal" method="POST" data-journal-form>
                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                <input type="hidden" name="_back" value="/collection">
                <label class="dash-journal__label" for="dash-journal-entry">Nowy wpis</label>
                <textarea class="textarea dash-journal__input" id="dash-journal-entry" name="text" rows="3" maxlength="500" minlength="3" required placeholder="Np. przyrost pąków, stan podłoża, korekta drutu"></textarea>
                <div class="dash-journal__tools">
                    <span class="dash-journal__status">
                        <span data-journal-counter>0 / 500</span>
                        <span class="dash-journal__draft" data-autosave aria-live="polite"></span>
                    </span>
                    <button type="submit" class="btn btn--primary btn--sm">Zapisz wpis</button>
                </div>
            </form>
            <div class="dash-journal__list" data-journal-list>
                <?php foreach ($journal as $entry): ?>
                    <article class="dash-journal__entry">
                        <p class="dash-journal__entry-time"><?= $e($journalWhen((int) $entry['at'])) ?></p>
                        <p class="dash-journal__entry-text"><?= $e($entry['text']) ?></p>
                    </article>
                <?php endforeach; ?>
                <p class="dash-empty dash-empty--inline" data-journal-empty<?= $journal === [] ? '' : ' hidden' ?>>Brak wpisów. Pierwsza notatka pojawi się tutaj po zapisaniu.</p>
            </div>
        </section>
    </aside>
</div>
