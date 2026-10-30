<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$historyCount = count($tree['history']);
$historyMod100 = $historyCount % 100;
$historyMod10 = $historyCount % 10;
if ($historyCount === 1) {
    $historyLabel = 'wpis historyczny';
} elseif ($historyMod10 >= 2 && $historyMod10 <= 4 && ($historyMod100 < 12 || $historyMod100 > 14)) {
    $historyLabel = 'wpisy historyczne';
} else {
    $historyLabel = 'wpisów historycznych';
}

$isWarning = in_array($tree['status'], ['attention', 'critical'], true);
$moistureTone = $tree['moisture'] < 30 ? 'terra' : 'dark';
$dotTones = ['terra', 'dark', 'light'];
$nextStepParts = explode(':', (string) $tree['recommendation']['nextStep'], 2);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <?php include __DIR__ . '/partials/head.html.php'; ?>
    <link rel="stylesheet" href="<?= $asset('/css/pages/passport.css') ?>">
</head>
<body>
    <div class="app">
        <?php include __DIR__ . '/partials/app-sidebar.html.php'; ?>
        <div class="app-main">
            <?php $topbarBack = ['href' => '/collection', 'title' => 'Karta Okazu Bonsai']; ?>
            <?php include __DIR__ . '/partials/app-topbar.html.php'; ?>
            <main class="app-content passport">
                <nav class="passport-toolbar" aria-label="Nawigacja karty okazu">
                    <a href="/collection" class="passport-toolbar__back">
                        <span class="ms" aria-hidden="true">arrow_back</span>
                        Wróć do ogrodu
                    </a>
                    <div class="passport-toolbar__actions">
                        <button type="button" class="icon-button" aria-label="Edytuj kartę okazu">
                            <span class="ms" aria-hidden="true">edit</span>
                        </button>
                        <button type="button" class="icon-button" aria-label="Udostępnij kartę okazu">
                            <span class="ms" aria-hidden="true">share</span>
                        </button>
                    </div>
                </nav>

                <div class="passport-layout">
                    <div class="passport-main">
                        <section class="passport-hero" aria-labelledby="passport-name">
                            <img class="passport-hero__image" src="<?= $e($tree['image']) ?>" alt="<?= $e($tree['name'] . ' — ' . $tree['species']) ?>">
                            <div class="passport-hero__body">
                                <div class="passport-hero__badges">
                                    <span class="passport-hero__id">Specimen ID #<?= $e(sprintf('%03d', $tree['id'])) ?></span>
                                    <span class="passport-hero__since">W kolekcji od <?= $e($tree['inCollectionSince']) ?></span>
                                </div>
                                <div class="passport-hero__headline">
                                    <h1 class="passport-hero__name" id="passport-name"><?= $e($tree['name']) ?> <span lang="ja">(<?= $e($tree['kanji']) ?>)</span></h1>
                                    <span class="passport-hero__age">wiek ~<?= $e($tree['ageLabel']) ?></span>
                                </div>
                                <p class="passport-hero__species"><?= $e($tree['species']) ?> • <?= $e($tree['commonName']) ?></p>
                            </div>
                        </section>

                        <dl class="passport-specs">
                            <div class="passport-spec">
                                <dt class="passport-spec__label">Styl korony</dt>
                                <dd class="passport-spec__value"><?= $e($tree['style']) ?></dd>
                                <dd class="passport-spec__desc"><?= $e($tree['styleDescription']) ?></dd>
                            </div>
                            <div class="passport-spec">
                                <dt class="passport-spec__label">Podłoże</dt>
                                <dd class="passport-spec__value"><?= $e($tree['substrate']) ?></dd>
                                <dd class="passport-spec__desc"><?= $e($tree['substrateMix']) ?></dd>
                            </div>
                            <div class="passport-spec">
                                <dt class="passport-spec__label">Doniczka</dt>
                                <dd class="passport-spec__value"><?= $e($tree['pot']) ?></dd>
                                <dd class="passport-spec__desc"><?= $e($tree['potDescription']) ?></dd>
                            </div>
                        </dl>

                        <section class="passport-status" aria-labelledby="passport-status-title">
                            <header class="passport-status__header">
                                <h2 class="passport-status__title" id="passport-status-title">
                                    <span class="passport-status__dot<?= $isWarning ? ' passport-status__dot--terra' : '' ?>" aria-hidden="true"></span>
                                    Status Biologiczny na Dziś
                                </h2>
                                <span class="passport-status__zone">Strefa 7b • Engawa</span>
                            </header>

                            <?php if ($tree['alert'] !== null): ?>
                                <div class="passport-alert" role="alert">
                                    <p class="passport-alert__title">
                                        <span class="ms" aria-hidden="true">warning</span>
                                        <?= $e($tree['alert']['title']) ?>
                                    </p>
                                    <p class="passport-alert__text"><?= $e($tree['alert']['text']) ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="passport-phase">
                                <span class="passport-phase__icon" aria-hidden="true"><span class="ms">spa</span></span>
                                <div>
                                    <p class="passport-phase__label">Cykl fenologiczny</p>
                                    <p class="passport-phase__title"><?= $e($tree['phenology']['phase']) ?></p>
                                    <p class="passport-phase__text"><?= $e($tree['phenology']['description']) ?></p>
                                </div>
                            </div>

                            <div class="passport-metrics">
                                <div class="passport-metric">
                                    <p class="passport-metric__label">
                                        Wilgotność bryły
                                        <span class="ms" aria-hidden="true">water_drop</span>
                                    </p>
                                    <p class="passport-metric__value">
                                        <span class="passport-metric__number"><?= $e($tree['moisture']) ?>%</span>
                                        <span class="passport-metric__unit"><?= $e($tree['moistureLabel']) ?></span>
                                    </p>
                                    <div class="passport-meter passport-meter--<?= $moistureTone ?>" role="progressbar" aria-label="Wilgotność bryły" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $e($tree['moisture']) ?>">
                                        <span class="passport-meter__bar" style="--value: <?= $e($tree['moisture']) ?>%"></span>
                                    </div>
                                    <p class="passport-metric__text">Nawodniono <?= $e($tree['lastWatering']) ?></p>
                                </div>
                                <div class="passport-metric<?= $isWarning ? ' passport-metric--warning' : '' ?>">
                                    <p class="passport-metric__label">
                                        <?= $e($tree['vitality']['label']) ?>
                                        <span class="ms ms--fill" aria-hidden="true">eco</span>
                                    </p>
                                    <p class="passport-metric__state"><?= $e($tree['vitality']['value']) ?></p>
                                    <p class="passport-metric__text"><?= $e($tree['vitality']['description']) ?></p>
                                </div>
                            </div>

                            <?php if ($tree['care'] !== null): ?>
                                <div class="passport-care">
                                    <p class="passport-care__title">
                                        <span class="ms" aria-hidden="true">architecture</span>
                                        <?= $e($tree['care']['title']) ?>
                                        <span class="passport-care__meta"><?= $e($tree['care']['meta']) ?></span>
                                    </p>
                                    <p class="passport-care__text"><?= $e($tree['care']['text']) ?></p>
                                </div>
                            <?php endif; ?>
                        </section>
                    </div>

                    <div class="passport-side">
                        <section class="passport-advice" aria-labelledby="passport-advice-title">
                            <span class="passport-advice__icon" aria-hidden="true"><span class="ms">psychology_alt</span></span>
                            <div class="passport-advice__body">
                                <header class="passport-advice__header">
                                    <h2 class="passport-advice__title" id="passport-advice-title">Zalecenia kuratora na ten miesiąc</h2>
                                    <span class="passport-advice__month"><?= $e($tree['recommendation']['month']) ?></span>
                                </header>
                                <p class="passport-advice__text"><?= $e($tree['recommendation']['text']) ?></p>
                                <p class="passport-advice__next">
                                    <span class="ms" aria-hidden="true">event_upcoming</span>
                                    <span>
                                        <?php if (count($nextStepParts) === 2): ?>
                                            <?= $e($nextStepParts[0]) ?>: <strong><?= $e(trim($nextStepParts[1])) ?></strong>
                                        <?php else: ?>
                                            <?= $e($nextStepParts[0]) ?>
                                        <?php endif; ?>
                                    </span>
                                </p>
                            </div>
                        </section>

                        <section class="passport-history" aria-labelledby="passport-history-title">
                            <header class="passport-history__header">
                                <h2 class="passport-history__title" id="passport-history-title">Oś Ewolucji i Zabiegów</h2>
                                <span class="passport-history__count"><?= $historyCount ?> <?= $historyLabel ?></span>
                            </header>

                            <ol class="passport-timeline">
                                <?php foreach ($tree['history'] as $index => $entry): ?>
                                    <?php $imageCount = count($entry['images']); ?>
                                    <li class="passport-entry passport-entry--<?= $dotTones[$index % count($dotTones)] ?>">
                                        <article class="passport-entry__card">
                                            <header class="passport-entry__meta">
                                                <span class="passport-entry__date<?= $index === 0 ? ' passport-entry__date--latest' : '' ?>"><?= $e($entry['date']) ?></span>
                                                <span class="passport-entry__type"><?= $e($entry['type']) ?></span>
                                            </header>
                                            <h3 class="passport-entry__title"><?= $e($entry['title']) ?></h3>
                                            <p class="passport-entry__text"><?= $e($entry['description']) ?></p>

                                            <?php if ($imageCount > 0): ?>
                                                <div class="passport-gallery<?= $imageCount === 1 ? ' passport-gallery--single' : '' ?>">
                                                    <?php foreach ($entry['images'] as $imageIndex => $image): ?>
                                                        <figure class="passport-shot">
                                                            <div class="passport-shot__frame">
                                                                <img src="<?= $e($image['src']) ?>" alt="<?= $e($entry['title'] . ' — ' . $image['label']) ?>" loading="lazy">
                                                                <span class="passport-shot__label<?= $imageIndex % 2 === 1 ? ' passport-shot__label--terra' : '' ?>"><?= $e($image['label']) ?></span>
                                                            </div>
                                                            <?php if ($image['caption'] !== null): ?>
                                                                <figcaption class="passport-shot__caption"><?= $e($image['caption']) ?></figcaption>
                                                            <?php endif; ?>
                                                        </figure>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </article>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </section>

                        <div class="passport-actions">
                            <button type="button" class="passport-action passport-action--water">
                                <span class="ms" aria-hidden="true">water_drop</span>
                                Zarejestruj Podlanie
                            </button>
                            <button type="button" class="passport-action passport-action--photo">
                                <span class="ms" aria-hidden="true">add_a_photo</span>
                                Dodaj Zdjęcie
                            </button>
                            <a href="/clinic" class="passport-action passport-action--ai">
                                <span class="ms" aria-hidden="true">psychology</span>
                                Skonsultuj z AI Bonsai Master
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/partials/app-bottom-nav.html.php'; ?>
</body>
</html>
