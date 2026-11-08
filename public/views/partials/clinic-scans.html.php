<?php
/**
 * Lista ostatnich skanów użytkownika. Wymaga: $scans (najnowsze pierwsze).
 * Odświeżana przez clinic.js po zapisie skanu (podmiana [data-scans-list]).
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
                    <a href="/media/<?= $scanEscape($scan['file']) ?>" target="_blank" rel="noopener" class="clinic-scans__item">
                        <img class="clinic-scans__thumb" src="/media/<?= $scanEscape($scan['file']) ?>" alt="" width="56" height="56" loading="lazy">
                        <span class="clinic-scans__text">
                            <span class="clinic-scans__title"><?= $scanEscape($scan['title']) ?></span>
                            <span class="clinic-scans__meta">
                                <?= $scanEscape($scanParts[$scan['part'] ?? ''] ?? 'Zdjęcie') ?>, <?= $scanEscape(PolishDate::ago((int) $scan['at'])) ?>
                            </span>
                            <?php if ((int) $scan['vitality'] > 0): ?>
                                <span class="clinic-scans__score clinic-scans__score--<?= $scanEscape($scanLevel) ?>"><?= $scanDiagnosis !== null ? 'Kondycja' : 'Indeks barwy' ?> <?= (int) $scan['vitality'] ?>/100, <?= $scanEscape($scanLevels[$scanLevel] ?? '') ?></span>
                            <?php endif; ?>
                        </span>
                        <span class="visually-hidden">(otwiera zdjęcie w nowej karcie)</span>
                        <span class="ms clinic-scans__open" aria-hidden="true">open_in_new</span>
                    </a>
                    <?php if ($scanDiagnosis !== null && ($scanDiagnosis['plantVisible'] ?? false)): ?>
                        <details class="clinic-scans__more">
                            <summary>Ocena i zalecenia, pewność <?= $scanEscape(\App\Service\PlantVision::CONFIDENCE[$scanDiagnosis['confidence'] ?? 'low'] ?? 'niska') ?></summary>
                            <p><?= $scanEscape($scanDiagnosis['summary'] ?? '') ?></p>
                            <ul>
                                <?php foreach (($scanDiagnosis['actions'] ?? []) as $scanAction): ?>
                                    <li><?= $scanEscape($scanAction) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </details>
                    <?php endif; ?>
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
