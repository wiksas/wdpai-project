<?php
// Układ: layouts/auth.html.php
$layout = 'auth';
?>
<div class="auth-card__top">
    <a href="/" class="brand auth-logo" aria-label="KOMOREBI, strona główna">
        <?php include __DIR__ . '/components/logo-mark.html.php'; ?>
        <span class="brand__name">KOMOREBI</span>
    </a>
    <nav class="auth-tabs" aria-label="Wybór formularza">
        <a href="/login" class="auth-tab">Logowanie</a>
        <a href="/register" class="auth-tab is-active" aria-current="page">Nowe konto</a>
    </nav>
    <?php include __DIR__ . '/components/theme-toggle.html.php'; ?>
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
// Etykiety i pola do listy błędów u góry formularza (link przenosi do pola).
$errorTargets = [
    'login' => ['Login', 'register-login'],
    'name' => ['Imię', 'register-name'],
    'email' => ['Adres e-mail', 'register-email'],
    'password' => ['Hasło', 'register-password'],
    'password_repeat' => ['Powtórz hasło', 'register-password-repeat'],
    'terms' => ['Zgoda', 'register-terms'],
];
$fieldError = static fn (string $field): string => isset($errors[$field])
    ? '<p class="auth-error" id="register-' . str_replace('_', '-', $field) . '-error"><span class="ms" aria-hidden="true">error</span>' . $e($errors[$field]) . '</p>'
    : '';
?>
<form action="/register" method="POST" class="auth-form" novalidate>
    <?php include __DIR__ . '/partials/csrf.html.php'; ?>
    <?php if ($errors !== []): ?>
        <div class="auth-alert auth-alert--list" role="alert" tabindex="-1" data-error-summary>
            <span class="ms" aria-hidden="true">error</span>
            <div>
                <p class="auth-alert__title">Konto nie zostało założone. Popraw <?= count($errors) === 1 ? 'jedno pole' : count($errors) . ' pola' ?>:</p>
                <ul class="auth-alert__list">
                    <?php foreach ($errorTargets as $field => [$label, $target]): ?>
                        <?php if (isset($errors[$field])): ?>
                            <li><a href="#<?= $target ?>"><?= $label ?>:</a> <?= $e($errors[$field]) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <p class="auth-alert__note">Ze względów bezpieczeństwa hasło nie jest zachowywane. Wpisz je ponownie w obu polach.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="auth-form__row">
        <div class="field auth-field">
            <label class="field__label auth-field__label" for="register-login">Login</label>
            <div class="input-group auth-input">
                <span class="ms" aria-hidden="true">badge</span>
                <input class="input" type="text" id="register-login" name="login" value="<?= $e($old['login']) ?>" minlength="3" maxlength="30" pattern="[A-Za-z][A-Za-z0-9._\-]{2,29}" autocomplete="username" autocapitalize="none" spellcheck="false" required aria-describedby="register-login-hint<?= isset($errors['login']) ? ' register-login-error' : '' ?>"<?= isset($errors['login']) ? ' aria-invalid="true"' : '' ?>>
            </div>
            <p class="auth-hint" id="register-login-hint">Unikalny, 3–30 znaków: litery bez polskich znaków, cyfry, kropka, _ lub -. Wielkość liter nie ma znaczenia.</p>
            <?= $fieldError('login') ?>
        </div>
        <div class="field auth-field">
            <label class="field__label auth-field__label" for="register-name">Imię</label>
            <div class="input-group auth-input">
                <span class="ms" aria-hidden="true">person</span>
                <input class="input" type="text" id="register-name" name="name" value="<?= $e($old['name']) ?>" autocomplete="name" required<?= isset($errors['name']) ? ' aria-invalid="true" aria-describedby="register-name-error"' : '' ?>>
            </div>
            <?= $fieldError('name') ?>
        </div>
    </div>

    <div class="field auth-field">
        <label class="field__label auth-field__label" for="register-email">Adres e-mail</label>
        <div class="input-group auth-input">
            <span class="ms" aria-hidden="true">mail</span>
            <input class="input" type="email" id="register-email" name="email" value="<?= $e($old['email']) ?>" placeholder="ty@przyklad.pl" autocomplete="email" required<?= isset($errors['email']) ? ' aria-invalid="true" aria-describedby="register-email-error"' : '' ?>>
        </div>
        <?= $fieldError('email') ?>
    </div>

    <div class="auth-form__row">
        <div class="field auth-field">
            <label class="field__label auth-field__label" for="register-password">Hasło</label>
            <div class="input-group auth-input">
                <span class="ms" aria-hidden="true">lock</span>
                <input class="input" type="password" id="register-password" name="password" minlength="8" maxlength="128" autocomplete="new-password" required aria-describedby="register-password-hint<?= isset($errors['password']) ? ' register-password-error' : '' ?>"<?= isset($errors['password']) ? ' aria-invalid="true"' : '' ?>>
                <button type="button" class="input-group__action auth-input__action" aria-label="Pokaż hasło" aria-pressed="false">
                    <span class="ms" aria-hidden="true">visibility</span>
                </button>
            </div>
            <p class="auth-hint" id="register-password-hint">Od 8 do 128 znaków. Zapisujemy tylko skrót hasła (Argon2id), nigdy samo hasło.</p>
            <?= $fieldError('password') ?>
        </div>

        <div class="field auth-field">
            <label class="field__label auth-field__label" for="register-password-repeat">Powtórz hasło</label>
            <div class="input-group auth-input">
                <span class="ms" aria-hidden="true">lock</span>
                <input class="input" type="password" id="register-password-repeat" name="password_repeat" minlength="8" maxlength="128" autocomplete="new-password" required<?= isset($errors['password_repeat']) ? ' aria-invalid="true" aria-describedby="register-password-repeat-error"' : '' ?>>
                <button type="button" class="input-group__action auth-input__action" aria-label="Pokaż hasło" aria-pressed="false">
                    <span class="ms" aria-hidden="true">visibility</span>
                </button>
            </div>
            <?= $fieldError('password_repeat') ?>
        </div>
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

    <div class="auth-terms-box<?= isset($errors['terms']) ? ' is-invalid' : '' ?>" data-invalid-box>
        <label class="checkbox auth-terms">
            <input type="checkbox" id="register-terms" name="terms" value="1"<?= $old['terms'] ? ' checked' : '' ?> required<?= isset($errors['terms']) ? ' aria-invalid="true" aria-describedby="register-terms-error"' : '' ?>>
            <span>Rozumiem, że KOMOREBI to projekt edukacyjny, a dane konta są zapisywane na tym serwerze demonstracyjnym.</span>
        </label>
        <?= $fieldError('terms') ?>
    </div>

    <button type="submit" class="btn btn--primary btn--block auth-submit">Załóż konto</button>
</form>
