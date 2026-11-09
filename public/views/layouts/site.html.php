<?php
/**
 * Układ stron publicznych: nagłówek witryny, treść i stopka.
 *
 * Widok wybiera go przez `$layout = 'site'` i podaje: `$mainClass`, `$pageStyles`, `$pageScripts`
 * (jak w layouts/app.html.php). Treść widoku trafia do `$content`.
 */
$layoutHtml = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pl" data-theme="<?= $layoutHtml($theme ?? 'system') ?>">
<head>
    <?php include __DIR__ . '/../components/head.html.php'; ?>
    <?php foreach (($pageStyles ?? []) as $style): ?>
        <link rel="stylesheet" href="<?= $asset('/css/pages/' . $style . '.css') ?>">
    <?php endforeach; ?>
</head>
<body>
    <?php include __DIR__ . '/../components/site-header.html.php'; ?>

    <main class="<?= $layoutHtml($mainClass ?? '') ?>">
        <?= $content ?>
    </main>

    <?php include __DIR__ . '/../components/site-footer.html.php'; ?>
    <?php include __DIR__ . '/../components/foot.html.php'; ?>
    <?php foreach (($pageScripts ?? []) as $script): ?>
        <script src="<?= $asset('/js/' . $script . '.js') ?>" defer></script>
    <?php endforeach; ?>
</body>
</html>
