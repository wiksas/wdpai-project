<?php
/**
 * @var DateTimeImmutable $selected
 * @var DateTimeImmutable $today
 * @var bool $isToday
 * @var array<string, mixed> $week
 * @var list<array<string, mixed>> $tasks
 * @var int $openCount
 * @var int $urgentCount
 * @var array<string, mixed> $sekki
 * @var array<string, mixed> $window
 * @var list<array<string, mixed>> $upcoming
 * @var array<string, string> $tip
 * @var array<string, string> $legend
 * @var array<string, string> $tones
 */
use App\Support\PolishDate;

$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$typeIcons = ['water' => 'water_drop', 'shape' => 'content_cut', 'care' => 'eco'];
$selectedIso = $selected->format('Y-m-d');
$total = count($tasks);
$self = '/schedule?date=' . $selectedIso;
?>
<!DOCTYPE html>
<html lang="pl">
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
                <h1 class="visually-hidden">Kalendarz Zabiegów i Zadań</h1>

                <div class="schedule-layout">
                    <div class="schedule-main">
                        <section class="schedule-season" aria-labelledby="schedule-season-title">
                            <div class="schedule-season__info">
                                <span class="schedule-season__tag">
                                    <span class="dot dot--terra" aria-hidden="true"></span>
                                    <?= (int) $sekki['number'] ?>. SEKKI (<span lang="ja"><?= $e($sekki['kanji']) ?></span>)
                                </span>
                                <h2 class="schedule-season__title" id="schedule-season-title"><?= $e($sekki['romaji']) ?> • <span lang="ja"><?= $e($sekki['kanji']) ?></span></h2>
                                <p class="schedule-season__text"><?= $e($sekki['name']) ?> • <?= $e($sekki['blurb']) ?></p>
                            </div>
                            <div class="schedule-season__date">
                                <span class="schedule-season__month"><?= $e($sekki['month']) ?></span>
                                <span class="schedule-season__ko">Dzień <?= (int) $sekki['dayNumber'] ?> z <?= (int) $sekki['length'] ?> • dalej: <?= $e($sekki['next']) ?></span>
                            </div>
                        </section>

                        <section class="schedule-week" aria-labelledby="schedule-week-title" data-week>
                            <header class="schedule-week__head">
                                <h2 class="schedule-week__title" id="schedule-week-title">
                                    <span class="ms" aria-hidden="true">calendar_month</span>
                                    Tydzień <?= (int) $week['number'] ?>
                                    <span class="schedule-week__range"><?= $e($week['range']) ?></span>
                                </h2>
                                <div class="schedule-week__nav">
                                    <a class="schedule-week__arrow" href="/schedule?date=<?= $e($week['prev']) ?>" data-week-nav="prev" aria-label="Poprzedni tydzień" rel="prev">
                                        <span class="ms" aria-hidden="true">chevron_left</span>
                                    </a>
                                    <?php if (!$isToday || $week['days'][0]['date'] > $today->format('Y-m-d') || $week['days'][6]['date'] < $today->format('Y-m-d')): ?>
                                        <a class="schedule-week__today" href="/schedule" data-week-nav="today">Dziś</a>
                                    <?php endif; ?>
                                    <a class="schedule-week__arrow" href="/schedule?date=<?= $e($week['next']) ?>" data-week-nav="next" aria-label="Następny tydzień" rel="next">
                                        <span class="ms" aria-hidden="true">chevron_right</span>
                                    </a>
                                </div>
                            </header>

                            <ol class="schedule-days" data-week-days>
                                <?php foreach ($week['days'] as $day): ?>
                                    <li>
                                        <a class="schedule-day<?= $day['isSelected'] ? ' is-active' : '' ?><?= $day['isToday'] ? ' is-today' : '' ?>" href="/schedule?date=<?= $e($day['date']) ?>" data-day="<?= $e($day['date']) ?>"<?= $day['isSelected'] ? ' aria-current="date"' : '' ?> aria-label="<?= $e($day['name']) ?>, <?= (int) $day['day'] ?>: <?= (int) $day['total'] ?> <?= $e(PolishDate::plural((int) $day['total'], 'zabieg', 'zabiegi', 'zabiegów')) ?>">
                                            <span class="schedule-day__label" aria-hidden="true"><?= $e($day['label']) ?></span>
                                            <span class="schedule-day__date"><?= (int) $day['day'] ?></span>
                                            <span class="schedule-day__dots" aria-hidden="true">
                                                <?php foreach ($day['tones'] as $tone): ?>
                                                    <span class="schedule-dot schedule-dot--<?= $e($tone) ?>"></span>
                                                <?php endforeach; ?>
                                            </span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ol>

                            <ul class="schedule-legend" aria-label="Legenda zabiegów">
                                <?php foreach ($legend as $type => $label): ?>
                                    <li class="schedule-legend__item">
                                        <span class="schedule-dot schedule-dot--<?= $e($tones[$type]) ?>" aria-hidden="true"></span>
                                        <?= $e($label) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </section>

                        <section class="schedule-today" aria-labelledby="schedule-today-title" data-day-panel>
                            <header class="schedule-section-head">
                                <div>
                                    <h2 class="schedule-section-head__title" id="schedule-today-title"><?= $isToday ? 'Zadania na Dziś' : 'Zadania: ' . $e(PolishDate::dayName($selected)) ?></h2>
                                    <p class="schedule-today__date" data-day-summary><?= $e(PolishDate::full($selected)) ?> • <?= $total ?> <?= $e(PolishDate::plural($total, 'zaplanowany zabieg', 'zaplanowane zabiegi', 'zaplanowanych zabiegów')) ?><?= $openCount < $total ? ' • wykonano ' . ($total - $openCount) : '' ?></p>
                                </div>
                                <?php if ($urgentCount > 0): ?>
                                    <span class="schedule-pill schedule-pill--urgent" data-urgent><?= $urgentCount ?> Pilne</span>
                                <?php endif; ?>
                            </header>

                            <?php if ($tasks === []): ?>
                                <p class="schedule-empty">Brak zaplanowanych zabiegów na ten dzień. Możesz dodać własny poniżej.</p>
                            <?php endif; ?>

                            <ul class="schedule-tasks" data-tasks>
                                <?php foreach ($tasks as $task): ?>
                                    <li class="schedule-task<?= $task['done'] ? ' is-done' : '' ?>" data-task>
                                        <form class="schedule-task__form" action="/schedule/tasks/done" method="POST" data-task-form>
                                            <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                            <input type="hidden" name="id" value="<?= $e($task['id']) ?>">
                                            <input type="hidden" name="date" value="<?= $e($task['date']) ?>">
                                            <input type="hidden" name="_back" value="<?= $e($self) ?>">
                                            <input type="hidden" name="done" value="0">
                                            <input class="schedule-task__check" type="checkbox" name="done" value="1" aria-label="Oznacz jako wykonane: <?= $e($task['title']) ?>"<?= $task['done'] ? ' checked' : '' ?>>
                                            <div class="schedule-task__body">
                                                <div class="schedule-task__top">
                                                    <?php if (!empty($task['custom'])): ?>
                                                        <span class="schedule-pill schedule-pill--routine">Własny</span>
                                                    <?php elseif ($task['urgent']): ?>
                                                        <span class="schedule-pill schedule-pill--urgent">Pilne</span>
                                                    <?php else: ?>
                                                        <span class="schedule-pill schedule-pill--routine">Rutynowe</span>
                                                    <?php endif; ?>
                                                    <span class="schedule-task__time"><?= $e($task['time']) ?></span>
                                                </div>
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
                                                <div class="schedule-task__meta">
                                                    <span class="schedule-task__specimen">
                                                        <span class="ms schedule-task__icon<?= $task['type'] === 'shape' ? ' schedule-task__icon--terra' : '' ?>" aria-hidden="true"><?= $e($typeIcons[$task['type']]) ?></span>
                                                        <?= $e($task['meta']) ?>
                                                    </span>
                                                    <?php if ($task['note'] !== ''): ?>
                                                        <span class="schedule-task__note<?= $task['urgent'] ? ' schedule-task__note--terra' : '' ?>"><?= $e($task['note']) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <button type="submit" class="schedule-task__save">Zapisz</button>
                                        </form>
                                        <?php if (!empty($task['custom'])): ?>
                                            <form class="schedule-task__remove" action="/schedule/tasks/delete" method="POST" data-task-delete>
                                                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                                <input type="hidden" name="task" value="<?= $e(substr((string) $task['key'], 7)) ?>">
                                                <input type="hidden" name="date" value="<?= $e($task['date']) ?>">
                                                <input type="hidden" name="_back" value="<?= $e($self) ?>">
                                                <button type="submit" class="schedule-task__delete" aria-label="Usuń zabieg: <?= $e($task['title']) ?>">
                                                    <span class="ms" aria-hidden="true">close</span>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                    </div>

                    <div class="schedule-side">
                        <section class="schedule-window" aria-labelledby="schedule-window-title">
                            <div class="schedule-window__head">
                                <div>
                                    <span class="schedule-window__eyebrow">
                                        <span class="ms ms--fill" aria-hidden="true">hourglass_bottom</span>
                                        <?= $window['state'] === 'open' ? 'OKNO SEZONOWE' : 'NADCHODZĄCE OKNO' ?>
                                    </span>
                                    <h2 class="schedule-window__title" id="schedule-window-title"><?= $e($window['title']) ?></h2>
                                </div>
                                <p class="schedule-window__counter">
                                    <span class="schedule-window__count"><?= (int) $window['days'] ?></span>
                                    <span class="schedule-window__unit"><?= $e(PolishDate::plural((int) $window['days'], 'dzień', 'dni', 'dni')) ?></span>
                                </p>
                            </div>
                            <p class="schedule-window__text"><?= $e($window['text']) ?></p>
                            <div class="schedule-window__foot">
                                <span class="schedule-window__species"><?= $e($window['species']) ?></span>
                                <a class="schedule-window__link" href="/studio">Wytyczne techniki →</a>
                            </div>
                        </section>

                        <section class="schedule-upcoming" aria-labelledby="schedule-upcoming-title">
                            <header class="schedule-section-head schedule-section-head--center">
                                <h2 class="schedule-section-head__title" id="schedule-upcoming-title">Nadchodzące 14 dni</h2>
                                <span class="schedule-upcoming__sekki">Sekki: <?= $e($sekki['romaji']) ?> / <?= $e($sekki['next']) ?></span>
                            </header>

                            <?php if ($upcoming === []): ?>
                                <p class="schedule-empty">W najbliższych dwóch tygodniach nie zaplanowano zabiegów formowania.</p>
                            <?php endif; ?>

                            <ol class="schedule-timeline">
                                <?php foreach ($upcoming as $event): ?>
                                    <li class="schedule-event">
                                        <span class="schedule-event__dot schedule-dot--<?= $e($event['tone']) ?>" aria-hidden="true"></span>
                                        <a class="schedule-event__card" href="/schedule?date=<?= $e($event['iso']) ?>" data-jump-date="<?= $e($event['iso']) ?>">
                                            <div class="schedule-event__body">
                                                <h3 class="schedule-event__title"><?= $e($event['title']) ?></h3>
                                                <p class="schedule-event__text"><?= $e($event['text']) ?></p>
                                            </div>
                                            <div class="schedule-event__when">
                                                <span class="schedule-event__in"><?= $e($event['in']) ?></span>
                                                <span class="schedule-event__date"><?= $e($event['date']) ?></span>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </section>

                        <aside class="schedule-rule" aria-labelledby="schedule-rule-title">
                            <span class="schedule-rule__icon" aria-hidden="true">
                                <span class="ms">warning</span>
                            </span>
                            <div class="schedule-rule__body">
                                <p class="schedule-rule__eyebrow">
                                    <?= $e($tip['eyebrow']) ?>
                                    <span class="ms schedule-rule__leaf" aria-hidden="true">eco</span>
                                </p>
                                <h2 class="schedule-rule__title" id="schedule-rule-title"><?= $e($tip['title']) ?></h2>
                                <p class="schedule-rule__text"><?= $e($tip['text']) ?></p>
                            </div>
                        </aside>

                        <details class="schedule-add" data-add>
                            <summary class="schedule-cta">
                                <span class="ms" aria-hidden="true">add_circle</span>
                                Zaplanuj zabieg / przypomnienie
                            </summary>
                            <form class="schedule-add__form" action="/schedule/tasks" method="POST" data-add-form>
                                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                <input type="hidden" name="date" value="<?= $e($selectedIso) ?>">
                                <input type="hidden" name="_back" value="<?= $e($self) ?>">
                                <p class="schedule-add__hint">Dodajesz do dnia: <strong><?= $e(PolishDate::full($selected)) ?></strong></p>
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
                                        <label class="field__label" for="add-time">Godzina</label>
                                        <input class="input" id="add-time" name="time" type="time">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="field__label" for="add-note">Notatka (opcjonalnie)</label>
                                    <textarea class="textarea" id="add-note" name="note" rows="2" maxlength="240" placeholder="Co przygotować, na co uważać…"></textarea>
                                </div>
                                <button type="submit" class="btn btn--primary btn--block">
                                    <span class="ms" aria-hidden="true">event_available</span>
                                    Dodaj do harmonogramu
                                </button>
                            </form>
                        </details>
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
