<!DOCTYPE html>
<html lang="pl" data-theme="<?= htmlspecialchars((string) ($theme ?? 'system'), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/partials/head.html.php'; ?>
    <link rel="stylesheet" href="<?= $asset('/css/pages/auth.css') ?>">
</head>
<body class="auth-page">
    <div class="auth">
        <?php include __DIR__ . '/partials/auth-panel.html.php'; ?>

        <main class="auth-card">
            <div class="auth-card__top">
                <a href="/" class="brand auth-logo" aria-label="KOMOREBI, strona główna">
                    <span class="brand__mark"><span class="ms" aria-hidden="true">potted_plant</span></span>
                    <span class="brand__name">KOMOREBI</span>
                </a>
                <nav class="auth-tabs" aria-label="Wybór formularza">
                    <a href="/login" class="auth-tab">Logowanie</a>
                    <a href="/register" class="auth-tab is-active" aria-current="page">Nowe konto</a>
                </nav>
                <?php include __DIR__ . '/partials/theme-toggle.html.php'; ?>
            </div>

            <h1 class="auth-title">Otwórz swoją księgę hodowcy</h1>
            <p class="auth-lead">Załóż konto, aby prowadzić kolekcję drzew, dziennik zabiegów i harmonogram pielęgnacji.</p>

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
                        <label class="field__label auth-field__label" for="register-name">Imię</label>
                        <div class="input-group auth-input">
                            <span class="ms" aria-hidden="true">person</span>
                            <input class="input" type="text" id="register-name" name="name" value="<?= $e($old['name']) ?>" autocomplete="name" required<?= isset($errors['name']) ? ' aria-invalid="true" aria-describedby="register-name-error"' : '' ?>>
                        </div>
                        <?php if (isset($errors['name'])): ?><p class="auth-error" id="register-name-error"><?= $e($errors['name']) ?></p><?php endif; ?>
                    </div>
                    <div class="field auth-field">
                        <label class="field__label auth-field__label" for="register-email">Adres e-mail</label>
                        <div class="input-group auth-input">
                            <span class="ms" aria-hidden="true">mail</span>
                            <input class="input" type="email" id="register-email" name="email" value="<?= $e($old['email']) ?>" placeholder="ty@przyklad.pl" autocomplete="email" required<?= isset($errors['email']) ? ' aria-invalid="true" aria-describedby="register-email-error"' : '' ?>>
                        </div>
                        <?php if (isset($errors['email'])): ?><p class="auth-error" id="register-email-error"><?= $e($errors['email']) ?></p><?php endif; ?>
                    </div>
                </div>

                <div class="field auth-field">
                    <label class="field__label auth-field__label" for="register-password">Hasło</label>
                    <div class="input-group auth-input">
                        <span class="ms" aria-hidden="true">lock</span>
                        <input class="input" type="password" id="register-password" name="password" minlength="8" autocomplete="new-password" required aria-describedby="register-password-hint<?= isset($errors['password']) ? ' register-password-error' : '' ?>"<?= isset($errors['password']) ? ' aria-invalid="true"' : '' ?>>
                        <button type="button" class="input-group__action auth-input__action" aria-label="Pokaż hasło" aria-pressed="false">
                            <span class="ms" aria-hidden="true">visibility</span>
                        </button>
                    </div>
                    <p class="auth-hint" id="register-password-hint">Co najmniej 8 znaków.</p>
                    <?php if (isset($errors['password'])): ?><p class="auth-error" id="register-password-error"><?= $e($errors['password']) ?></p><?php endif; ?>
                </div>

                <fieldset class="auth-fieldset">
                    <legend class="auth-fieldset__legend">Doświadczenie w bonsai</legend>
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
                    <legend class="auth-fieldset__legend">Gatunki w Twojej kolekcji <span class="auth-fieldset__optional">(opcjonalnie, możesz wybrać kilka)</span></legend>
                    <div class="auth-chips">
                        <?php foreach ($speciesOptions as $value => $label): ?>
                            <label class="auth-chip">
                                <input class="visually-hidden" type="checkbox" name="species[]" value="<?= $e($value) ?>"<?= in_array($value, $old['species'], true) ? ' checked' : '' ?>>
                                <span class="auth-chip__body"><span class="ms auth-chip__check" aria-hidden="true">check</span><?= $e($label) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <label class="checkbox auth-terms">
                    <input type="checkbox" name="terms" value="1"<?= $old['terms'] ? ' checked' : '' ?> required<?= isset($errors['terms']) ? ' aria-invalid="true" aria-describedby="register-terms-error"' : '' ?>>
                    <span>Rozumiem, że KOMOREBI to projekt edukacyjny, a dane konta są zapisywane na tym serwerze demonstracyjnym.</span>
                </label>
                <?php if (isset($errors['terms'])): ?><p class="auth-error" id="register-terms-error"><?= $e($errors['terms']) ?></p><?php endif; ?>

                <button type="submit" class="btn btn--primary btn--block auth-submit">Załóż konto</button>
            </form>
        </main>
    </div>
    <?php include __DIR__ . '/partials/foot.html.php'; ?>
</body>
</html>
