<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie — KOMOREBI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

    <div class="auth-wrapper">
        <aside class="auth-sidebar">
            <div>
                <a href="/" class="brand-logo" style="color: #ffffff;">🌿 KOMOREBI</a>

                <div class="auth-poster">
                    <p class="serif" style="font-size: 1.1rem;">Witaj ponownie w KOMOREBI</p>
                    <small style="color: var(--color-text-muted);">Sprawdź dzisiejsze alerty wilgotności swoich drzewek</small>
                </div>

                <h2 style="color: #ffffff; font-size: 1.35rem;">Status Twojej kolekcji czeka</h2>
                <ul class="auth-step-list">
                    <li><strong>Monitoring podłoża</strong> Sprawdź, które okazy wymagają dzisiaj kąpieli wodnej.</li>
                    <li><strong>Kalendarz formowania</strong> Zobacz zaplanowane terminy zdjęcia drutu miedzianego.</li>
                </ul>
            </div>

            <small style="opacity: 0.7;">&copy; 2026 KOMOREBI</small>
        </aside>

        <main class="auth-main">
            <section class="auth-card" aria-labelledby="login-title">
                <div class="auth-tabs">
                    <a href="/register" class="auth-tab">Załóż konto</a>
                    <a href="/login" class="auth-tab active">Zaloguj się</a>
                </div>

                <h1 id="login-title" style="font-size: 1.7rem;">Zaloguj się do panelu</h1>
                <p style="color: var(--color-text-muted); font-size: 0.88rem; margin-top: 0.25rem;">
                    Wpisz swój adres e-mail oraz hasło, aby przejść do kolekcji drzewek KOMOREBI.
                </p>

                <form action="/login" method="POST" class="form-grid">
                    <div class="form-group full">
                        <label class="form-label" for="login-email">Adres e-mail</label>
                        <input class="form-input" type="email" id="login-email" name="email" placeholder="opiekun@komorebi.pl" required>
                    </div>
                    <div class="form-group full">
                        <label class="form-label" for="login-pass">Hasło</label>
                        <input class="form-input" type="password" id="login-pass" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="form-group full" style="margin-top: 0.5rem;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Otwórz moją kolekcję
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>

</body>
</html>