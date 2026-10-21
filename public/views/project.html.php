<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $projectTitle ?? 'Szczegóły projektu'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="page header-inner">
            <h1>ProjectFlow</h1>
            <nav aria-label="Nawigacja">
                <ul class="nav-list">
                    <li><a href="/projects">&larr; Powrót do listy projektów</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="page" style="flex: 1;">
        <article class="project-card">
            <header class="actions">
                <h2><?= $projectTitle; ?></h2>
                <span class="badge">ID: <?= htmlspecialchars($projectId); ?></span>
            </header>

            <p class="text-muted">Właściciel projektu: <strong><?= htmlspecialchars($projectOwner); ?></strong></p>

            <section>
                <h3>Zadania w projekcie</h3>
                <ul class="task-list">
                    <li>Przygotowanie architektury MVC i routingu z parametrem</li>
                    <li>Zbudowanie semantycznego szkieletu HTML5</li>
                    <li>Wdrożenie responsywnych stylów CSS (Flexbox &amp; Grid)</li>
                </ul>
            </section>
        </article>
    </main>

    <footer class="site-footer">
        <div class="page">
            <p>&copy; 2026 ProjectFlow - WDPAI</p>
        </div>
    </footer>
</body>
</html>