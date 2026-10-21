<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Logowanie'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="page header-inner">
            <h1>ProjectFlow</h1>
            <nav aria-label="Nawigacja">
                <ul class="nav-list">
                    <li><a href="/">Start</a></li>
                    <li><a href="/register">Rejestracja</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="auth-wrapper">
        <section class="auth-card">
            <h2><?= htmlspecialchars($pageTitle); ?></h2>
            <p class="text-muted">Wprowadź swoje dane, aby przejść do panelu.</p>

            <form class="auth-form" action="/login" method="POST">
                <div class="form-group">
                    <label for="email">Adres e-mail</label>
                    <input type="email" id="email" name="email" placeholder="jan.kowalski@example.com" required>
                </div>
                <div class="form-group">
                    <label for="password">Hasło</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-full">Zaloguj się</button>
            </form>

            <p class="text-muted">Nie masz konta? <a href="/register">Zarejestruj się</a></p>
        </section>
    </main>

    <footer class="site-footer">
        <div class="page">
            <p>&copy; 2026 ProjectFlow - WDPAI</p>
        </div>
    </footer>
</body>
</html>