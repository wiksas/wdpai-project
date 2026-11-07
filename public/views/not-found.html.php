<!DOCTYPE html>
<html lang="pl" data-theme="<?= htmlspecialchars((string) ($theme ?? 'system'), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/partials/head.html.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/partials/site-header.html.php'; ?>

    <main class="container not-found">
        <?php $code = (int) ($errorCode ?? 404); ?>
        <section class="not-found__card" aria-labelledby="not-found-title">
            <p class="eyebrow">Błąd <?= $code ?></p>
            <h1 id="not-found-title" class="not-found__title"><?= htmlspecialchars((string) ($errorTitle ?? 'Nie ma takiej strony'), ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="lead">
                <?= htmlspecialchars((string) ($errorMessage ?? 'Strona o podanym adresie nie istnieje.'), ENT_QUOTES, 'UTF-8') ?>
                <?= $code === 404 ? 'Sprawdź adres albo wróć do kolekcji, jeśli szukasz konkretnego okazu.' : '' ?>
            </p>
            <div class="not-found__actions">
                <a href="/collection" class="btn btn--primary btn--lg">Przejdź do kolekcji</a>
                <a href="/" class="btn btn--ghost btn--lg">Strona główna</a>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/partials/site-footer.html.php'; ?>
    <?php include __DIR__ . '/partials/foot.html.php'; ?>
</body>
</html>
