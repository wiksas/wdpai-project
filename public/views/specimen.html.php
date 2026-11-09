<?php
// Układ: layouts/app.html.php
$layout = 'app';
$mainClass = 'passport';
$pageStyles = ['passport'];
$pageScripts = ['collection', 'specimen'];
$topbarBack = ['href' => '/collection', 'title' => 'Karta okazu'];

$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$historyCount = count($tree['history']);
$photoCount = count($photos);
$treeUrl = '/collection/' . (int) $tree['id'];
$isWarning = in_array($tree['status'], ['attention', 'critical'], true);
$nextStepParts = explode(':', (string) $tree['recommendation']['nextStep'], 2);

// Japońskie nazwy stylów, glin i podłoży z danych okazu: motyw tożsamości `.term` (romaji + kanji).
$termKanji = [
    'Moyogi' => '模様木',
    'Chokkan' => '直幹',
    'Kengai' => '懸崖',
    'Hokidachi' => '箒立ち',
    'Shakan' => '斜幹',
    'Fukinagashi' => '吹流し',
    'Akadama' => '赤玉',
    'Kiryu' => '桐生',
    'Tokoname' => '常滑',
    'Shudei' => '朱泥',
];
$term = static function (string $value) use ($e, $termKanji): string {
    if (!isset($termKanji[$value])) {
        return $e($value);
    }

    return '<span class="term">' . $e($value) . ' <span lang="ja">' . $e($termKanji[$value]) . '</span></span>';
};
?>
<nav class="passport-toolbar" aria-label="Nawigacja karty okazu">
    <a href="/collection" class="passport-toolbar__back">
        <span class="ms" aria-hidden="true">arrow_back</span>
        Wróć do kolekcji
    </a>
    <div class="passport-toolbar__actions">
        <button type="button" class="icon-button" aria-label="Drukuj kartę okazu" data-print>
            <span class="ms" aria-hidden="true">print</span>
        </button>
        <button type="button" class="icon-button" aria-label="Udostępnij link do karty okazu" data-share data-share-title="<?= $e($tree['name'] . ': karta okazu KOMOREBI') ?>">
            <span class="ms" aria-hidden="true">share</span>
        </button>
    </div>
</nav>

