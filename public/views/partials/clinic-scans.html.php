<?php
/**
 * Lista ostatnich skanów użytkownika. Wymaga: $scans (najnowsze pierwsze).
 * Odświeżana przez clinic.js po zapisie skanu (podmiana [data-scans-list]). Opcjonalnie: $treeNames (id => nazwa okazu).
 */
use App\Support\PolishDate;

$scanEscape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$scanLevels = ['good' => 'dobra kondycja', 'watch' => 'do obserwacji', 'warning' => 'ostrzeżenie', 'critical' => 'stan krytyczny'];
$scanParts = ['leaves' => 'Liście i igły', 'bark' => 'Kora i pień', 'roots' => 'Podłoże i korzenie', 'wiring' => 'Drutowanie'];
?>
<div class="clinic-scans" data-scans-list>
    <?php if ($scans !== []): ?>
        <ul class="clinic-scans__list">
            <?php foreach ($scans as $scan): ?>
                <?php $scanLevel = (string) ($scan['level'] ?? 'watch'); ?>
                <?php $scanDiagnosis = is_array($scan['diagnosis'] ?? null) ? $scan['diagnosis'] : null; ?>
                <li>
                    <a href="/clinic/scans/<?= $scanEscape($scan['id']) ?>" class="clinic-scans__item">
                        <img class="clinic-scans__thumb" src="/media/<?= $scanEscape($scan['file']) ?>" alt="" width="56" height="56" loading="lazy">
                        <span class="clinic-scans__text">
                            <span class="clinic-scans__title"><?= $scanEscape($scan['title']) ?></span>
                            <span class="clinic-scans__meta">
                                <?= $scanEscape($scanParts[$scan['part'] ?? ''] ?? 'Zdjęcie') ?>, <?= $scanEscape(PolishDate::ago((int) $scan['at'])) ?>
                            </span>
                            <?php if (isset($scan['treeId'], ($treeNames ?? [])[(int) $scan['treeId']])): ?>
                                <span class="clinic-scans__tree"><span class="ms" aria-hidden="true">spa</span><?= $scanEscape($treeNames[(int) $scan['treeId']]) ?></span>
                            <?php endif; ?>
                            <?php if ((int) $scan['vitality'] > 0): ?>
                                <span class="clinic-scans__score clinic-scans__score--<?= $scanEscape($scanLevel) ?>"><?= $scanDiagnosis !== null ? 'Kondycja' : 'Indeks barwy' ?> <?= (int) $scan['vitality'] ?>/100, <?= $scanEscape($scanLevels[$scanLevel] ?? '') ?></span>
                            <?php endif; ?>
                        </span>
                        <span class="visually-hidden">(pokaż wynik skanu)</span>
                        <span class="ms clinic-scans__open" aria-hidden="true">chevron_right</span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="clinic-scans__empty" data-scans-empty>
            <p>Nie masz jeszcze zapisanych skanów. Wybierz zdjęcie w skanerze i kliknij „Zapisz skan”, a wynik pojawi się tutaj.</p>
            <label class="btn btn--tonal btn--sm clinic-scans__empty-action" for="clinic-photo">
                <span class="ms" aria-hidden="true">add_photo_alternate</span>
                Wybierz zdjęcie
            </label>
        </div>
    <?php endif; ?>
</div>
