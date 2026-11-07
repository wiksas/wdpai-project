<?php
/**
 * @var DateTimeImmutable $selected
 * @var DateTimeImmutable $today
 * @var bool $isToday
 * @var array<string, mixed> $week
 * @var list<array<string, mixed>> $tasks
 * @var int $urgentCount
 * @var string $summary
 * @var array<string, mixed> $sekki
 * @var array<string, mixed> $window
 * @var list<array<string, mixed>> $upcoming
 * @var array<string, string> $tip
 * @var array<string, string> $legend
 */
use App\Support\PolishDate;

$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$typeIcons = ['water' => 'water_drop', 'shape' => 'content_cut', 'care' => 'eco'];
$selectedIso = $selected->format('Y-m-d');
$todayIso = $today->format('Y-m-d');
$self = '/schedule?date=' . $selectedIso;
$todayInWeek = $week['days'][0]['date'] <= $todayIso && $todayIso <= $week['days'][6]['date'];
?>
<!DOCTYPE html>
<html lang="pl" data-theme="<?= htmlspecialchars((string) ($theme ?? 'system'), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/partials/head.html.php'; ?>
    <link rel="stylesheet" href="<?= $asset('/css/pages/schedule.css') ?>">
</head>
<body>
    <div class="app">
        <?php include __DIR__ . '/partials/app-sidebar.html.php'; ?>

        <div class="app-main">
            <?php include __DIR__ . '/partials/app-topbar.html.php'; ?>

            <main class="app-content schedule">
                <div class="schedule-layout">
                    <div class="schedule-main">
                        <header class="schedule-head">
                            <h1 class="schedule-head__title">Harmonogram</h1>
                            <div class="schedule-season">
                                <p class="schedule-season__name">
                                    <span class="term schedule-season__term"><?= $e($sekki['romaji']) ?> <span lang="ja"><?= $e($sekki['kanji']) ?></span></span>
                                    <span class="schedule-season__pl"><?= $e($sekki['name']) ?></span>
                                </p>
                                <p class="schedule-season__meta">Pora <?= (int) $sekki['number'] ?> z 24 sekki, dzień <?= (int) $sekki['dayNumber'] ?> z <?= (int) $sekki['length'] ?>. Następna: <?= $e($sekki['next']) ?>.</p>
                                <p class="schedule-season__text"><?= $e($sekki['blurb']) ?></p>
                            </div>
                        </header>

                        <section class="schedule-week" aria-labelledby="schedule-week-title" data-week>
                            <header class="schedule-week__head">
                                <h2 class="schedule-week__title" id="schedule-week-title">
                                    Tydzień <?= (int) $week['number'] ?>
                                    <span class="schedule-week__range"><?= $e($week['range']) ?></span>
                                </h2>
                                <div class="schedule-week__nav">
                                    <a class="schedule-week__arrow" href="/schedule?date=<?= $e($week['prev']) ?>" data-week-nav="prev" aria-label="Poprzedni tydzień" rel="prev">
                                        <span class="ms" aria-hidden="true">chevron_left</span>
                                    </a>
                                    <?php if (!$isToday || !$todayInWeek): ?>
                                        <a class="schedule-week__today" href="/schedule" data-week-nav="today">Dziś</a>
                                    <?php endif; ?>
                                    <a class="schedule-week__arrow" href="/schedule?date=<?= $e($week['next']) ?>" data-week-nav="next" aria-label="Następny tydzień" rel="next">
                                        <span class="ms" aria-hidden="true">chevron_right</span>
                                    </a>
                                </div>
                            </header>

                            <ol class="schedule-days" data-week-days>
                                <?php foreach ($week['days'] as $day): ?>
                                    <?php
                                    $dayTotal = (int) $day['total'];
                                    $dayOpen = (int) $day['open'];
                                    $dayLabel = $day['name'] . ', ' . (int) $day['day'] . ($day['isToday'] ? ' (dziś)' : '') . ': '
                                        . ($dayTotal === 0 ? 'brak zabiegów' : $dayTotal . ' ' . PolishDate::plural($dayTotal, 'zabieg', 'zabiegi', 'zabiegów') . ', do zrobienia ' . $dayOpen);
                                    ?>
                                    <li>
                                        <a class="schedule-day<?= $day['isSelected'] ? ' is-active' : '' ?><?= $day['isToday'] ? ' is-today' : '' ?>" href="/schedule?date=<?= $e($day['date']) ?>" data-day="<?= $e($day['date']) ?>"<?= $day['isSelected'] ? ' aria-current="date"' : '' ?> aria-label="<?= $e($dayLabel) ?>">
                                            <span class="schedule-day__label" aria-hidden="true"><?= $e(mb_convert_case((string) $day['label'], MB_CASE_TITLE, 'UTF-8')) ?></span>
                                            <span class="schedule-day__date" aria-hidden="true"><?= (int) $day['day'] ?></span>
                                            <span class="schedule-day__count" aria-hidden="true" data-day-count><?php if ($dayTotal > 0 && $dayOpen === 0): ?><span class="ms">done_all</span><?php elseif ($dayOpen > 0): ?><?= $dayOpen ?><?php endif; ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                            <p class="schedule-week__hint">Liczba pod datą to zabiegi do zrobienia tego dnia.</p>
                        </section>

                        <section class="schedule-today" aria-labelledby="schedule-today-title" data-day-panel>
                            <header class="schedule-section-head">
                                <div>
                                    <h2 class="schedule-section-head__title" id="schedule-today-title"><?= $isToday ? 'Zadania na dziś' : 'Zadania: ' . $e(mb_strtolower(PolishDate::dayName($selected))) ?></h2>
                                    <p class="schedule-today__date" data-day-summary><?= $e($summary) ?></p>
                                </div>
                                <p class="schedule-today__urgent" data-urgent<?= $urgentCount > 0 ? '' : ' hidden' ?>>Pilne: <?= $urgentCount ?></p>
                            </header>

                            <?php if ($tasks === []): ?>
                                <div class="schedule-empty">
                                    <p>Na ten dzień reguły pielęgnacji nie wyznaczyły podlewania ani formowania i nie dodano własnych zabiegów.</p>
                                    <a class="schedule-empty__action" href="#add-title" data-open-add>Dodaj własny zabieg</a>
                                </div>
                            <?php endif; ?>

                            <ul class="schedule-tasks" data-tasks>
                                <?php foreach ($tasks as $task): ?>
                                    <li class="schedule-task<?= $task['done'] ? ' is-done' : '' ?><?= !empty($task['custom']) ? ' is-custom' : '' ?>" data-task>
                                        <form class="schedule-task__form" action="/schedule/tasks/done" method="POST" data-task-form>
                                            <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                            <input type="hidden" name="id" value="<?= $e($task['id']) ?>">
                                            <input type="hidden" name="date" value="<?= $e($task['date']) ?>">
                                            <input type="hidden" name="_back" value="<?= $e($self) ?>">
                                            <input type="hidden" name="done" value="0">
                                            <label class="schedule-task__hit">
                                                <input class="schedule-task__check" type="checkbox" name="done" value="1" aria-label="Oznacz jako wykonane: <?= $e($task['title']) ?>"<?= $task['done'] ? ' checked' : '' ?>>
                                            </label>
                                            <div class="schedule-task__body">
                                                <p class="schedule-task__when">
                                                    <span class="ms schedule-task__icon" aria-hidden="true"><?= $e($typeIcons[$task['type']]) ?></span>
                                                    <span><?= $e($legend[$task['type']]) ?>, <?= $e($task['time']) ?></span>
                                                    <?php if ($task['urgent']): ?>
                                                        <strong class="schedule-task__urgent">Pilne</strong>
                                                    <?php endif; ?>
                                                </p>
                                                <h3 class="schedule-task__title">
                                                    <?php if ($task['treeId'] !== null): ?>
                                                        <a href="/collection/<?= (int) $task['treeId'] ?>"><?= $e($task['title']) ?></a>
                                                    <?php else: ?>
                                                        <?= $e($task['title']) ?>
                                                    <?php endif; ?>
                                                </h3>
                                                <?php if ($task['text'] !== ''): ?>
                                                    <p class="schedule-task__text"><?= $e($task['text']) ?></p>
                                                <?php endif; ?>
                                                <p class="schedule-task__meta">
                                                    <span><?= $e($task['meta']) ?></span>
                                                    <?php if ($task['note'] !== ''): ?>
                                                        <span class="schedule-task__note"><?= $e($task['note']) ?></span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            <button type="submit" class="schedule-task__save">Zapisz</button>
                                        </form>
                                        <?php if (!empty($task['custom'])): ?>
                                            <form class="schedule-task__remove" action="/schedule/tasks/delete" method="POST" data-task-delete>
                                                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                                <input type="hidden" name="task" value="<?= $e(substr((string) $task['key'], 7)) ?>">
                                                <input type="hidden" name="date" value="<?= $e($task['date']) ?>">
                                                <input type="hidden" name="_back" value="<?= $e($self) ?>">
                                                <button type="submit" class="schedule-task__delete" aria-label="Usuń zabieg: <?= $e($task['title']) ?>" title="Usuń zabieg">
                                                    <span class="ms" aria-hidden="true">delete</span>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </section>

                        <details class="schedule-add" data-add>
                            <summary class="schedule-add__toggle">
                                <span class="ms" aria-hidden="true">add</span>
                                Dodaj własny zabieg
                            </summary>
                            <form class="schedule-add__form" action="/schedule/tasks" method="POST" data-add-form>
                                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                <input type="hidden" name="date" value="<?= $e($selectedIso) ?>">
                                <input type="hidden" name="_back" value="<?= $e($self) ?>">
                                <p class="schedule-add__hint">Dzień: <strong><?= $e(PolishDate::full($selected)) ?></strong></p>
                                <div class="field">
                                    <label class="field__label" for="add-title">Nazwa zabiegu</label>
                                    <input class="input" id="add-title" name="title" type="text" maxlength="80" minlength="3" placeholder="np. Przesadzenie wiązu" required>
                                </div>
                                <div class="schedule-add__row">
                                    <div class="field">
                                        <label class="field__label" for="add-type">Rodzaj</label>
                                        <select class="select" id="add-type" name="type">
                                            <?php foreach ($legend as $type => $label): ?>
                                                <option value="<?= $e($type) ?>"<?= $type === 'care' ? ' selected' : '' ?>><?= $e($label) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label class="field__label" for="add-time">Godzina (opcjonalnie)</label>
                                        <input class="input" id="add-time" name="time" type="time">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="field__label" for="add-note">Notatka (opcjonalnie)</label>
                                    <textarea class="textarea" id="add-note" name="note" rows="2" maxlength="240" placeholder="Co przygotować, na co uważać"></textarea>
                                </div>
                                <button type="submit" class="btn btn--primary btn--block">Dodaj do harmonogramu</button>
                            </form>
                        </details>
                    </div>

                    <div class="schedule-side">
                        <section class="schedule-window" aria-labelledby="schedule-window-title">
                            <p class="schedule-window__kicker"><?= $window['state'] === 'open' ? 'Trwa okno sezonowe' : 'Najbliższe okno sezonowe' ?></p>
                            <h2 class="schedule-window__title" id="schedule-window-title"><?= $e($window['title']) ?></h2>
                            <p class="schedule-window__days">
                                <span class="schedule-window__count"><?= (int) $window['days'] ?></span>
                                <?= $e(PolishDate::plural((int) $window['days'], 'dzień', 'dni', 'dni')) ?> <?= $window['state'] === 'open' ? 'do końca' : 'do otwarcia' ?>
                            </p>
                            <p class="schedule-window__text"><?= $e($window['text']) ?></p>
                            <p class="schedule-window__species">Dotyczy: <?= $e($window['species']) ?></p>
                            <a class="schedule-window__link" href="/studio">Kalendarz zabiegów w Studio</a>
                        </section>

                        <section class="schedule-upcoming" aria-labelledby="schedule-upcoming-title">
                            <h2 class="schedule-section-head__title" id="schedule-upcoming-title">Najbliższe zabiegi</h2>
                            <p class="schedule-upcoming__scope">Kolejne 14 dni, bez podlewania</p>

                            <?php if ($upcoming === []): ?>
                                <div class="schedule-empty schedule-empty--compact">
                                    <p>W najbliższych dwóch tygodniach nie ma formowania, nawożenia ani własnych zabiegów.</p>
                                    <a class="schedule-empty__action" href="#add-title" data-open-add>Zaplanuj własny zabieg</a>
                                </div>
                            <?php endif; ?>

                            <ol class="schedule-upcoming__list">
                                <?php foreach ($upcoming as $event): ?>
                                    <li>
                                        <a class="schedule-event" href="/schedule?date=<?= $e($event['iso']) ?>" data-jump-date="<?= $e($event['iso']) ?>">
                                            <span class="schedule-event__when">
                                                <span class="schedule-event__date"><?= $e($event['date']) ?></span>
                                                <span class="schedule-event__in"><?= $e($event['in']) ?></span>
                                            </span>
                                            <span class="schedule-event__body">
                                                <span class="schedule-event__title"><?= $e($event['title']) ?></span>
                                                <span class="schedule-event__text"><?= $e($event['text']) ?></span>
                                            </span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </section>

                        <aside class="schedule-rule" aria-labelledby="schedule-rule-title">
                            <p class="schedule-rule__kicker"><?= $e($tip['eyebrow']) ?></p>
                            <h2 class="schedule-rule__title" id="schedule-rule-title"><?= $e($tip['title']) ?></h2>
                            <p class="schedule-rule__text"><?= $e($tip['text']) ?></p>
                        </aside>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/partials/app-bottom-nav.html.php'; ?>
    <?php include __DIR__ . '/partials/foot.html.php'; ?>
    <script src="<?= $asset('/js/schedule.js') ?>" defer></script>
</body>
</html>
