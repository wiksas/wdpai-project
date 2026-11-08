<?php
/**
 * Skan kondycji okazu: wybór zdjęcia, ostatni wynik z krokami i postępami względem poprzedniego skanu, historia skanów.
 * Wymaga: $tree, $treeUrl, $treeScans (najnowszy pierwszy), $photos, $e. Odświeżana przez specimen.js ([data-passport-scan]).
 */
use App\Service\PlantVision;
use App\Support\PolishDate;

$latest = $treeScans[0] ?? null;
$older = array_slice($treeScans, 1, 5);
$scanDate = static fn (int $timestamp): string => PolishDate::dayMonthYear(PolishDate::now()->setTimestamp($timestamp));
$progressIcons = ['improved' => 'trending_up', 'worse' => 'trending_down', 'unchanged' => 'trending_flat'];
?>
<section class="passport-scan" id="passport-scan" aria-labelledby="passport-scan-title" data-passport-scan>
    <header class="passport-section-header">
        <h2 class="passport-section-header__title" id="passport-scan-title">Skan kondycji</h2>
        <?php if ($treeScans !== []): ?>
            <span class="passport-section-header__count"><?= count($treeScans) ?> <?= $e(PolishDate::plural(count($treeScans), 'skan', 'skany', 'skanów')) ?></span>
        <?php endif; ?>
    </header>

    <p class="passport-scan__lead">Model z Kliniki ogląda zdjęcie okazu, wskazuje przyczynę problemu i podpowiada kroki. Każdy skan jest zestawiony z poprzednim, więc widać, czy drzewko się poprawia. Wynik to podpowiedź, nie diagnoza.</p>

    <form class="passport-scan__form" action="<?= $treeUrl ?>/scan" method="POST" enctype="multipart/form-data" data-scan-form>
        <?php include __DIR__ . '/csrf.html.php'; ?>
        <label class="btn passport-upload__label" for="passport-scan-photo">
            <span class="ms" aria-hidden="true">center_focus_strong</span>
            Skanuj nowe zdjęcie
        </label>
        <input class="visually-hidden" id="passport-scan-photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp" data-scan-input required>
        <button type="submit" class="btn passport-scan__send">Skanuj zdjęcie</button>
        <?php if ($photos !== []): ?>
            <button type="submit" class="btn passport-scan__latest" name="photo_id" value="<?= $e($photos[0]['id']) ?>" formnovalidate data-scan-existing>
                Skanuj ostatnie zdjęcie (<?= $e($scanDate((int) $photos[0]['at'])) ?>)
            </button>
        <?php endif; ?>
    </form>

    <div class="passport-scan__status" role="status" aria-live="polite" data-scan-status hidden>Model ogląda zdjęcie…</div>

    <?php if ($latest !== null): ?>
        <?php $d = is_array($latest['diagnosis'] ?? null) ? $latest['diagnosis'] : []; ?>
        <article class="passport-scan__result" data-level="<?= $e($d['condition'] ?? 'watch') ?>">
            <p class="passport-scan__when">
                Skan z <?= $e($scanDate((int) $latest['at'])) ?>, zdjęcie dodane <?= $e($scanDate((int) ($latest['photoAt'] ?? $latest['at']))) ?>
            </p>

            <?php if (!($d['plantVisible'] ?? false)): ?>
                <h3 class="passport-scan__cause">Nie widać drzewka na zdjęciu</h3>
                <p class="passport-scan__summary"><?= $e($d['summary'] ?? '') ?> Zrób zbliżenie liści lub igieł w świetle dziennym i zeskanuj ponownie.</p>
            <?php else: ?>
                <p class="passport-scan__confidence">Pewność wyniku: <?= $e(PlantVision::CONFIDENCE[$d['confidence'] ?? 'low'] ?? 'niska') ?>, wskaźnik kondycji <?= (int) $d['vitality'] ?>/100</p>
                <h3 class="passport-scan__cause"><?= $e(PlantVision::CAUSES[$d['cause']] ?? '') ?></h3>
                <p class="passport-scan__summary"><?= $e($d['summary'] ?? '') ?></p>

                <?php if (is_array($d['progress'] ?? null)): ?>
                    <?php $progress = $d['progress']; ?>
                    <div class="passport-scan__progress is-<?= $e($progress['status']) ?>">
                        <p class="passport-scan__progress-title">
                            <span class="ms" aria-hidden="true"><?= $e($progressIcons[$progress['status']] ?? 'trending_flat') ?></span>
                            Postępy: <?= $e($progress['label']) ?>
                        </p>
                        <p class="passport-scan__progress-text"><?= $e($progress['text']) ?></p>
                    </div>
                <?php else: ?>
                    <p class="passport-scan__progress-note">To pierwszy skan tego okazu. Kolejne zostaną z nim porównane.</p>
                <?php endif; ?>

                <div class="passport-scan__cols">
                    <div>
                        <h4 class="passport-scan__heading">Co widać na zdjęciu</h4>
                        <ul class="passport-scan__list">
                            <?php foreach (($d['observations'] ?? []) as $observation): ?>
                                <li><?= $e($observation) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div>
                        <h4 class="passport-scan__heading">Co zrobić</h4>
                        <ol class="passport-scan__list passport-scan__list--steps">
                            <?php foreach (($d['actions'] ?? []) as $action): ?>
                                <li><?= $e($action) ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>

                <?php if (($d['verify'] ?? '') !== ''): ?>
                    <p class="passport-scan__verify"><strong>Jak to sprawdzić:</strong> <?= $e($d['verify']) ?></p>
                <?php endif; ?>

                <?php if (($d['actions'] ?? []) !== []): ?>
                    <?php if ($latest['scheduled'] ?? false): ?>
                        <a href="/schedule" class="btn btn--tonal passport-scan__schedule is-done">
                            <span class="ms" aria-hidden="true">check</span>
                            Dodano do harmonogramu. Otwórz harmonogram
                        </a>
                    <?php else: ?>
                        <form action="/clinic/protocol" method="POST" class="passport-scan__schedule-form" data-scan-schedule>
                            <?php include __DIR__ . '/csrf.html.php'; ?>
                            <input type="hidden" name="label" value="<?= $e($tree['name']) ?>">
                            <input type="hidden" name="scan" value="<?= $e($latest['id']) ?>">
                            <input type="hidden" name="_back" value="<?= $treeUrl ?>#passport-scan">
                            <?php foreach ($d['actions'] as $action): ?>
                                <input type="hidden" name="steps[]" value="<?= $e($action) ?>">
                            <?php endforeach; ?>
                            <button type="submit" class="btn btn--tonal passport-scan__schedule">
                                <span class="ms" aria-hidden="true">event_available</span>
                                Dodaj kroki do harmonogramu
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </article>

        <?php if ($older !== []): ?>
            <h3 class="passport-scan__heading passport-scan__heading--history">Wcześniejsze skany</h3>
            <ul class="passport-scan__history">
                <?php foreach ($older as $scan): ?>
                    <?php $sd = is_array($scan['diagnosis'] ?? null) ? $scan['diagnosis'] : []; ?>
                    <li class="passport-scan__past">
                        <img src="/media/<?= $e($scan['file']) ?>" alt="" width="48" height="48" loading="lazy">
                        <div>
                            <p class="passport-scan__past-title"><?= $e($scan['title']) ?></p>
                            <?php
                            $meta = [$scanDate((int) ($scan['photoAt'] ?? $scan['at']))];
                            if ((int) $scan['vitality'] > 0) {
                                $meta[] = 'kondycja ' . (int) $scan['vitality'] . '/100';
                            }
                            if (is_array($sd['progress'] ?? null)) {
                                $meta[] = mb_strtolower($sd['progress']['label']);
                            }
                            ?>
                            <p class="passport-scan__past-meta"><?= $e(implode(', ', $meta)) ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php else: ?>
        <p class="passport-empty">Ten okaz nie ma jeszcze skanów. Zeskanuj zdjęcie, a wynik razem z krokami pojawi się tutaj; kolejne skany będą z nim porównywane.</p>
    <?php endif; ?>
</section>
