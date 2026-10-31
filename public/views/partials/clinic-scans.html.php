<?php
/**
 * Lista ostatnich skanów użytkownika (desktop i mobile). Wymaga: $scans (najnowsze pierwsze).
 */
use App\Support\PolishDate;

$scanEscape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$scanLevels = ['good' => 'Dobra kondycja', 'watch' => 'Do obserwacji', 'warning' => 'Ostrzeżenie', 'critical' => 'Krytyczny'];
?>
<div class="clinic-scans" data-scans-list>
    <?php foreach ($scans as $scan): ?>
        <a href="/media/<?= $scanEscape($scan['file']) ?>" target="_blank" rel="noopener" class="clinic-previous__item clinic-scans__item">
            <img src="/media/<?= $scanEscape($scan['file']) ?>" alt="" width="40" height="40" loading="lazy">
            <span class="clinic-previous__text">
                <span class="clinic-previous__meta"><?= $scanEscape(PolishDate::ago((int) $scan['at'])) ?> • <?= (int) $scan['vitality'] ?> pkt witalności</span>
                <span class="clinic-previous__note"><?= $scanEscape($scan['title']) ?> (<?= $scanEscape($scanLevels[$scan['level']] ?? '') ?>)</span>
            </span>
            <span class="ms" aria-hidden="true">open_in_new</span>
        </a>
    <?php endforeach; ?>
    <p class="clinic-scans__empty" data-scans-empty<?= $scans === [] ? '' : ' hidden' ?>>Brak zapisanych skanów. Wgraj zdjęcie, a analiza pojawi się tutaj.</p>
</div>
