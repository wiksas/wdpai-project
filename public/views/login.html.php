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
        <a href="/login" class="auth-tab is-active" aria-current="page">Logowanie</a>
        <a href="/register" class="auth-tab">Nowe konto</a>
    </nav>
    <?php include __DIR__ . '/components/theme-toggle.html.php'; ?>
</div>

<h1 class="auth-title">Wróć do swojego ogrodu</h1>
<p class="auth-lead">Zaloguj się, aby sprawdzić stan i dzisiejsze potrzeby swoich drzew.</p>

<form action="/login" method="POST" class="auth-form">
    <?php include __DIR__ . '/partials/csrf.html.php'; ?>
    <?php if (isset($errors['form'])): ?>
        <p class="auth-alert" role="alert">
            <span class="ms" aria-hidden="true">error</span>
            <?= htmlspecialchars((string) $errors['form'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <div class="field auth-field">
        <label class="field__label auth-field__label" for="login-login">Login lub adres e-mail</label>
        <div class="input-group auth-input">
            <span class="ms" aria-hidden="true">person</span>
            <input class="input" type="text" id="login-login" name="login" value="<?= htmlspecialchars((string) ($old['login'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="username" autocapitalize="none" spellcheck="false" required<?= isset($errors['form']) ? ' aria-invalid="true"' : '' ?>>
        </div>
    </div>

    <div class="field auth-field">
        <label class="field__label auth-field__label" for="login-password">Hasło</label>
        <div class="input-group auth-input">
            <span class="ms" aria-hidden="true">lock</span>
            <input class="input" type="password" id="login-password" name="password" autocomplete="current-password" required<?= isset($errors['form']) ? ' aria-invalid="true"' : '' ?>>
            <button type="button" class="input-group__action auth-input__action" aria-label="Pokaż hasło" aria-pressed="false">
                <span class="ms" aria-hidden="true">visibility</span>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn--primary btn--block auth-submit">Zaloguj się</button>
</form>

<?php if (!empty($unverified)): ?>
    <form action="/verify-email/resend" method="POST" class="auth-verify__form auth-verify__form--login">
        <?php include __DIR__ . '/partials/csrf.html.php'; ?>
        <input type="hidden" name="identifier" value="<?= htmlspecialchars((string) ($old['login'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" class="btn btn--secondary btn--block">
            <span class="ms" aria-hidden="true">send</span>
            Wyślij link aktywacyjny ponownie
        </button>
    </form>
<?php endif; ?>

<p class="auth-demo">
    <span class="ms" aria-hidden="true">info</span>
    <span>Konto demonstracyjne: login <strong>admin</strong>, hasło <strong>admin</strong>.</span>
</p>