<div class="passport-layout">
    <div class="passport-main">
        <section class="passport-hero" aria-labelledby="passport-name">
            <img class="passport-hero__image" src="<?= $e($tree['image']) ?>" alt="<?= $e($tree['name'] . ', ' . $tree['commonName']) ?>" width="800" height="600" fetchpriority="high">
            <div class="passport-hero__body">
                <p class="passport-hero__meta">
                    Okaz nr <?= $e(sprintf('%03d', $tree['id'])) ?> · w kolekcji od <?= $e($tree['inCollectionSince']) ?> · wiek ok. <?= $e($tree['ageLabel']) ?>
                </p>
                <h1 class="passport-hero__name" id="passport-name"><?= $e($tree['name']) ?> <span lang="ja"><?= $e($tree['kanji']) ?></span></h1>
                <p class="passport-hero__species"><i><?= $e($tree['species']) ?></i>, <?= $e($tree['commonName']) ?></p>
            </div>
        </section>

        <dl class="passport-specs">
            <div class="passport-spec">
                <dt class="passport-spec__label">Styl korony</dt>
                <dd class="passport-spec__value"><?= $term((string) $tree['style']) ?></dd>
                <dd class="passport-spec__desc"><?= $e($tree['styleDescription']) ?></dd>
            </div>
            <div class="passport-spec">
                <dt class="passport-spec__label">Podłoże</dt>
                <dd class="passport-spec__value"><?= $term((string) $tree['substrate']) ?></dd>
                <dd class="passport-spec__desc"><?= $e($tree['substrateMix']) ?></dd>
            </div>
            <div class="passport-spec">
                <dt class="passport-spec__label">Doniczka</dt>
                <dd class="passport-spec__value"><?= $term((string) $tree['pot']) ?></dd>
                <dd class="passport-spec__desc"><?= $e($tree['potDescription']) ?></dd>
            </div>
        </dl>

        <section class="passport-status" aria-labelledby="passport-status-title">
            <header class="passport-status__header">
                <h2 class="passport-status__title" id="passport-status-title">Stan na dziś</h2>
                <p class="passport-status__verdict<?= $isWarning ? ' passport-status__verdict--warning' : '' ?>"><?= $e($tree['statusLabel']) ?></p>
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
                <p class="eyebrow">Faza wzrostu (opis dla miesiąca: <?= $e(mb_strtolower($tree['recommendation']['month'])) ?>)</p>
                <p class="passport-phase__title"><?= $e($tree['phenology']['phase']) ?></p>
                <p class="passport-phase__text"><?= $e($tree['phenology']['description']) ?></p>
            </div>

            <div class="passport-metrics">
                <div class="passport-metric passport-metric--water">
                    <p class="passport-metric__label">Wilgotność bryły</p>
                    <p class="passport-metric__value">
                        <span class="passport-metric__number"><?= $e($tree['moisture']) ?>%</span>
                        <span class="passport-metric__unit"><?= $e($tree['moistureLabel']) ?></span>
                    </p>
                    <div class="passport-meter<?= $tree['moisture'] < 30 ? ' passport-meter--low' : '' ?>" role="progressbar" aria-label="Wilgotność bryły" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $e($tree['moisture']) ?>">
                        <span class="passport-meter__bar" style="--value: <?= $e($tree['moisture']) ?>%"></span>
                    </div>
                    <p class="passport-metric__text">Ostatnie podlanie: <?= $e($tree['lastWatering']) ?></p>
                    <?php if (isset($tree['sensor'])): ?>
                        <p class="passport-metric__text">Wilgotność z czujnika, odczyt <a href="#passport-sensor">z <?= $e(\App\Support\PolishDate::now()->setTimestamp($tree['sensor']['at'])->format('H:i')) ?></a>.</p>
                    <?php endif; ?>
                    <form class="passport-water" action="<?= $treeUrl ?>/care" method="POST" data-care-form>
                        <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                        <input type="hidden" name="type" value="water">
                        <input type="hidden" name="_back" value="<?= $treeUrl ?>">
                        <button type="submit" class="btn passport-water__button">
                            <span class="ms" aria-hidden="true">water_drop</span>
                            Zarejestruj podlanie
                        </button>
                    </form>
                </div>
                <div class="passport-metric<?= $isWarning ? ' passport-metric--warning' : '' ?>">
                    <p class="passport-metric__label"><?= $e($tree['vitality']['label']) ?></p>
                    <p class="passport-metric__state"><?= $e($tree['vitality']['value']) ?></p>
                    <p class="passport-metric__text"><?= $e($tree['vitality']['description']) ?></p>
                </div>
            </div>

            <?php if ($tree['care'] !== null): ?>
                <div class="passport-care">
                    <p class="passport-care__title"><?= $e($tree['care']['title']) ?></p>
                    <p class="passport-care__meta"><?= $e($tree['care']['meta']) ?></p>
                    <p class="passport-care__text"><?= $e($tree['care']['text']) ?></p>
                </div>
            <?php endif; ?>

            <p class="passport-consult">
                Widzisz objawy, których tu nie ma?
                <a href="/clinic" class="passport-consult__link">Zapytaj konsultanta</a>
            </p>
        </section>
    </div>

    <div class="passport-side">
        <section class="passport-advice" aria-labelledby="passport-advice-title">
            <p class="eyebrow"><?= $e($tree['recommendation']['month']) ?></p>
            <h2 class="passport-advice__title" id="passport-advice-title">Zalecenia sezonowe</h2>
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
        </section>

        <?php include __DIR__ . '/partials/passport-schedule.html.php'; ?>

        <section class="passport-history" aria-labelledby="passport-history-title">
            <header class="passport-section-header">
                <h2 class="passport-section-header__title" id="passport-history-title">Historia zabiegów</h2>
                <span class="passport-section-header__count"><?= $historyCount ?> <?= $e(\App\Support\PolishDate::plural($historyCount, 'wpis', 'wpisy', 'wpisów')) ?></span>
            </header>

            <?php if ($historyCount === 0): ?>
                <p class="passport-empty">Ten okaz nie ma jeszcze zapisanych zabiegów. Podlanie zarejestrujesz w sekcji „Stan na dziś”, a zabiegi formowania zaplanujesz w <a href="/schedule">harmonogramie</a>.</p>
            <?php else: ?>
                <ol class="passport-timeline">
                    <?php foreach ($tree['history'] as $index => $entry): ?>
                        <?php $imageCount = count($entry['images']); ?>
                        <li class="passport-entry<?= $index === 0 ? ' passport-entry--latest' : '' ?>">
                            <article class="passport-entry__card">
                                <header class="passport-entry__meta">
                                    <span class="passport-entry__date"><?= $e($entry['date']) ?></span>
                                    <span class="passport-entry__type"><?= $e($entry['type']) ?></span>
                                </header>
                                <h3 class="passport-entry__title"><?= $e($entry['title']) ?></h3>
                                <p class="passport-entry__text"><?= $e($entry['description']) ?></p>

                                <?php if ($imageCount > 0): ?>
                                    <div class="passport-gallery<?= $imageCount === 1 ? ' passport-gallery--single' : '' ?>">
                                        <?php foreach ($entry['images'] as $image): ?>
                                            <figure class="passport-shot">
                                                <div class="passport-shot__frame">
                                                    <img src="<?= $e($image['src']) ?>" alt="<?= $e($entry['title'] . ': ' . $image['label']) ?>" width="400" height="300" loading="lazy">
                                                    <span class="passport-shot__label"><?= $e($image['label']) ?></span>
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
            <?php endif; ?>
        </section>

        <?php include __DIR__ . '/partials/passport-scan.html.php'; ?>

        <?php include __DIR__ . '/partials/passport-sensor.html.php'; ?>

        <section class="passport-photos" aria-labelledby="passport-photos-title" data-photos>
            <header class="passport-section-header">
                <h2 class="passport-section-header__title" id="passport-photos-title">Twoje zdjęcia</h2>
                <?php if ($photoCount > 0): ?>
                    <span class="passport-section-header__count"><?= $photoCount ?> <?= $e(\App\Support\PolishDate::plural($photoCount, 'zdjęcie', 'zdjęcia', 'zdjęć')) ?></span>
                <?php endif; ?>
            </header>

            <?php if ($photos === []): ?>
                <p class="passport-empty">Nie dodałeś jeszcze zdjęć tego okazu. Zdjęcie z każdego sezonu pozwoli porównać, jak korona zmienia się po zabiegach.</p>
            <?php else: ?>
                <ul class="passport-photos__grid">
                    <?php foreach ($photos as $photo): ?>
                        <li class="passport-photos__item">
                            <a href="/media/<?= $e($photo['file']) ?>" target="_blank" rel="noopener" class="passport-photos__link">
                                <img src="/media/<?= $e($photo['file']) ?>" alt="<?= $e($photo['caption'] !== '' ? $photo['caption'] : 'Zdjęcie okazu ' . $tree['name']) ?>" width="240" height="240" loading="lazy">
                                <?php $photoMoment = \App\Support\PolishDate::now()->setTimestamp((int) $photo['at']); ?>
                                <span class="passport-photos__date">
                                    <time datetime="<?= $e($photoMoment->format('c')) ?>"><?= $e(\App\Support\PolishDate::dayMonthYear($photoMoment)) ?></time>
                                    <span><?= $e($photoMoment->format('H:i')) ?></span>
                                </span>
                            </a>
                            <form action="<?= $treeUrl ?>/scan" method="POST" class="passport-photos__scan" data-scan-form>
                                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                <input type="hidden" name="photo_id" value="<?= $e($photo['id']) ?>">
                                <button type="submit" aria-label="Skanuj to zdjęcie" title="Skanuj to zdjęcie"><span class="ms" aria-hidden="true">center_focus_strong</span></button>
                            </form>
                            <form action="<?= $treeUrl ?>/photos/delete" method="POST" class="passport-photos__remove" data-photo-delete>
                                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                <input type="hidden" name="photo" value="<?= $e($photo['id']) ?>">
                                <input type="hidden" name="_back" value="<?= $treeUrl ?>">
                                <button type="submit" aria-label="Usuń zdjęcie"><span class="ms" aria-hidden="true">delete</span></button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form class="passport-upload" action="<?= $treeUrl ?>/photos" method="POST" enctype="multipart/form-data" data-photo-form>
                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                <input type="hidden" name="_back" value="<?= $treeUrl ?>">
                <label class="btn passport-upload__label" for="passport-photo">
                    <span class="ms" aria-hidden="true">add_a_photo</span>
                    Dodaj zdjęcie
                </label>
                <input class="visually-hidden" id="passport-photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp" aria-describedby="passport-photo-hint" data-photo-input required>
                <button type="submit" class="btn passport-upload__send">Wyślij zdjęcie</button>
                <p class="passport-upload__hint" id="passport-photo-hint">JPG, PNG lub WebP, do 2 MB.</p>
            </form>
        </section>

        <section class="passport-remove" aria-labelledby="passport-remove-title">
            <h2 class="passport-remove__title" id="passport-remove-title">Usuń okaz z kolekcji</h2>
            <details class="passport-remove__box">
                <summary class="btn btn--secondary passport-remove__toggle">
                    <span class="ms" aria-hidden="true">delete</span>
                    Usuń z kolekcji
                </summary>
                <form action="<?= $treeUrl ?>/delete" method="POST" class="passport-remove__form">
                    <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                    <p class="passport-remove__text">
                        „<?= $e($tree['name']) ?>” zniknie z Twojej kolekcji razem z dodanymi zdjęciami i zapisanymi zabiegami.
                        <?= (int) $tree['id'] < \App\Service\CollectionService::FIRST_CUSTOM_ID ? 'To okaz przykładowy: dla innych kont zostaje.' : '' ?>
                        Tej operacji nie można cofnąć.
                    </p>
                    <button type="submit" class="btn btn--tertiary">Tak, usuń okaz</button>
                </form>
            </details>
        </section>
    </div>
</div>
