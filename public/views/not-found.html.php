<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Nie znaleziono strony</title>
</head>
<body>
    <main>
        <section>
            <h1>Błąd 404</h1>
            <p><?= $errorMessage ?? 'Nie znaleziono strony.'; ?></p>
            <a href="/">Wróć na stronę główną</a>
        </section>
    </main>
</body>
</html>