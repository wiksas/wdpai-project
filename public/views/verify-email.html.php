<?php
// Układ: layouts/auth.html.php
$layout = 'auth';

/**
 * Stany $state: sent (po rejestracji), resend (formularz ponownej wysyłki), expired (link wygasł po 24 h), invalid (link zły albo użyty).
 *
 * @var string $state
 * @var string $email
 * @var string $mailPreviewUrl
 */
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$content = [
    'sent' => ['mark_email_unread', 'Sprawdź skrzynkę'],
    'resend' => ['forward_to_inbox', 'Wyślij link ponownie'],
    'expired' => ['schedule', 'Link wygasł'],
    'invalid' => ['link_off', 'Link nie działa'],
][$state];
?>
<div class="auth-card__top">
    <a href="/" class="brand auth-logo" aria-label="KOMOREBI, strona główna">
        <?php include __DIR__ . '/components/logo-mark.html.php'; ?>
        <span class="brand__name">KOMOREBI</span>
    </a>
    <nav class="auth-tabs" aria-label="Wybór formularza">
        <a href="/login" class="auth-tab">Logowanie</a>
        <a href="/register" class="auth-tab">Nowe konto</a>
    </nav>
    <?php include __DIR__ . '/components/theme-toggle.html.php'; ?>
</div>

<span class="auth-verify__icon<?= in_array($state, ['expired', 'invalid'], true) ? ' auth-verify__icon--warn' : '' ?>" aria-hidden="true"><span class="ms"><?= $content[0] ?></span></span>
<h1 class="auth-title"><?= $content[1] ?></h1>

<?php if ($state === 'sent'): ?>
    <p class="auth-lead">Wysłaliśmy link aktywacyjny na <strong class="auth-verify__email"><?= $e($email) ?></strong>. Otwórz wiadomość i kliknij <em>Potwierdź adres e-mail</em>, a potem zaloguj się.</p>
    <ul class="auth-verify__steps">
        <li>Link działa 24 godziny i można go użyć raz.</li>
        <li>Nie widzisz wiadomości? Sprawdź folder spam albo oferty.</li>
        <li>Do czasu potwierdzenia adresu logowanie jest zablokowane.</li>
    </ul>
<?php elseif ($state === 'resend'): ?>
    <p class="auth-lead">Podaj login albo adres e-mail konta, które czeka na potwierdzenie. Wyślemy nowy link aktywacyjny.</p>
<?php elseif ($state === 'expired'): ?>
    <p class="auth-lead">Link aktywacyjny działa 24 godziny. Wyślij nowy na <strong class="auth-verify__email"><?= $e($email) ?></strong>, a poprzedni przestanie działać.</p>
<?php else: ?>
    <p class="auth-lead">Ten link jest nieprawidłowy albo został już użyty. Jeśli adres jest już potwierdzony, po prostu <a href="/login" class="auth-link">zaloguj się</a>. W innym wypadku wyślij nowy link.</p>
<?php endif; ?>

<form action="/verify-email/resend" method="POST" class="auth-form auth-verify__form">
    <?php include __DIR__ . '/partials/csrf.html.php'; ?>
    <?php if ($email !== ''): ?>
        <input type="hidden" name="identifier" value="<?= $e($email) ?>">
        <button type="submit" class="btn <?= $state === 'sent' ? 'btn--secondary' : 'btn--primary' ?> btn--block auth-submit">
            <span class="ms" aria-hidden="true">send</span>
            <?= $state === 'sent' ? 'Nie dotarło? Wyślij link ponownie' : 'Wyślij nowy link' ?>
        </button>
    <?php else: ?>
        <div class="field auth-field">
            <label class="field__label auth-field__label" for="verify-identifier">Login lub adres e-mail</label>
            <div class="input-group auth-input">
                <span class="ms" aria-hidden="true">person</span>
                <input class="input" type="text" id="verify-identifier" name="identifier" autocomplete="username" autocapitalize="none" spellcheck="false" required>
            </div>
        </div>
        <button type="submit" class="btn btn--primary btn--block auth-submit">
            <span class="ms" aria-hidden="true">send</span>
            Wyślij link aktywacyjny
        </button>
    <?php endif; ?>
</form>

<?php if ($mailPreviewUrl !== ''): ?>
    <p class="auth-demo">
        <span class="ms" aria-hidden="true">info</span>
        <span>Środowisko deweloperskie: wiadomości nie wychodzą na zewnątrz, tylko trafiają do skrzynki testowej <a class="auth-link" href="<?= $e($mailPreviewUrl) ?>" target="_blank" rel="noopener">Mailpit</a>.</span>
    </p>
<?php endif; ?>
