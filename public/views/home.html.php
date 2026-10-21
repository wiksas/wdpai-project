<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?? 'Strona główna'; ?></title>
</head>
<body>
    <header>
        <h1><?= $appName; ?></h1>
        <nav>
            <ul>
                <li><a href="/">Start</a></li>
                <li><a href="/projects">Przeglądaj projekty</a></li>
                <li><a href="/login">Logowanie</a></li>
                <li><a href="/register">Rejestracja</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Witamy w aplikacji!</h2>
            <p><?= $welcomeMessage; ?></p>
            <a href="/projects">Przejdź do panelu projektów</a>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 <?= $appName; ?></p>
    </footer>
</body>
</html>