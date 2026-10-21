<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Projekty'; ?></title>
</head>
<body>
    <header>
        <h1>ProjectFlow</h1>
        <nav aria-label="Główna nawigacja">
            <ul>
                <li><a href="/">Strona główna</a></li>
                <li><a href="/projects" aria-current="page">Projekty</a></li>
                <li><a href="/login">Wyloguj</a></li>
            </ul>
        </nav>
    </header>

    <aside aria-label="Filtry i statystyki">
        <section>
            <h2>Podsumowanie</h2>
            <!-- Wyświetlenie przekazanych zmiennych z kontrolera -->
            <p>Widok: <strong><?= $title; ?></strong></p>
            <p>Łączna liczba projektów: <strong><?= $totalCount; ?></strong></p>
        </section>
    </aside>

    <main>
        <header>
            <h2>Twoje Projekty</h2>
            <form action="/projects" method="GET" role="search">
                <label for="search-project">Szukaj projektu:</label>
                <input type="search" id="search-project" name="q" placeholder="Wpisz nazwę...">
                <button type="submit">Szukaj</button>
            </form>
        </header>

        <section aria-label="Lista projektów">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                    <article>
                        <header>
                            <h3>
                                <a href="/project/<?= $project['id']; ?>">
                                    <?= htmlspecialchars($project['name']); ?>
                                </a>
                            </h3>
                        </header>
                        <p>Status: <mark><?= htmlspecialchars($project['status']); ?></mark></p>
                        <footer>
                            <small>Autor: <?= htmlspecialchars($project['author']); ?></small>
                        </footer>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Brak projektów do wyświetlenia.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 ProjectFlow - WDPAI</p>
    </footer>
</body>
</html>