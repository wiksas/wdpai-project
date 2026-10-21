<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Projekty'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="page header-inner">
            <h1>ProjectFlow</h1>
            <nav aria-label="Główna nawigacja">
                <ul class="nav-list">
                    <li><a href="/">Strona główna</a></li>
                    <li><a href="/projects" aria-current="page">Projekty</a></li>
                    <li><a href="/login">Wyloguj</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="page dashboard-layout">
        <aside aria-label="Filtry i statystyki">
            <section class="summary-box">
                <h2>Podsumowanie</h2>
                <p class="text-muted">Widok: <strong><?= htmlspecialchars($title); ?></strong></p>
                <p class="text-muted">Łączna liczba projektów: <strong><?= $totalCount; ?></strong></p>
            </section>
        </aside>

        <main>
            <header class="actions">
                <h2>Twoje Projekty</h2>
                <form class="search-form" action="/projects" method="GET" role="search">
                    <label for="search-project" class="text-muted">Szukaj:</label>
                    <input type="search" id="search-project" name="q" placeholder="Wpisz nazwę...">
                    <button type="submit" class="btn">Szukaj</button>
                </form>
            </header>

            <section class="projects" aria-label="Lista projektów">
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $project): ?>
                        <article class="project-card">
                            <header>
                                <h3>
                                    <a href="/project/<?= $project['id']; ?>">
                                        <?= htmlspecialchars($project['name']); ?>
                                    </a>
                                </h3>
                            </header>
                            <p>Status: <span class="badge"><?= htmlspecialchars($project['status']); ?></span></p>
                            <footer>
                                <small class="text-muted">Autor: <?= htmlspecialchars($project['author']); ?></small>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Brak projektów do wyświetlenia.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <footer class="site-footer">
        <div class="page">
            <p>&copy; 2026 ProjectFlow - WDPAI</p>
        </div>
    </footer>
</body>
</html>