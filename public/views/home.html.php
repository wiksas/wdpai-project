<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?? 'Strona główna'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="page header-inner">
            <h1><?= htmlspecialchars($appName); ?></h1>
            <nav aria-label="Główna nawigacja">
                <ul class="nav-list">
                    <li><a href="/" aria-current="page">Start</a></li>
                    <li><a href="/projects">Projekty</a></li>
                    <li><a href="/login">Logowanie</a></li>
                    <li><a href="/register">Rejestracja</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="page">
        <section class="hero">
            <div>
                <h2>Nowoczesne zarządzanie projektami</h2>
                <p class="text-muted" style="margin-block: 1rem;">
                    <?= htmlspecialchars($welcomeMessage); ?>
                </p>
                <a href="/projects" class="btn" style="text-decoration: none; display: inline-block;">
                    Przejdź do panelu projektów &rarr;
                </a>
            </div>
            <div class="hero-card">
                <h3>Dlaczego <?= htmlspecialchars($appName); ?>?</h3>
                <ul class="task-list text-muted">
                    <li>Przejrzysta tablica ze statusami wszystkich zadań</li>
                    <li>Szybkie wyszukiwanie i filtrowanie projektów</li>
                    <li>Responsywny interfejs dopasowany do telefonu i komputera</li>
                </ul>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="page">
            <p>&copy; 2026 <?= htmlspecialchars($appName); ?> - WDPAI</p>
        </div>
    </footer>
</body>
</html>