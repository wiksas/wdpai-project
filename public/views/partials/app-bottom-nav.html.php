<?php
$activeNav = $activeNav ?? '';
$bottomLinks = [
    'collection' => ['href' => '/collection', 'icon' => 'eco', 'label' => 'Kolekcja'],
    'clinic' => ['href' => '/clinic', 'icon' => 'document_scanner', 'label' => 'Diagnoza'],
    'studio' => ['href' => '/studio', 'icon' => 'content_cut', 'label' => 'Studio'],
    'knowledge' => ['href' => '/', 'icon' => 'menu_book', 'label' => 'Wiedza'],
    'profile' => ['href' => '/login', 'icon' => 'self_improvement', 'label' => 'Profil'],
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
