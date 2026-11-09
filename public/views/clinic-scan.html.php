<!DOCTYPE html>
<html lang="pl" data-theme="<?= htmlspecialchars((string) ($theme ?? 'system'), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/partials/head.html.php'; ?>
    <link rel="stylesheet" href="<?= $asset('/css/pages/clinic.css') ?>">
</head>
<body>
<?php
use App\Service\PlantVision;
use App\Support\PolishDate;

$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$levels = ['good' => 'dobra kondycja', 'watch' => 'do obserwacji', 'warning' => 'ostrzeżenie', 'critical' => 'stan krytyczny'];
$parts = ['leaves' => 'Liście i igły', 'bark' => 'Kora i pień', 'roots' => 'Podłoże i korzenie', 'wiring' => 'Drutowanie'];
$progressIcons = ['improved' => 'trending_up', 'worse' => 'trending_down', 'unchanged' => 'trending_flat'];
$date = static fn (int $timestamp): string => PolishDate::dayMonthYear(PolishDate::now()->setTimestamp($timestamp));

$d = is_array($scan['diagnosis'] ?? null) ? $scan['diagnosis'] : null;
$visible = $d !== null && ($d['plantVisible'] ?? false) === true;
$level = (string) ($scan['level'] ?? 'watch');
$photoUrl = '/media/' . $scan['file'];
$scanUrl = '/clinic/scans/' . $scan['id'];
$note = (string) ($scan['note'] ?? '');
$treeName = $tree !== null ? (string) $tree['name'] : '';
?>
    <div class="app">
        <?php include __DIR__ . '/partials/app-sidebar.html.php'; ?>
        <div class="app-main">
            <?php include __DIR__ . '/partials/app-topbar.html.php'; ?>
            <main class="app-content clinic clinic-scan-page">

                <a class="clinic-scan__back" href="/clinic#clinic-history-title">
                    <span class="ms" aria-hidden="true">arrow_back</span>
                    Wróć do Kliniki
                </a>

                <header class="clinic-head" data-scan-head>
                    <p class="eyebrow">Skan zdjęcia</p>
                    <h1 class="clinic-head__title"><?= $e($scan['title']) ?></h1>
                    <p class="clinic-head__lead">
                        <?= $e($parts[$scan['part'] ?? ''] ?? 'Zdjęcie') ?>, zapisany <?= $e($date((int) $scan['at'])) ?> (<?= $e(PolishDate::ago((int) $scan['at'])) ?>).
                        <?php if ($tree !== null): ?>
                            Okaz: <a href="/collection/<?= (int) $tree['id'] ?>"><?= $e($treeName) ?></a>.
                        <?php endif; ?>
                        <?php if (isset($scan['reassessedAt'])): ?>
                            Oceniony ponownie <?= $e(PolishDate::ago((int) $scan['reassessedAt'])) ?>.
                        <?php endif; ?>
                    </p>
                </header>

                <div class="clinic-scan">
                    <section class="clinic-card clinic-scan__photo" aria-label="Zdjęcie skanu">
                        <div class="clinic-zoom" data-zoom>
                            <a class="clinic-zoom__link" href="<?= $e($photoUrl) ?>" target="_blank" rel="noopener" data-zoom-link>
                                <img class="clinic-zoom__img" src="<?= $e($photoUrl) ?>" alt="Zdjęcie, które zostało ocenione" width="640" height="640" data-zoom-img>
                                <span class="visually-hidden">(otwiera zdjęcie w nowej karcie)</span>
                            </a>
                            <div class="clinic-zoom__tools" data-zoom-tools hidden>
                                <button type="button" class="clinic-zoom__btn" data-zoom-out aria-label="Pomniejsz"><span class="ms" aria-hidden="true">remove</span></button>
                                <span class="clinic-zoom__level" data-zoom-level aria-live="polite">100%</span>
                                <button type="button" class="clinic-zoom__btn" data-zoom-in aria-label="Powiększ"><span class="ms" aria-hidden="true">add</span></button>
                                <button type="button" class="clinic-zoom__btn" data-zoom-reset aria-label="Dopasuj do ramki"><span class="ms" aria-hidden="true">fit_screen</span></button>
                            </div>
                        </div>
                        <p class="clinic-zoom__hint" data-zoom-hint>Kliknij zdjęcie, żeby otworzyć je w nowej karcie.</p>
                    </section>

                    <section class="clinic-card clinic-scan__result" aria-labelledby="clinic-scan-cause" data-scan-result-box>
                        <?php if ($d === null): ?>
                            <div class="clinic-result" data-level="<?= $e($level) ?>">
                                <p class="clinic-result__confidence">Indeks barwy <?= (int) $scan['vitality'] ?>/100, <?= $e($levels[$level] ?? '') ?></p>
                                <h2 class="clinic-result__cause" id="clinic-scan-cause"><?= $e($scan['title']) ?></h2>
                                <p class="clinic-result__summary">Ten skan ma tylko indeks barwy z przeglądarki, bez oceny modelu. Użyj „Oceń ponownie”, żeby model obejrzał to samo zdjęcie.</p>
                            </div>
                        <?php elseif (!$visible): ?>
                            <div class="clinic-result" data-level="watch">
                                <h2 class="clinic-result__cause" id="clinic-scan-cause">Nie widać drzewka na zdjęciu</h2>
                                <p class="clinic-result__summary"><?= $e($d['summary'] ?? '') ?> Zrób zbliżenie liści lub igieł w świetle dziennym i zeskanuj ponownie.</p>
                            </div>
                        <?php else: ?>
                            <div class="clinic-result" data-level="<?= $e($d['condition'] ?? $level) ?>">
                                <p class="clinic-result__confidence">
                                    Pewność wyniku: <?= $e(PlantVision::CONFIDENCE[$d['confidence'] ?? 'low'] ?? 'niska') ?>, kondycja <?= (int) $d['vitality'] ?>/100 (<?= $e($levels[$d['condition'] ?? $level] ?? '') ?>)
                                </p>
                                <h2 class="clinic-result__cause" id="clinic-scan-cause"><?= $e(PlantVision::CAUSES[$d['cause']] ?? '') ?></h2>
                                <p class="clinic-result__summary"><?= $e($d['summary'] ?? '') ?></p>

                                <?php if (is_array($d['progress'] ?? null)): ?>
                                    <div class="clinic-scan__progress is-<?= $e($d['progress']['status']) ?>">
                                        <p class="clinic-scan__progress-title">
                                            <span class="ms" aria-hidden="true"><?= $e($progressIcons[$d['progress']['status']] ?? 'trending_flat') ?></span>
                                            Postępy: <?= $e($d['progress']['label']) ?>
                                        </p>
                                        <p class="clinic-scan__progress-text"><?= $e($d['progress']['text']) ?></p>
                                    </div>
                                <?php endif; ?>

                                <div class="clinic-result__cols">
                                    <div>
                                        <h3 class="clinic-result__heading">Co widać na zdjęciu</h3>
                                        <ul class="clinic-result__list">
                                            <?php foreach (($d['observations'] ?? []) as $observation): ?>
                                                <li><?= $e($observation) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <div>
                                        <h3 class="clinic-result__heading">Co zrobić</h3>
                                        <ol class="clinic-result__list clinic-result__list--steps">
                                            <?php foreach (($d['actions'] ?? []) as $action): ?>
                                                <li><?= $e($action) ?></li>
                                            <?php endforeach; ?>
                                        </ol>
                                    </div>
                                </div>

                                <?php if (($d['noteHints'] ?? []) !== []): ?>
                                    <div>
                                        <h3 class="clinic-result__heading">Co dodał Twój opis</h3>
                                        <ul class="clinic-result__list">
                                            <?php foreach ($d['noteHints'] as $hint): ?>
                                                <li><?= $e($hint) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <?php if (($d['verify'] ?? '') !== ''): ?>
                                    <p class="clinic-result__verify"><strong>Jak to sprawdzić:</strong> <?= $e($d['verify']) ?></p>
                                <?php endif; ?>

                                <?php if (($d['actions'] ?? []) !== []): ?>
                                    <?php if ($scan['scheduled'] ?? false): ?>
                                        <a href="/schedule" class="btn btn--tonal clinic-result__schedule is-done">
                                            <span class="ms" aria-hidden="true">check</span>
                                            Dodano do harmonogramu. Otwórz harmonogram
                                        </a>
                                    <?php else: ?>
                                        <form action="/clinic/protocol" method="post" class="clinic-scan__schedule">
                                            <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                            <input type="hidden" name="label" value="<?= $e($treeName !== '' ? $treeName : 'Skan') ?>">
                                            <input type="hidden" name="scan" value="<?= $e($scan['id']) ?>">
                                            <?php if ($tree !== null): ?>
                                                <input type="hidden" name="tree" value="<?= (int) $tree['id'] ?>">
                                            <?php endif; ?>
                                            <?php foreach ($d['actions'] as $action): ?>
                                                <input type="hidden" name="steps[]" value="<?= $e($action) ?>">
                                            <?php endforeach; ?>
                                            <button type="submit" class="btn btn--tonal clinic-result__schedule">
                                                <span class="ms" aria-hidden="true">event_available</span>
                                                Dodaj kroki do harmonogramu
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($note !== ''): ?>
                            <p class="clinic-scan__note"><strong>Twój opis:</strong> <?= $e($note) ?></p>
                        <?php endif; ?>
                    </section>

                    <section class="clinic-card clinic-scan__actions" aria-labelledby="clinic-scan-actions-title">
                        <h2 class="clinic-card__title" id="clinic-scan-actions-title">Oceń ponownie</h2>
                        <p class="clinic-card__note">Model obejrzy to samo zdjęcie jeszcze raz. Opisz, co ostatnio działo się z drzewkiem: podlewanie, miejsce, przesadzanie, nawożenie, szkodniki. Opis wpływa na wybór przyczyny i zalecenia, ale nie zmienia tego, co widać na zdjęciu.</p>

                        <form class="clinic-scan__form" action="<?= $e($scanUrl) ?>/reassess" method="post" data-reassess-form>
                            <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                            <div class="field">
                                <label class="field__label" for="scan-note">Co się ostatnio działo z drzewkiem? (opcjonalnie)</label>
                                <textarea class="textarea" id="scan-note" name="note" rows="4" maxlength="<?= (int) $maxNote ?>" placeholder="Np. od tygodnia nie podlewałem, stoi na południowym parapecie, miesiąc temu było przesadzane"><?= $e($note) ?></textarea>
                            </div>
                            <button type="submit" class="btn btn--primary" data-reassess-button>
                                <span class="ms" aria-hidden="true">refresh</span>
                                Oceń ponownie
                            </button>
                            <p class="clinic-scan__status" role="status" aria-live="polite" data-reassess-status hidden>Model ogląda zdjęcie, to potrwa kilka sekund…</p>
                        </form>

                        <details class="clinic-scan__delete">
                            <summary>Usuń skan</summary>
                            <p>Skan i jego zdjęcie znikną z historii. Tej zmiany nie da się cofnąć.</p>
                            <form action="<?= $e($scanUrl) ?>/delete" method="post" data-delete-form>
                                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                                <button type="submit" class="btn btn--tonal clinic-scan__delete-button">
                                    <span class="ms" aria-hidden="true">delete</span>
                                    Tak, usuń skan
                                </button>
                            </form>
                        </details>
                    </section>
                </div>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/partials/app-bottom-nav.html.php'; ?>
    <?php include __DIR__ . '/partials/foot.html.php'; ?>
    <script src="<?= $asset('/js/clinic-scan.js') ?>" defer></script>
</body>
</html>
