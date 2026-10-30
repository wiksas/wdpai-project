<?php
$weekDays = [
    ['label' => 'PN', 'name' => 'Poniedziałek', 'date' => '28', 'dots' => ['dark'], 'active' => false],
    ['label' => 'WT', 'name' => 'Wtorek', 'date' => '29', 'dots' => ['terra'], 'active' => false],
    ['label' => 'ŚR', 'name' => 'Środa', 'date' => '30', 'dots' => ['green'], 'active' => false],
    ['label' => 'CZ', 'name' => 'Czwartek', 'date' => '1', 'dots' => ['dark', 'terra'], 'active' => false],
    ['label' => 'PT', 'name' => 'Piątek', 'date' => '2', 'dots' => ['dark'], 'active' => false],
    ['label' => 'SO', 'name' => 'Sobota', 'date' => '3', 'dots' => ['green'], 'active' => false],
    ['label' => 'ND', 'name' => 'Niedziela', 'date' => '4', 'dots' => ['green', 'terra', 'light'], 'active' => true],
];

$legend = [
    ['tone' => 'dark', 'label' => 'Nawadnianie'],
    ['tone' => 'terra', 'label' => 'Cięcie/Formowanie'],
    ['tone' => 'green', 'label' => 'Zraszanie'],
];

$upcoming = [
    ['tone' => 'terra', 'title' => 'Defoliacja selektywna Wiązu', 'text' => 'Ulmus parvifolia • Redukcja wielkości liści', 'in' => 'za 8 dni', 'date' => '12 maja'],
    ['tone' => 'dark', 'title' => 'Aplikacja nawozu Biogold', 'text' => 'Wszystkie dojrzałe okazy liściaste i iglaste', 'in' => 'za 11 dni', 'date' => '15 maja'],
    ['tone' => 'green', 'title' => 'Prześwietlanie pąków wewnętrznych', 'text' => 'Poprawa dopływu światła do dolnych partii', 'in' => 'za 14 dni', 'date' => '18 maja'],
];

