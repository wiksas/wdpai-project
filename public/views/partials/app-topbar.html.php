<?php
// Opcjonalnie: $topbarBack = ['href' => '/projects', 'title' => 'Karta Okazu Bonsai'] — na mobile zastępuje logo przyciskiem powrotu.
$topbarBack = $topbarBack ?? null;
?>
<header class="app-topbar<?= $topbarBack !== null ? ' app-topbar--back' : '' ?>">
    <?php if ($topbarBack !== null): ?>
        <a href="<?= htmlspecialchars($topbarBack['href'], ENT_QUOTES, 'UTF-8') ?>" class="app-topbar__back" aria-label="Wróć">
            <span class="ms" aria-hidden="true">arrow_back_ios_new</span>
        </a>
        <p class="app-topbar__title"><?= htmlspecialchars($topbarBack['title'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <a href="/" class="brand app-topbar__brand" aria-label="KOMOREBI — strona główna">
        <span class="brand__mark"><span class="ms" aria-hidden="true">potted_plant</span></span>
        <span class="brand__text">
            <span class="brand__name">KOMOREBI</span>
            <span class="brand__tag"><span lang="ja">木漏れ日</span> · Bonsai</span>
        </span>
    </a>

    <form class="search app-topbar__search" action="/projects" method="GET" role="search">
        <span class="ms" aria-hidden="true">search</span>
        <label class="visually-hidden" for="app-search">Szukaj okazu</label>
        <input class="search__input" type="search" id="app-search" name="q" placeholder="Wyszukaj okaz w szklarni (drzewko, styl, donica)...">
    </form>

    <div class="app-topbar__actions">
        <span class="pill pill--neutral app-topbar__status">
            <span class="ms" aria-hidden="true">shield</span>
            Prewencja Usychania: Aktywna
        </span>
        <a href="/schedule" class="icon-button icon-button--notify app-topbar__notify" aria-label="Powiadomienia (1 nowe)">
            <span class="ms" aria-hidden="true">notifications</span>
        </a>
        <?php if ($topbarBack !== null): ?>
            <button type="button" class="icon-button app-topbar__more" aria-label="Więcej opcji">
                <span class="ms" aria-hidden="true">more_vert</span>
            </button>
        <?php endif; ?>
        <span class="app-topbar__divider" aria-hidden="true"></span>
        <?php $userMenuTrigger = 'avatar'; ?>
        <?php include __DIR__ . '/user-menu.html.php'; ?>
    </div>
</header>
