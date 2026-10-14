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
            <a href="/recipes">&larr; Wróć do listy przepisów</a>
        </nav>
    </header>

    <main>
        <article>
            <header>
                <h1><?= htmlspecialchars($recipeName) ?> (ID: <?= (int) $recipeId ?>)</h1>
                <p>Czas przygotowania: <strong><?= htmlspecialchars($prepTime) ?></strong> | Kalorie: <strong><?= (int) $calories ?> kcal</strong></p>
            </header>

            <section>
                <h2>Składniki</h2>
                <ul>
                    <li>Składnik bazowy 100g</li>
                    <li>Świeże warzywa i przyprawy</li>
                    <li>Oliwa z oliwek 1 łyżka</li>
                </ul>
            </section>

            <section>
                <h2>Sposób przygotowania</h2>
                <ol>
                    <li>Przygotuj i umyj wszystkie składniki.</li>
                    <li>Połącz składniki zgodnie z instrukcją i gotuj przez <?= htmlspecialchars($prepTime) ?>.</li>
                    <li>Podawaj na ciepło.</li>
                </ol>
            </section>
        </article>
    </main>

    <footer>
        <p>&copy; 2026 MealPlanner — Projekt WDPAI</p>
    </footer>
</body>
</html>