$e = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
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
                                    7. SEKKI (<span lang="ja">立夏</span>)
                                </span>
                                <h2 class="schedule-season__title" id="schedule-season-title">Rikka • <span lang="ja">立夏</span></h2>
                                <p class="schedule-season__text">Początek Lata • Pierwsze żary i żywy<br>wzrost pąków</p>
                            </div>
                            <div class="schedule-season__date">
                                <span class="schedule-season__month">Maj 2025</span>
                                <span class="schedule-season__ko">Kō 19: Żaby rechoczą</span>
                            </div>
                        </section>

                        <section class="schedule-week" aria-labelledby="schedule-week-title">
                            <header class="schedule-week__head">
                                <h2 class="schedule-week__title" id="schedule-week-title">
                                    <span class="ms" aria-hidden="true">calendar_month</span>
                                    Tydzień 18
                                </h2>
                                <div class="schedule-week__nav">
                                    <button type="button" class="schedule-week__arrow" aria-label="Poprzedni tydzień">
                                        <span class="ms" aria-hidden="true">chevron_left</span>
                                    </button>
                                    <button type="button" class="schedule-week__arrow" aria-label="Następny tydzień">
                                        <span class="ms" aria-hidden="true">chevron_right</span>
                                    </button>
                                </div>
                            </header>

                            <ol class="schedule-days">
                                <?php foreach ($weekDays as $day): ?>
                                    <li class="schedule-day<?= $day['active'] ? ' is-active' : '' ?>"<?= $day['active'] ? ' aria-current="date"' : '' ?>>
                                        <abbr class="schedule-day__label" title="<?= $e($day['name']) ?>"><?= $e($day['label']) ?></abbr>
                                        <span class="schedule-day__date"><?= $e($day['date']) ?></span>
                                        <span class="schedule-day__dots" aria-hidden="true">
                                            <?php foreach ($day['dots'] as $tone): ?>
                                                <span class="schedule-dot schedule-dot--<?= $e($tone) ?>"></span>
                                            <?php endforeach; ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ol>

                            <ul class="schedule-legend" aria-label="Legenda zabiegów">
                                <?php foreach ($legend as $item): ?>
                                    <li class="schedule-legend__item">
                                        <span class="schedule-dot schedule-dot--<?= $e($item['tone']) ?>" aria-hidden="true"></span>
                                        <?= $e($item['label']) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </section>

                        <section class="schedule-today" aria-labelledby="schedule-today-title">
                            <header class="schedule-section-head">
                                <div>
                                    <h2 class="schedule-section-head__title" id="schedule-today-title">Zadania na Dziś</h2>
                                    <p class="schedule-today__date">Niedziela, 4 maja • 3 zaplanowane zabiegi</p>
                                </div>
                                <span class="schedule-pill schedule-pill--urgent">1 Pilne</span>
                            </header>

                            <ul class="schedule-tasks">
                                <li class="schedule-task">
                                    <input class="schedule-task__check" type="checkbox" id="schedule-task-1" aria-label="Oznacz jako wykonane: Podlanie zanurzeniowe Klonu Momiji">
                                    <div class="schedule-task__body">
                                        <div class="schedule-task__top">
                                            <span class="schedule-pill schedule-pill--urgent">Pilne</span>
                                            <span class="schedule-task__time">Przed 11:30</span>
                                        </div>
                                        <h3 class="schedule-task__title">
                                            <a href="/collection/2">Podlanie zanurzeniowe Klonu Momiji</a>
                                        </h3>
                                        <p class="schedule-task__text">Podłoże akadama wymaga głębokiego nasycenia przed ostrym południowym słońcem.</p>
                                        <div class="schedule-task__meta">
                                            <span class="schedule-task__specimen">
                                                <span class="ms schedule-task__icon" aria-hidden="true">water_drop</span>
                                                Acer palmatum #04 • 18 lat
                                            </span>
                                            <span class="schedule-task__note schedule-task__note--terra">Zanurzenie 4 min</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="schedule-task">
                                    <input class="schedule-task__check" type="checkbox" id="schedule-task-2" aria-label="Oznacz jako wykonane: Kontrola wcinania drutu u Jałowca Shimpaku">
                                    <div class="schedule-task__body">
                                        <div class="schedule-task__top">
                                            <span class="schedule-pill schedule-pill--routine">Rutynowe</span>
                                            <span class="schedule-task__time">Popołudnie</span>
                                        </div>
                                        <h3 class="schedule-task__title">
                                            <a href="/collection/3">Kontrola wcinania drutu u Jałowca Shimpaku</a>
                                        </h3>
                                        <p class="schedule-task__text">Gałąź boczna poddana silnym naprężeniom w lutym; sprawdzić przyrost kalusa.</p>
                                        <div class="schedule-task__meta">
                                            <span class="schedule-task__specimen">
                                                <span class="ms schedule-task__icon schedule-task__icon--terra" aria-hidden="true">cable</span>
                                                Juniperus chinensis • Drut Al 2.5mm
                                            </span>
                                            <span class="schedule-task__note">Inspekcja lupy 10x</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </section>
                    </div>

                    <div class="schedule-side">
                        <section class="schedule-window" aria-labelledby="schedule-window-title">
                            <div class="schedule-window__head">
                                <div>
                                    <span class="schedule-window__eyebrow">
                                        <span class="ms ms--fill" aria-hidden="true">hourglass_bottom</span>
                                        OKNO SEZONOWE • SOSNA
                                    </span>
                                    <h2 class="schedule-window__title" id="schedule-window-title">Optymalne okno na Mekiri (sosna)</h2>
                                </div>
                                <p class="schedule-window__counter">
                                    <span class="schedule-window__count">6</span>
                                    <span class="schedule-window__unit">dni</span>
                                </p>
                            </div>
                            <p class="schedule-window__text">Pozostało 6 dni do zamknięcia optymalnego bilansu soków dla tegorocznych świec.</p>
                            <div class="schedule-window__foot">
                                <span class="schedule-window__species">Pinus thunbergii (Sosna Czarna)</span>
                                <a class="schedule-window__link" href="/studio">Wytyczne techniki →</a>
                            </div>
                        </section>

                        <section class="schedule-upcoming" aria-labelledby="schedule-upcoming-title">
                            <header class="schedule-section-head schedule-section-head--center">
                                <h2 class="schedule-section-head__title" id="schedule-upcoming-title">Nadchodzące 14 dni</h2>
                                <span class="schedule-upcoming__sekki">Sekki: Rikka / Shōman</span>
                            </header>

                            <ol class="schedule-timeline">
                                <?php foreach ($upcoming as $event): ?>
                                    <li class="schedule-event">
                                        <span class="schedule-event__dot schedule-dot--<?= $e($event['tone']) ?>" aria-hidden="true"></span>
                                        <article class="schedule-event__card">
                                            <div class="schedule-event__body">
                                                <h3 class="schedule-event__title"><?= $e($event['title']) ?></h3>
                                                <p class="schedule-event__text"><?= $e($event['text']) ?></p>
                                            </div>
                                            <div class="schedule-event__when">
                                                <span class="schedule-event__in"><?= $e($event['in']) ?></span>
                                                <span class="schedule-event__date"><?= $e($event['date']) ?></span>
                                            </div>
                                        </article>
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
                                    ZASADA MAJOWA • MISTRZOWSKA UWAGA
                                    <span class="ms schedule-rule__leaf" aria-hidden="true">eco</span>
                                </p>
                                <h2 class="schedule-rule__title" id="schedule-rule-title">Czego unikać w maju?</h2>
                                <p class="schedule-rule__text">Nigdy nie przycinaj pędów klonów w pełnym słońcu — ryzyko wycieku soków i zaschnięcia kambium. Przeprowadzaj cięcia wyłącznie wczesnym rankiem lub po zmierzchu przy osłonie przed promieniami UV.</p>
                            </div>
                        </aside>

                        <button type="button" class="schedule-cta">
                            <span class="ms" aria-hidden="true">add_circle</span>
                            + Zaplanuj zabieg / przypomnienie
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/partials/app-bottom-nav.html.php'; ?>
</body>
</html>
