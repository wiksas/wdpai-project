<?php
/**
 * Diagram stylu drzewka (SVG). Wymaga: $style (z StudioCatalog::style()).
 * Kolory są rolami (`leaf`, `apex`, `back`…), które studio.css zamienia na tokeny motywu.
 */
$d = $style['diagram'];
$points = $d['triangle'];
$fmt = static fn (float|int $n): string => rtrim(rtrim(number_format((float) $n, 1, '.', ''), '0'), '.');
$pt = static fn (array $p): string => $p[0] . ',' . $p[1];
$role = static fn (string $tone): string => 'studio-svg-' . preg_replace('/[^a-z-]/', '', $tone);
?>
<svg class="studio-detail__diagram" viewBox="24 48 446 412" role="img" aria-label="Rysunek stylu <?= htmlspecialchars($style['name'], ENT_QUOTES, 'UTF-8') ?>: pień, gałęzie i trójkąt korony">
    <polygon class="studio-diagram__triangle" points="<?= $pt($points[0]) ?> <?= $pt($points[1]) ?> <?= $pt($points[2]) ?>"/>
    <line class="studio-svg-axis" x1="246" y1="150" x2="246" y2="394"/>
    <line class="studio-svg-ground" x1="111" y1="382" x2="383" y2="382"/>
    <path class="studio-svg-trunk" d="M214 382 C 234 378, 240 362, 246 348 C 252 362, 258 378, 278 382 Z"/>
    <?php foreach ($d['branches'] as $branch): ?>
        <path class="studio-diagram__branch <?= $role($branch['tone'] ?? 'limb') ?>" d="<?= htmlspecialchars($branch['d'], ENT_QUOTES, 'UTF-8') ?>" stroke-width="<?= (int) $branch['w'] ?>" pathLength="1"/>
    <?php endforeach; ?>
    <path class="studio-diagram__branch studio-svg-limb" d="<?= htmlspecialchars($d['trunk'], ENT_QUOTES, 'UTF-8') ?>" stroke-width="12" pathLength="1"/>
    <?php foreach ($d['foliage'] as $leaf): ?>
        <ellipse class="studio-diagram__foliage <?= $role($leaf[4]) ?>" cx="<?= $fmt($leaf[0]) ?>" cy="<?= $fmt($leaf[1]) ?>" rx="<?= $fmt($leaf[2]) ?>" ry="<?= $fmt($leaf[3]) ?>"/>
    <?php endforeach; ?>
    <g class="studio-diagram__triangle-edge">
        <line x1="<?= $points[0][0] ?>" y1="<?= $points[0][1] ?>" x2="<?= $points[1][0] ?>" y2="<?= $points[1][1] ?>"/>
        <line x1="<?= $points[0][0] ?>" y1="<?= $points[0][1] ?>" x2="<?= $points[2][0] ?>" y2="<?= $points[2][1] ?>"/>
        <line x1="<?= $points[1][0] ?>" y1="<?= $points[1][1] ?>" x2="<?= $points[2][0] ?>" y2="<?= $points[2][1] ?>"/>
    </g>
    <text x="<?= (int) $d['angleAt'][0] ?>" y="<?= (int) $d['angleAt'][1] ?>" class="studio-svg-label"><?= htmlspecialchars($d['angle'], ENT_QUOTES, 'UTF-8') ?></text>
    <text x="<?= (int) $d['noteAt'][0] ?>" y="<?= (int) $d['noteAt'][1] ?>" class="studio-svg-label studio-svg-label--muted"><?= htmlspecialchars($d['note'], ENT_QUOTES, 'UTF-8') ?></text>
</svg>
