<header class="site-header">
    <div class="site-header__inner">
        <a href="/" class="brand" aria-label="KOMOREBI, strona główna">
            <?php include __DIR__ . '/logo-mark.html.php'; ?>
            <span class="brand__text">
                <span class="brand__name">KOMOREBI</span>
                <span class="brand__tag"><span lang="ja">木漏れ日</span> · Bonsai</span>
            </span>
        </a>

        <form class="search site-header__search" action="/collection" method="GET" role="search">
            <span class="ms" aria-hidden="true">search</span>
            <label class="visually-hidden" for="site-search">Szukaj okazu</label>
            <input class="search__input" type="search" id="site-search" name="q" placeholder="Szukaj okazu lub stylu (np. Pinus thunbergii)">
        </form>

        <nav class="site-nav" aria-label="Główna nawigacja">
            <a href="/collection" class="site-nav__link">Moja kolekcja</a>
            <a href="/clinic" class="site-nav__link">Klinika</a>
            <a href="/studio" class="site-nav__link">Studio formowania</a>
            <a href="/schedule" class="site-nav__link">Harmonogram</a>
        </nav>

        <div class="site-header__tools">
            <?php include __DIR__ . '/theme-toggle.html.php'; ?>

            <?php if (($currentUser ?? null) !== null): ?>
                <?php include __DIR__ . '/notifications.html.php'; ?>
                <?php $userMenuTrigger = 'chip'; ?>
                <?php include __DIR__ . '/user-menu.html.php'; ?>
            <?php else: ?>
                <div class="site-header__auth">
                    <a href="/login" class="btn btn--ghost">Zaloguj się</a>
                    <a href="/register" class="btn btn--primary">Zarejestruj się</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>
