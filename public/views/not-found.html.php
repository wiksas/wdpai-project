<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Nie znaleziono strony</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <main class="auth-wrapper">
        <section class="auth-card" style="text-align: center;">
            <span class="badge">Błąd HTTP 404</span>
            <h1 style="margin-block: 0.75rem;">Nie znaleziono strony</h1>
            <p class="text-muted" style="margin-bottom: 1.5rem;">
                <?= htmlspecialchars($errorMessage ?? 'Strona o podanym adresie nie istnieje.'); ?>
            </p>
            <a href="/" class="btn btn-full">Wróć na stronę główną</a>
        </section>
    </main>
</body>
</html>