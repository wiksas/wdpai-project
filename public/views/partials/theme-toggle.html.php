<?php
/**
 * Przełącznik motywu jasny/ciemny. Bez JS: formularz POST /theme zapisuje ciasteczko i wraca na stronę.
 * Z JS (app.js): wartość `theme` liczona z motywu widocznego teraz (także systemowego), zmiana bez przeładowania.
 *
 * Wymaga: $theme (light|dark|system), $currentPath.
 */
$isDark = ($theme ?? 'system') === 'dark';
?>
<form class="theme-toggle" action="/theme" method="POST" data-theme-form>
    <?php include __DIR__ . '/csrf.html.php'; ?>
    <input type="hidden" name="_back" value="<?= htmlspecialchars((string) ($currentPath ?? '/'), ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit" class="icon-button theme-toggle__button" name="theme" value="<?= $isDark ? 'light' : 'dark' ?>" aria-pressed="<?= $isDark ? 'true' : 'false' ?>" aria-label="Ciemny motyw" title="Ciemny motyw">
        <span class="ms theme-toggle__icon" aria-hidden="true"><?= $isDark ? 'light_mode' : 'dark_mode' ?></span>
    </button>
</form>
