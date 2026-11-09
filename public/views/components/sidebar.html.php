<?php
$activeNav = $activeNav ?? '';
$sidebarLinks = [
    'collection' => ['href' => '/collection', 'icon' => 'potted_plant', 'label' => 'Moja kolekcja'],
    'clinic' => ['href' => '/clinic', 'icon' => 'vital_signs', 'label' => 'Klinika'],
    'studio' => ['href' => '/studio', 'icon' => 'menu_book', 'label' => 'Studio formowania'],
    'schedule' => ['href' => '/schedule', 'icon' => 'calendar_month', 'label' => 'Harmonogram'],
    'home' => ['href' => '/', 'icon' => 'home', 'label' => 'Strona główna'],
];
?>
<aside class="app-sidebar" aria-label="Panel boczny">
    <a href="/" class="brand" aria-label="KOMOREBI, strona główna">
        <?php include __DIR__ . '/logo-mark.html.php'; ?>
        <span class="brand__text">
            <span class="brand__name">KOMOREBI</span>
            <span class="brand__tag"><span lang="ja">木漏れ日</span> · Bonsai</span>
        </span>
    </a>

    <?php
    $sbHealth = (int) ($sidebarSummary['health'] ?? 100);
    $sbThirsty = (int) ($sidebarSummary['thirsty'] ?? 0);
    $sbTotal = (int) ($sidebarSummary['total'] ?? 0);
    ?>
    <section class="status-card" aria-label="Status kolekcji">
        <div class="status-card__row">
            <span>Stan kolekcji</span>
            <span class="status-card__value" data-sidebar-health><?= $sbHealth ?>% zdrowia</span>
        </div>
        <div class="progress progress--thin" aria-hidden="true">
            <span class="progress__bar" style="--value: <?= $sbHealth ?>%"></span>
        </div>
        <div class="status-card__row status-card__row--meta">
            <span class="status-card__alert<?= $sbThirsty > 0 ? ' is-urgent' : '' ?>" data-sidebar-thirsty><span class="ms" aria-hidden="true">water_drop</span><?= $sbThirsty === 0 ? 'Wszystko nawodnione' : $sbThirsty . ' ' . ($sbThirsty === 1 ? 'wymaga podlania' : 'wymagają podlania') ?></span>
            <span><?= $sbTotal ?> <?= \App\Support\PolishDate::plural($sbTotal, 'okaz', 'okazy', 'okazów') ?></span>
        </div>
    </section>

    <nav class="app-nav" aria-label="Moduły aplikacji">
        <?php foreach ($sidebarLinks as $key => $link): ?>
            <a href="<?= $link['href'] ?>" class="app-nav__link<?= $key === $activeNav ? ' is-active' : '' ?>"<?= $key === $activeNav ? ' aria-current="page"' : '' ?>>
                <span class="ms" aria-hidden="true"><?= $link['icon'] ?></span>
                <?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8') ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="app-sidebar__footer">
        <div class="app-user">
            <span class="avatar" aria-hidden="true"><?= htmlspecialchars((string) $currentUser['initials'], ENT_QUOTES, 'UTF-8') ?></span>
            <span>
                <span class="app-user__name"><?= htmlspecialchars((string) $currentUser['name'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="app-user__plan"><?= htmlspecialchars((string) $currentUser['label'], ENT_QUOTES, 'UTF-8') ?></span>
            </span>
        </div>
    </div>
</aside>
