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
        <header>
            <h1><?= htmlspecialchars($pageTitle) ?></h1>
            <p>Liczba dostępnych przepisów: <strong><?= (int) $recipesCount ?></strong> | Dzienny cel: <strong><?= (int) $dailyCaloriesLimit ?> kcal</strong></p>
        </header>

        <section aria-label="Wyszukiwarka i filtry">
            <h2>Filtruj posiłki</h2>
            <form action="/recipes" method="get">
                <label for="search">Szukaj przepisu:</label>
                <input type="search" id="search" name="q" placeholder="np. owsianka">
                <button type="submit">Szukaj</button>
            </form>
        </section>

        <section aria-label="Lista przepisów">
            <h2>Propozycje na dziś</h2>
            <?php foreach ($recipes as $item): ?>
                <article>
                    <header>
                        <h3><?= htmlspecialchars($item['title']) ?></h3>
                        <p>Kategoria: <?= htmlspecialchars($item['category']) ?></p>
                    </header>
                    <p>Kaloryczność: <strong><?= (int) $item['calories'] ?> kcal</strong> | Czas przygotowania: <?= htmlspecialchars($item['time']) ?></p>
                    <footer>
                        <a href="/recipes/<?= (int) $item['id'] ?>">Zobacz szczegóły przepisu #<?= (int) $item['id'] ?></a>
                    </footer>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 MealPlanner — Projekt WDPAI</p>
    </footer>
</body>
</html>