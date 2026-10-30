<?php
$activeNav = $activeNav ?? '';
$sidebarLinks = [
    'collection' => ['href' => '/collection', 'icon' => 'potted_plant', 'label' => 'Moja Kolekcja'],
    'clinic' => ['href' => '/clinic', 'icon' => 'vital_signs', 'label' => 'Klinika AI'],
    'studio' => ['href' => '/studio', 'icon' => 'menu_book', 'label' => 'Studio Formowania'],
    'schedule' => ['href' => '/schedule', 'icon' => 'calendar_month', 'label' => 'Harmonogram'],
    'discover' => ['href' => '/', 'icon' => 'explore', 'label' => 'Odkryj & Sklep'],
];
?>
<aside class="app-sidebar" aria-label="Panel boczny">
    <a href="/" class="brand" aria-label="KOMOREBI — strona główna">
        <span class="brand__mark"><span class="ms" aria-hidden="true">potted_plant</span></span>
        <span class="brand__text">
            <span class="brand__name">KOMOREBI</span>
            <span class="brand__tag"><span lang="ja">木漏れ日</span> · Bonsai</span>
        </span>
    </a>

    <section class="status-card" aria-label="Status kolekcji">
        <div class="status-card__row">
            <span>Status Kolekcji</span>
            <span class="status-card__value">98% Zdrowia</span>
        </div>
        <div class="progress progress--thin" role="presentation">
            <span class="progress__bar" style="--value: 98%"></span>
        </div>
        <div class="status-card__row status-card__row--meta">
            <span class="status-card__alert"><span class="ms" aria-hidden="true">water_drop</span>1 wymaga podlania</span>
            <span>12 okazów</span>
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
        <section class="weather-card" aria-label="Stacja pogodowa">
            <h2 class="weather-card__title">Stacja Pogodowa &amp; Wilgoć</h2>
            <p>Warszawa: 19°C, Wilgotność 64%. Idealna pora na zraszanie igieł.</p>
        </section>

        <div class="app-user">
            <span class="avatar" aria-hidden="true"><?= htmlspecialchars((string) $currentUser['initials'], ENT_QUOTES, 'UTF-8') ?></span>
            <span>
                <span class="app-user__name"><?= htmlspecialchars((string) $currentUser['name'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="app-user__plan"><?= htmlspecialchars((string) $currentUser['label'], ENT_QUOTES, 'UTF-8') ?></span>
            </span>
        </div>
    </div>
</aside>
