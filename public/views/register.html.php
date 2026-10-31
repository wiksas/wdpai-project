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

        <main class="auth-card">
            <a href="/" class="auth-logo" aria-label="KOMOREBI — strona główna">
                <span class="auth-logo__mark"><span class="ms" aria-hidden="true">potted_plant</span></span>
                <span class="auth-logo__name">KOMOREBI</span>
                <span class="auth-logo__tag">Sztuka i Ochrona Drzew · Bonsai Sanctuary</span>
            </a>

            <nav class="auth-tabs" aria-label="Wybór formularza">
                <a href="/register" class="auth-tab auth-tab--register is-active" aria-current="page">
                    <span class="auth-tab__desktop">Nowe konto</span>
                    <span class="auth-tab__mobile">Nowy Hodowca</span>
                </a>
                <a href="/login" class="auth-tab auth-tab--login">Logowanie</a>
            </nav>

            <h1 class="auth-title">Otwórz swoją księgę hodowcy</h1>
            <p class="auth-lead">Zarejestruj się, aby monitorować biologiczny stan i ewolucję swoich drzew.</p>

            <div class="auth-social">
                <button type="button" class="auth-social__btn">
                    <svg class="auth-social__logo" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                        <path fill="#4285F4" d="M23.52 12.27c0-.85-.08-1.67-.22-2.45H12v4.64h6.46a5.52 5.52 0 0 1-2.4 3.62v3h3.88c2.27-2.09 3.58-5.17 3.58-8.81z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.96-1.07 7.94-2.9l-3.88-3.01c-1.07.72-2.45 1.15-4.06 1.15-3.13 0-5.78-2.11-6.73-4.95H1.26v3.11A12 12 0 0 0 12 24z"/>
                        <path fill="#FBBC05" d="M5.27 14.29A7.2 7.2 0 0 1 4.89 12c0-.8.14-1.57.38-2.29V6.6H1.26A12 12 0 0 0 0 12c0 1.94.46 3.77 1.26 5.4l4.01-3.11z"/>
                        <path fill="#EA4335" d="M12 4.77c1.76 0 3.34.61 4.59 1.8l3.44-3.44C17.95 1.19 15.24 0 12 0A12 12 0 0 0 1.26 6.6l4.01 3.11C6.22 6.88 8.87 4.77 12 4.77z"/>
                    </svg>
                    Kontynuuj z Google
                </button>
                <button type="button" class="auth-social__btn">
                    <svg class="auth-social__logo" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                        <path fill="currentColor" d="M17.05 12.54c-.03-2.79 2.28-4.13 2.38-4.2-1.3-1.9-3.32-2.16-4.04-2.19-1.72-.17-3.36 1.01-4.23 1.01-.88 0-2.22-.99-3.65-.96-1.88.03-3.61 1.09-4.58 2.77-1.95 3.39-.5 8.4 1.4 11.15.93 1.34 2.03 2.85 3.48 2.8 1.4-.06 1.93-.9 3.62-.9 1.69 0 2.17.9 3.65.88 1.51-.03 2.46-1.37 3.38-2.72 1.07-1.56 1.5-3.07 1.53-3.15-.03-.01-2.93-1.13-2.96-4.49zM14.27 4.35c.77-.94 1.29-2.24 1.15-3.54-1.11.05-2.46.74-3.26 1.67-.71.82-1.34 2.15-1.17 3.42 1.24.1 2.5-.63 3.28-1.55z"/>
                    </svg>
                    Kontynuuj z Apple
                </button>
            </div>

            <p class="auth-divider"><span>lub za pomocą e-maila</span></p>

            <?php
            $e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
            $speciesOptions = [
                'pinus' => 'Sosna (Pinus)',
                'acer' => 'Klon palmowy (Acer)',
                'juniperus' => 'Jałowiec (Juniperus)',
                'ficus' => 'Fikus ginseng',
                'ulmus' => 'Wiąz drobnolistny (Ulmus)',
            ];
            $levelOptions = [
                'beginner' => ['Początkujący', 'Pierwsze drzewko w kolekcji'],
                'enthusiast' => ['Pasjonat', '2–5 lat doświadczenia'],
                'master' => ['Mistrz', 'Ponad 5 lat praktyki'],
            ];
            ?>
            <form action="/register" method="POST" class="auth-form" novalidate>
                <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                <?php if ($errors !== []): ?>
                    <p class="auth-alert" role="alert">
                        <span class="ms" aria-hidden="true">error</span>
                        Popraw zaznaczone pola i spróbuj ponownie.
                    </p>
                <?php endif; ?>

                <div class="auth-form__row">
                    <div class="field auth-field">
                        <label class="field__label auth-field__label" for="register-name">Imię hodowcy</label>
                        <div class="input-group auth-input">
                            <span class="ms" aria-hidden="true">person</span>
                            <input class="input" type="text" id="register-name" name="name" value="<?= $e($old['name']) ?>" placeholder="np. Julian Klemens" autocomplete="name" required<?= isset($errors['name']) ? ' aria-invalid="true" aria-describedby="register-name-error"' : '' ?>>
                        </div>
                        <?php if (isset($errors['name'])): ?><p class="auth-error" id="register-name-error"><?= $e($errors['name']) ?></p><?php endif; ?>
                    </div>
                    <div class="field auth-field">
                        <label class="field__label auth-field__label" for="register-email">Adres e-mail</label>
                        <div class="input-group auth-input">
                            <span class="ms" aria-hidden="true">mail</span>
                            <input class="input" type="email" id="register-email" name="email" value="<?= $e($old['email']) ?>" placeholder="sanctuary@komorebi.pl" autocomplete="email" required<?= isset($errors['email']) ? ' aria-invalid="true" aria-describedby="register-email-error"' : '' ?>>
                        </div>
                        <?php if (isset($errors['email'])): ?><p class="auth-error" id="register-email-error"><?= $e($errors['email']) ?></p><?php endif; ?>
                    </div>
                </div>

                <div class="field auth-field">
                    <label class="field__label auth-field__label" for="register-password">Klucz dostępu (Hasło)</label>
                    <div class="input-group auth-input">
                        <span class="ms" aria-hidden="true">lock</span>
                        <input class="input" type="password" id="register-password" name="password" placeholder="Minimum 8 znaków" minlength="8" autocomplete="new-password" required<?= isset($errors['password']) ? ' aria-invalid="true" aria-describedby="register-password-error"' : '' ?>>
                        <button type="button" class="input-group__action auth-input__action" aria-label="Pokaż hasło">
                            <span class="ms" aria-hidden="true">visibility</span>
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?><p class="auth-error" id="register-password-error"><?= $e($errors['password']) ?></p><?php endif; ?>
                </div>

                <fieldset class="auth-fieldset">
                    <legend class="auth-fieldset__legend">Poziom doświadczenia w sztuce bonsai</legend>
                    <div class="auth-levels">
                        <?php foreach ($levelOptions as $value => [$title, $text]): ?>
                            <label class="auth-level">
                                <input class="visually-hidden" type="radio" name="experience" value="<?= $e($value) ?>"<?= $old['experience'] === $value ? ' checked' : '' ?>>
                                <span class="auth-level__title"><?= $e($title) ?></span>
                                <span class="auth-level__text"><?= $e($text) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <fieldset class="auth-fieldset">
                    <legend class="auth-fieldset__legend">Posiadane gatunki w ogrodzie (opcjonalnie)</legend>
                    <span class="auth-fieldset__hint">Wybór wielokrotny</span>
                    <div class="auth-chips">
                        <?php foreach ($speciesOptions as $value => $label): ?>
                            <label class="auth-chip">
                                <input class="visually-hidden" type="checkbox" name="species[]" value="<?= $e($value) ?>"<?= in_array($value, $old['species'], true) ? ' checked' : '' ?>>
                                <span class="auth-chip__body"><?= $e($label) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <label class="checkbox auth-terms">
                    <input type="checkbox" name="terms" value="1"<?= $old['terms'] ? ' checked' : '' ?> required<?= isset($errors['terms']) ? ' aria-invalid="true" aria-describedby="register-terms-error"' : '' ?>>
                    <span>Akceptuję regulamin sanktuarium i etykę hodowli</span>
                </label>
                <?php if (isset($errors['terms'])): ?><p class="auth-error" id="register-terms-error"><?= $e($errors['terms']) ?></p><?php endif; ?>

                <button type="submit" class="btn btn--primary btn--block auth-submit">
                    Rozpocznij Praktykę
                    <span class="ms" aria-hidden="true">arrow_forward</span>
                </button>
            </form>

            <ul class="auth-assurance">
                <li class="auth-assurance__item">
                    <span class="ms" aria-hidden="true">verified_user</span>
                    Szyfrowanie biometryczne i SSL
                </li>
                <li class="auth-assurance__item auth-assurance__item--terra">
                    <span class="ms" aria-hidden="true">eco</span>
                    1% darowizny na ochronę prastarych borów
                </li>
            </ul>
        </main>
    </div>
    <?php include __DIR__ . '/partials/foot.html.php'; ?>
</body>
</html>
