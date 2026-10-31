<?php
/**
 * Koniec <body>: kontener toastów (komunikaty flash po przekierowaniu) i skrypt aplikacji.
 * Wymaga: $flashes (lista ['type', 'message']).
 */
$footAsset = $asset ?? static fn (string $path): string => $path;
?>
<div class="toasts" data-toasts role="region" aria-label="Komunikaty" aria-live="polite">
    <?php foreach (($flashes ?? []) as $flash): ?>
        <div class="toast toast--<?= htmlspecialchars((string) $flash['type'], ENT_QUOTES, 'UTF-8') ?>" role="status">
            <span class="ms" aria-hidden="true"><?= $flash['type'] === 'error' ? 'error' : ($flash['type'] === 'info' ? 'info' : 'check_circle') ?></span>
            <span class="toast__text"><?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    <?php endforeach; ?>
</div>
<script src="<?= $footAsset('/js/app.js') ?>" defer></script>
