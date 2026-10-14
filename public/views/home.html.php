<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
    <header>
        <nav aria-label="Główna nawigacja">
            <strong>MealPlanner</strong>
            <ul>
                <li><a href="/">Start</a></li>
                <li><a href="/recipes">Przepisy</a></li>
                <li><a href="/login">Logowanie</a></li>
                <li><a href="/register">Rejestracja</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h1>Witaj w MealPlanner!</h1>
            <p>Twoje centrum planowania codziennych posiłków, liczenia kalorii i organizacji listy zakupów.</p>
            <p><a href="/recipes">Przejdź do katalogu przepisów</a></p>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 MealPlanner — Projekt WDPAI</p>
    </footer>
</body>
</html>