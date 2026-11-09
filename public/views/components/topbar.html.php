<?php
// Opcjonalnie: $topbarBack = ['href' => '/collection', 'title' => 'Karta okazu']; na mobile zastępuje logo przyciskiem powrotu.
$topbarBack = $topbarBack ?? null;
?>
<header class="app-topbar<?= $topbarBack !== null ? ' app-topbar--back' : '' ?>">
    <?php if ($topbarBack !== null): ?>
        <a href="<?= htmlspecialchars($topbarBack['href'], ENT_QUOTES, 'UTF-8') ?>" class="app-topbar__back" aria-label="Wróć">
            <span class="ms" aria-hidden="true">arrow_back_ios_new</span>
        </a>
        <p class="app-topbar__title"><?= htmlspecialchars($topbarBack['title'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <a href="/" class="brand app-topbar__brand" aria-label="KOMOREBI, strona główna">
        <?php include __DIR__ . '/logo-mark.html.php'; ?>
        <span class="brand__text">
            <span class="brand__name">KOMOREBI</span>
            <span class="brand__tag"><span lang="ja">木漏れ日</span> · Bonsai</span>
        </span>
    </a>

    <form class="search app-topbar__search" action="/collection" method="GET" role="search">
        <span class="ms" aria-hidden="true">search</span>
        <label class="visually-hidden" for="app-search">Szukaj okazu</label>
        <input class="search__input" type="search" id="app-search" name="q" value="<?= htmlspecialchars((string) ($criteria['q'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Wyszukaj okaz w szklarni (drzewko, styl, donica)..." autocomplete="off">
    </form>

    <div class="app-topbar__actions">
        <?php include __DIR__ . '/theme-toggle.html.php'; ?>
        <?php include __DIR__ . '/notifications.html.php'; ?>
        <span class="app-topbar__divider" aria-hidden="true"></span>
        <?php $userMenuTrigger = 'avatar'; ?>
        <?php include __DIR__ . '/user-menu.html.php'; ?>
    </div>
</header>
