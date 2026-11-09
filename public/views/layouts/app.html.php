<?php
/**
 * Układ stron aplikacji po zalogowaniu: panel boczny, pasek górny, treść i dolna nawigacja mobilna.
 *
 * Widok wybiera go przez `$layout = 'app'` i podaje: `$mainClass` (klasa elementu <main>),
 * `$pageStyles` (nazwy plików z css/pages bez rozszerzenia), `$pageScripts` (nazwy plików z js bez rozszerzenia).
 * Treść widoku trafia do `$content` (renderuje ją AppController::render()).
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
    <div class="app">
        <?php include __DIR__ . '/../components/sidebar.html.php'; ?>

        <div class="app-main">
            <?php include __DIR__ . '/../components/topbar.html.php'; ?>

            <main class="app-content <?= $layoutHtml($mainClass ?? '') ?>">
                <?= $content ?>
            </main>
        </div>
    </div>

    <?php include __DIR__ . '/../components/bottom-nav.html.php'; ?>
    <?php include __DIR__ . '/../components/foot.html.php'; ?>
    <?php foreach (($pageScripts ?? []) as $script): ?>
        <script src="<?= $asset('/js/' . $script . '.js') ?>" defer></script>
    <?php endforeach; ?>
</body>
</html>
