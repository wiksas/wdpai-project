<?php
/**
 * Układ logowania, rejestracji i potwierdzania e-maila: panel z hasłem marki obok karty z formularzem.
 *
 * Widok wybiera go przez `$layout = 'auth'`. Treść widoku trafia do `$content` (wnętrze karty).
 */
$layoutHtml = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pl" data-theme="<?= $layoutHtml($theme ?? 'system') ?>">
<head>
    <?php include __DIR__ . '/../components/head.html.php'; ?>
    <link rel="stylesheet" href="<?= $asset('/css/pages/auth.css') ?>">
</head>
<body class="auth-page">
    <div class="auth">
        <?php include __DIR__ . '/../components/auth-panel.html.php'; ?>

        <main class="auth-card">
            <?= $content ?>
        </main>
    </div>
    <?php include __DIR__ . '/../components/foot.html.php'; ?>
</body>
</html>
