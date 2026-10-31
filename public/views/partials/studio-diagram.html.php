<?php
/**
 * Diagram stylu drzewka (SVG). Wymaga: $style (z StudioCatalog::style()).
 */
$d = $style['diagram'];
$points = $d['triangle'];
$fmt = static fn (float|int $n): string => rtrim(rtrim(number_format((float) $n, 1, '.', ''), '0'), '.');
$pt = static fn (array $p): string => $p[0] . ',' . $p[1];
?>
<svg class="studio-detail__diagram" viewBox="24 48 446 412" role="img" aria-label="Diagram stylu <?= htmlspecialchars($style['name'], ENT_QUOTES, 'UTF-8') ?>: trójkąt wierzchołka i gałęzi">
    <polygon class="studio-diagram__triangle" points="<?= $pt($points[0]) ?> <?= $pt($points[1]) ?> <?= $pt($points[2]) ?>" fill="#fe9e77" fill-opacity="0.08"/>
    <line x1="246" y1="150" x2="246" y2="394" stroke="#c2c8c1" stroke-width="1" stroke-dasharray="3 3"/>
    <line x1="111" y1="382" x2="383" y2="382" stroke="#727973" stroke-width="1.5"/>
    <path d="M214 382 C 234 378, 240 362, 246 348 C 252 362, 258 378, 278 382 Z" fill="#486554"/>
    <?php foreach ($d['branches'] as $branch): ?>
        <path class="studio-diagram__branch" d="<?= htmlspecialchars($branch['d'], ENT_QUOTES, 'UTF-8') ?>" fill="none" stroke="<?= htmlspecialchars($branch['color'] ?? '#1e3a2b', ENT_QUOTES, 'UTF-8') ?>" stroke-width="<?= (int) $branch['w'] ?>" stroke-linecap="round" pathLength="1"/>
    <?php endforeach; ?>
    <path class="studio-diagram__trunk" d="<?= htmlspecialchars($d['trunk'], ENT_QUOTES, 'UTF-8') ?>" fill="none" stroke="#1e3a2b" stroke-width="12" stroke-linecap="round" pathLength="1"/>
    <?php foreach ($d['foliage'] as $leaf): ?>
        <ellipse class="studio-diagram__foliage" cx="<?= $fmt($leaf[0]) ?>" cy="<?= $fmt($leaf[1]) ?>" rx="<?= $fmt($leaf[2]) ?>" ry="<?= $fmt($leaf[3]) ?>" fill="<?= htmlspecialchars($leaf[4], ENT_QUOTES, 'UTF-8') ?>"/>
    <?php endforeach; ?>
    <g fill="none" stroke="#fe9e77" stroke-width="1" stroke-dasharray="3 3">
        <line x1="<?= $points[0][0] ?>" y1="<?= $points[0][1] ?>" x2="<?= $points[1][0] ?>" y2="<?= $points[1][1] ?>"/>
        <line x1="<?= $points[0][0] ?>" y1="<?= $points[0][1] ?>" x2="<?= $points[2][0] ?>" y2="<?= $points[2][1] ?>"/>
        <line x1="<?= $points[1][0] ?>" y1="<?= $points[1][1] ?>" x2="<?= $points[2][0] ?>" y2="<?= $points[2][1] ?>"/>
    </g>
    <circle cx="<?= $points[0][0] ?>" cy="<?= $points[0][1] ?>" r="2.5" fill="#e8efe9"/>
    <text x="<?= (int) $d['angleAt'][0] ?>" y="<?= (int) $d['angleAt'][1] ?>" class="studio-svg-label studio-svg-label--terra"><?= htmlspecialchars($d['angle'], ENT_QUOTES, 'UTF-8') ?></text>
    <text x="<?= (int) $d['noteAt'][0] ?>" y="<?= (int) $d['noteAt'][1] ?>" class="studio-svg-label studio-svg-label--muted"><?= htmlspecialchars($d['note'], ENT_QUOTES, 'UTF-8') ?></text>
</svg>
