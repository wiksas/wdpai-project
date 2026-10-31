<!DOCTYPE html>
<html lang="pl">
<head>
    <?php include __DIR__ . '/partials/head.html.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/partials/site-header.html.php'; ?>

    <main class="container not-found">
        <section class="not-found__card" aria-labelledby="not-found-title">
            <span class="icon-tile icon-tile--terra not-found__icon"><span class="ms" aria-hidden="true">nature</span></span>
            <?php $code = (int) ($errorCode ?? 404); ?>
            <span class="eyebrow">Błąd <?= $code ?><?= $code === 404 ? ' · Ścieżka nie istnieje' : '' ?></span>
            <h1 id="not-found-title" class="not-found__title"><?= htmlspecialchars((string) ($errorTitle ?? 'Nie odnaleziono tej ścieżki w ogrodzie'), ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="lead">
                <?= htmlspecialchars((string) ($errorMessage ?? 'Strona o podanym adresie nie istnieje.'), ENT_QUOTES, 'UTF-8') ?>
                <?= $code === 404 ? 'Mogła zostać przycięta lub przeniesiona do innej części kolekcji KOMOREBI.' : '' ?>
            </p>
            <div class="not-found__actions">
                <a href="/" class="btn btn--primary btn--lg">
                    <span class="ms" aria-hidden="true">home</span>
                    Strona główna
                </a>
                <a href="/collection" class="btn btn--secondary btn--lg">Moja Kolekcja</a>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/partials/site-footer.html.php'; ?>
    <?php include __DIR__ . '/partials/foot.html.php'; ?>
</body>
</html>
