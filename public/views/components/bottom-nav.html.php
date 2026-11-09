<?php
$activeNav = $activeNav ?? '';
$bottomLinks = [
    'collection' => ['href' => '/collection', 'icon' => 'potted_plant', 'label' => 'Kolekcja'],
    'clinic' => ['href' => '/clinic', 'icon' => 'vital_signs', 'label' => 'Klinika'],
    'studio' => ['href' => '/studio', 'icon' => 'content_cut', 'label' => 'Studio'],
    'schedule' => ['href' => '/schedule', 'icon' => 'calendar_month', 'label' => 'Harmonogram'],
    'home' => ['href' => '/', 'icon' => 'home', 'label' => 'Start'],
];
?>
<nav class="bottom-nav" aria-label="Nawigacja mobilna">
    <?php foreach ($bottomLinks as $key => $link): ?>
        <a href="<?= $link['href'] ?>" class="bottom-nav__link<?= $key === $activeNav ? ' is-active' : '' ?>"<?= $key === $activeNav ? ' aria-current="page"' : '' ?>>
            <span class="ms" aria-hidden="true"><?= $link['icon'] ?></span>
            <?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8') ?>
        </a>
    <?php endforeach; ?>
</nav>
