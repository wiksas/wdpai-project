<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja opiekuna — KOMOREBI</title>
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
                    <p class="serif" style="font-size: 1.1rem;">Rejestr Opiekunów KOMOREBI</p>
                    <small style="color: var(--color-text-muted);">Cyfrowy paszport i historia formowania każdego okazu</small>
                </div>

                <h2 style="color: #ffffff; font-size: 1.35rem;">Co zyskujesz po utworzeniu konta?</h2>
                <ul class="auth-step-list">
                    <li><strong>1. Indywidualny kalendarz podlewania</strong> Powiadomienia dopasowane do pojemności donicy i podłoża.</li>
                    <li><strong>2. Archiwum fotograficzne rozwoju</strong> Śledź ewolucję nebari oraz korony na przestrzeni lat.</li>
                    <li><strong>3. Szybka diagnostyka SOS</strong> Bezpośredni dostęp do protokołów ratunkowych.</li>
                </ul>
            </div>

            <small style="opacity: 0.7;">&copy; 2026 KOMOREBI</small>
        </aside>

        <main class="auth-main">
            <section class="auth-card" aria-labelledby="reg-title">
                <div class="auth-tabs">
                    <a href="/register" class="auth-tab active">Załóż konto</a>
                    <a href="/login" class="auth-tab">Zaloguj się</a>
                </div>

                <h1 id="reg-title" style="font-size: 1.7rem;">Utwórz profil opiekuna bonsai</h1>
                <p style="color: var(--color-text-muted); font-size: 0.88rem; margin-top: 0.25rem;">
                    Wprowadź dane swojego stanowiska uprawowego, aby skonfigurować dziennik KOMOREBI.
                </p>

                <form action="/register" method="POST" class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="reg-name">Imię</label>
                        <input class="form-input" type="text" id="reg-name" name="name" placeholder="Jan" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="reg-surname">Nazwisko</label>
                        <input class="form-input" type="text" id="reg-surname" name="surname" placeholder="Kowalski" required>
                    </div>
                    <div class="form-group full">
                        <label class="form-label" for="reg-email">Adres e-mail</label>
                        <input class="form-input" type="email" id="reg-email" name="email" placeholder="opiekun@komorebi.pl" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="reg-pass">Hasło</label>
                        <input class="form-input" type="password" id="reg-pass" name="password" placeholder="Min. 8 znaków" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="reg-location">Stanowisko uprawowe</label>
                        <select class="form-select" id="reg-location" name="location">
                            <option value="balcony">Balkon / Taras południowy</option>
                            <option value="indoor">Parapet wewnętrzny</option>
                            <option value="garden">Ogród całoroczny</option>
                        </select>
                    </div>
                    <div class="form-group full" style="margin-top: 0.5rem;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Zarejestruj konto opiekuna
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>

</body>
</html>