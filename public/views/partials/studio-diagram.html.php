<?php
/**
 * Rysunek stylu drzewka (SVG): drzewo z `BonsaiArt` i nakładka dydaktyczna (pion nad nasadą, trójkąt korony, numery
 * opisów). Wymaga: $style (z StudioCatalog::style()). Nakładkę (.studio-overlay) można wyłączyć przełącznikiem nad rysunkiem.
 */
use App\Support\BonsaiArt;

$d = $style['diagram'];
$pt = static fn (array $p): string => $p[0] . ',' . $p[1];
[$axisX, $axisTop, $axisBottom] = $d['axis'];
?>
<svg class="studio-detail__diagram" viewBox="0 0 520 480" role="img" aria-label="Rysunek stylu <?= htmlspecialchars($style['name'], ENT_QUOTES, 'UTF-8') ?>: pień, gałęzie, korony i donica">
    <?= BonsaiArt::tree($d, $style['id']) ?>
    <g class="studio-overlay" aria-hidden="true">
        <polygon class="studio-diagram__triangle" points="<?= $pt($d['triangle'][0]) ?> <?= $pt($d['triangle'][1]) ?> <?= $pt($d['triangle'][2]) ?>"/>
        <line class="studio-svg-axis" x1="<?= (int) $axisX ?>" y1="<?= (int) $axisTop ?>" x2="<?= (int) $axisX ?>" y2="<?= (int) $axisBottom ?>"/>
        <?php foreach ($d['marks'] as $index => $mark): ?>
            <g class="studio-mark">
                <circle cx="<?= (int) $mark[0] ?>" cy="<?= (int) $mark[1] ?>" r="12"/>
                <text x="<?= (int) $mark[0] ?>" y="<?= (int) $mark[1] + 4.5 ?>"><?= $index + 1 ?></text>
            </g>
        <?php endforeach; ?>
    </g>
</svg>
