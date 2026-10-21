<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $projectTitle ?? 'Projekt'; ?></title>
</head>
<body>
    <header>
        <nav>
            <a href="/projects">&larr; Powrót do listy projektów</a>
        </nav>
    </header>

    <main>
        <article>
            <header>
                <h1><?= $projectTitle; ?></h1>
                <p>Identyfikator w systemie (z parametru URL): <strong><?= htmlspecialchars($projectId); ?></strong></p>
                <p>Właściciel projektu: <strong><?= htmlspecialchars($projectOwner); ?></strong></p>
            </header>

            <section>
                <h2>Zadania w projekcie</h2>
                <ul>
                    <li>Przygotowanie architektury MVC</li>
                    <li>Zbudowanie semantycznego szkieletu HTML</li>
                    <li>Konfiguracja Composera i przestrzeni nazw</li>
                </ul>
            </section>
        </article>
    </main>

    <footer>
        <p>&copy; 2026 ProjectFlow</p>
    </footer>
</body>
</html>