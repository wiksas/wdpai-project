<!DOCTYPE html>
<html lang="pl">
<head>
    <?php include __DIR__ . '/partials/head.html.php'; ?>
    <link rel="stylesheet" href="<?= $asset('/css/pages/auth.css') ?>">
</head>
<body class="auth-page">
    <div class="auth">
        <aside class="auth-panel" aria-label="KOMOREBI — sanktuarium hodowców bonsai">
            <a href="/" class="brand auth-panel__brand" aria-label="KOMOREBI — strona główna">
                <span class="brand__mark auth-panel__mark"><span class="ms" aria-hidden="true">potted_plant</span></span>
                <span class="brand__text">
                    <span class="brand__name auth-panel__name">KOMOREBI</span>
                    <span class="brand__tag auth-panel__tag"><span lang="ja">木漏れ日</span> · Bonsai</span>
                </span>
            </a>

            <figure class="auth-panel__art">
                <img src="/images/ink-bonsai-paper.jpg" alt="Tuszowy rysunek sosny bonsai w płaskiej donicy na czerpanym papierze" width="264" height="264">
            </figure>

            <blockquote class="auth-quote">
                <span class="auth-quote__mark" aria-hidden="true">99</span>
                <p class="auth-quote__text">„Drzewo mówi do ciebie poprzez każdy opadający liść. Trzeba tylko nauczyć się słuchać.”</p>
                <footer class="auth-quote__author">— Mistrz Kenjiro Sato, Kioto</footer>
            </blockquote>

            <ul class="auth-perks">
                <li class="auth-perk">
                    <span class="auth-perk__icon"><span class="ms" aria-hidden="true">water_drop</span></span>
                    <div>
                        <h2 class="auth-perk__title">Precyzyjne alerty podlewania</h2>
                        <p class="auth-perk__text">Kalkulacja mikroklimatu, wilgotności akadamy i cyklu słonecznego w Twoim ogrodzie.</p>
                    </div>
                </li>
                <li class="auth-perk">
                    <span class="auth-perk__icon"><span class="ms" aria-hidden="true">document_scanner</span></span>
                    <div>
                        <h2 class="auth-perk__title">Diagnostyka chorób w 3 sekundy</h2>
                        <p class="auth-perk__text">Analiza wizualna igieł i kory wsparta bazą ponad 400 patogenów drzew bonsai.</p>
                    </div>
                </li>
                <li class="auth-perk">
                    <span class="auth-perk__icon"><span class="ms" aria-hidden="true">history_edu</span></span>
                    <div>
                        <h2 class="auth-perk__title">Archiwum progresu formowania</h2>
                        <p class="auth-perk__text">Wielosezonowe oś czasu: drutowanie, cięcie strukturalne oraz rejestr przesadzeń.</p>
                    </div>
                </li>
            </ul>

            <p class="auth-panel__footer">
                <span>Sanktuarium Opieki Botanicznej</span>
                <span>Wabi-Sabi Precision</span>
            </p>
        </aside>

        <main class="auth-card auth-card--login">
            <a href="/" class="auth-logo" aria-label="KOMOREBI — strona główna">
                <span class="auth-logo__mark"><span class="ms" aria-hidden="true">potted_plant</span></span>
                <span class="auth-logo__name">KOMOREBI</span>
                <span class="auth-logo__tag">Sztuka i Ochrona Drzew · Bonsai Sanctuary</span>
            </a>

            <nav class="auth-tabs" aria-label="Wybór formularza">
                <a href="/register" class="auth-tab auth-tab--register">
                    <span class="auth-tab__desktop">Nowe konto</span>
                    <span class="auth-tab__mobile">Nowy Hodowca</span>
                </a>
                <a href="/login" class="auth-tab auth-tab--login is-active" aria-current="page">Logowanie</a>
            </nav>

            <h1 class="auth-title">Wróć do swojego ogrodu</h1>
            <p class="auth-lead">Zaloguj się, aby sprawdzić stan i dzisiejsze potrzeby swoich drzew.</p>

            <div class="auth-social">
                <button type="button" class="auth-social__btn">
                    <svg class="auth-social__logo" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                        <path fill="currentColor" d="M17.05 12.54c-.03-2.79 2.28-4.13 2.38-4.2-1.3-1.9-3.32-2.16-4.04-2.19-1.72-.17-3.36 1.01-4.23 1.01-.88 0-2.22-.99-3.65-.96-1.88.03-3.61 1.09-4.58 2.77-1.95 3.39-.5 8.4 1.4 11.15.93 1.34 2.03 2.85 3.48 2.8 1.4-.06 1.93-.9 3.62-.9 1.69 0 2.17.9 3.65.88 1.51-.03 2.46-1.37 3.38-2.72 1.07-1.56 1.5-3.07 1.53-3.15-.03-.01-2.93-1.13-2.96-4.49zM14.27 4.35c.77-.94 1.29-2.24 1.15-3.54-1.11.05-2.46.74-3.26 1.67-.71.82-1.34 2.15-1.17 3.42 1.24.1 2.5-.63 3.28-1.55z"/>
                    </svg>
                    Apple ID
                </button>
                <button type="button" class="auth-social__btn">
                    <svg class="auth-social__logo" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                        <path fill="#4285F4" d="M23.52 12.27c0-.85-.08-1.67-.22-2.45H12v4.64h6.46a5.52 5.52 0 0 1-2.4 3.62v3h3.88c2.27-2.09 3.58-5.17 3.58-8.81z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.96-1.07 7.94-2.9l-3.88-3.01c-1.07.72-2.45 1.15-4.06 1.15-3.13 0-5.78-2.11-6.73-4.95H1.26v3.11A12 12 0 0 0 12 24z"/>
                        <path fill="#FBBC05" d="M5.27 14.29A7.2 7.2 0 0 1 4.89 12c0-.8.14-1.57.38-2.29V6.6H1.26A12 12 0 0 0 0 12c0 1.94.46 3.77 1.26 5.4l4.01-3.11z"/>
                        <path fill="#EA4335" d="M12 4.77c1.76 0 3.34.61 4.59 1.8l3.44-3.44C17.95 1.19 15.24 0 12 0A12 12 0 0 0 1.26 6.6l4.01 3.11C6.22 6.88 8.87 4.77 12 4.77z"/>
                    </svg>
                    Google
                </button>
            </div>

            <p class="auth-divider"><span>lub e-mailem</span></p>

            <form action="/login" method="POST" class="auth-form">
                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                <?php if (isset($errors['form'])): ?>
                    <p class="auth-alert" role="alert">
                        <span class="ms" aria-hidden="true">error</span>
                        <?= htmlspecialchars((string) $errors['form'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <div class="field auth-field">
                    <div class="auth-field__head">
                        <label class="field__label auth-field__label" for="login-login">Login lub adres e-mail</label>
                        <span class="auth-field__meta">Hortus ID</span>
                    </div>
                    <div class="input-group auth-input">
                        <span class="ms" aria-hidden="true">alternate_email</span>
                        <input class="input" type="text" id="login-login" name="login" value="<?= htmlspecialchars((string) ($old['login'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="hodowca@komorebi.pl" autocomplete="username" autocapitalize="none" spellcheck="false" required<?= isset($errors['form']) ? ' aria-invalid="true"' : '' ?>>
                    </div>
                </div>

                <div class="field auth-field">
                    <div class="auth-field__head">
                        <label class="field__label auth-field__label" for="login-password">Hasło dostępu</label>
                        <button type="button" class="auth-field__link">Przypomnij</button>
                    </div>
                    <div class="input-group auth-input">
                        <span class="ms" aria-hidden="true">lock</span>
                        <input class="input" type="password" id="login-password" name="password" placeholder="••••••••" autocomplete="current-password" required<?= isset($errors['form']) ? ' aria-invalid="true"' : '' ?>>
                        <button type="button" class="input-group__action auth-input__action" aria-label="Pokaż hasło">
                            <span class="ms" aria-hidden="true">visibility</span>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn--primary btn--block auth-submit">
                    Wejdź do Ogrodu Bonsai
                    <span class="ms" aria-hidden="true">arrow_forward</span>
                </button>
            </form>

            <p class="auth-demo">
                <span class="ms" aria-hidden="true">info</span>
                <span>Konto demonstracyjne: login <strong>admin</strong>, hasło <strong>admin</strong></span>
            </p>

            <ul class="auth-benefits">
                <li class="auth-benefit">
                    <span class="icon-tile auth-benefit__icon"><span class="ms" aria-hidden="true">fingerprint</span></span>
                    <div>
                        <h2 class="auth-benefit__title">Szyfrowanie biometryczne FaceID</h2>
                        <p class="auth-benefit__text">Bezpieczny dziennik hodowlany w chmurze</p>
                    </div>
                </li>
                <li class="auth-benefit">
                    <span class="icon-tile icon-tile--terra auth-benefit__icon"><span class="ms" aria-hidden="true">volunteer_activism</span></span>
                    <div>
                        <h2 class="auth-benefit__title">1% dla prastarych borów</h2>
                        <p class="auth-benefit__text">Wsparcie dla rezerwatów pierwotnych puszcz</p>
                    </div>
                </li>
            </ul>

            <p class="auth-tagline"><span lang="ja">木漏れ日</span> · Światło przesączające się przez liście drzew</p>
        </main>
    </div>
    <?php include __DIR__ . '/partials/foot.html.php'; ?>
</body>
</html>
