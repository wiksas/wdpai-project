<?php
/**
 * Przełącznik motywu. Bez JS: POST /theme zapisuje ciasteczko. Z JS (app.js): `theme` liczony z motywu widocznego teraz.
 * Wymaga: $theme (light|dark|system), $currentPath.
 */
$isDark = ($theme ?? 'system') === 'dark';
?>
<form class="theme-toggle" action="/theme" method="POST" data-theme-form>
    <?php include __DIR__ . '/../partials/csrf.html.php'; ?>
    <input type="hidden" name="_back" value="<?= htmlspecialchars((string) ($currentPath ?? '/'), ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit" class="icon-button theme-toggle__button" name="theme" value="<?= $isDark ? 'light' : 'dark' ?>" aria-pressed="<?= $isDark ? 'true' : 'false' ?>" aria-label="Ciemny motyw" title="Ciemny motyw">
        <span class="ms theme-toggle__icon" aria-hidden="true"><?= $isDark ? 'light_mode' : 'dark_mode' ?></span>
    </button>
</form>
