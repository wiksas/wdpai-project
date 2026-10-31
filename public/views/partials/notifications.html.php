<?php
/**
 * Dzwonek z panelem powiadomień (natywne <details>, działa bez JavaScriptu; app.js dodaje animacje i zapisywanie bez przeładowania).
 * Wymaga: $notifications ['items' => [...], 'unread' => int], $csrfToken.
 */
$notifItems = $notifications['items'] ?? [];
$notifUnread = (int) ($notifications['unread'] ?? 0);
$notifEscape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<details class="notif" data-notif>
    <summary class="notif__trigger icon-button" aria-label="Powiadomienia<?= $notifUnread > 0 ? ' (' . $notifUnread . ' nowych)' : '' ?>">
        <span class="ms" aria-hidden="true">notifications</span>
        <span class="notif__badge" data-notif-count<?= $notifUnread === 0 ? ' hidden' : '' ?>><?= $notifUnread > 9 ? '9+' : $notifUnread ?></span>
    </summary>

    <div class="notif__panel" role="dialog" aria-label="Powiadomienia">
        <header class="notif__head">
            <h2 class="notif__title">Powiadomienia</h2>
            <form action="/notifications/read" method="POST" data-notif-read-all>
                <?php include __DIR__ . '/csrf.html.php'; ?>
                <input type="hidden" name="_back" value="<?= $notifEscape($_SERVER['REQUEST_URI'] ?? '/collection') ?>">
                <button type="submit" class="notif__all"<?= $notifUnread === 0 ? ' disabled' : '' ?>>Oznacz wszystkie jako przeczytane</button>
            </form>
        </header>

        <?php if ($notifItems === []): ?>
            <p class="notif__empty">Brak powiadomień. Twój ogród jest spokojny.</p>
        <?php else: ?>
            <ul class="notif__list">
                <?php foreach ($notifItems as $item): ?>
                    <li class="notif__item notif__item--<?= $notifEscape($item['tone']) ?><?= $item['read'] ? '' : ' is-unread' ?>" data-notif-item="<?= $notifEscape($item['id']) ?>">
                        <span class="notif__icon" aria-hidden="true"><span class="ms"><?= $notifEscape($item['icon']) ?></span></span>
                        <a class="notif__body" href="<?= $notifEscape($item['href']) ?>">
                            <span class="notif__item-title"><?= $notifEscape($item['title']) ?></span>
                            <span class="notif__text"><?= $notifEscape($item['text']) ?></span>
                            <span class="notif__when"><?= $notifEscape($item['when']) ?></span>
                        </a>
                        <form action="/notifications/read" method="POST" class="notif__read" data-notif-read>
                            <?php include __DIR__ . '/csrf.html.php'; ?>
                            <input type="hidden" name="id" value="<?= $notifEscape($item['id']) ?>">
                            <input type="hidden" name="_back" value="<?= $notifEscape($_SERVER['REQUEST_URI'] ?? '/collection') ?>">
                            <button type="submit" class="notif__dot" aria-label="Oznacz jako przeczytane: <?= $notifEscape($item['title']) ?>"<?= $item['read'] ? ' disabled' : '' ?>></button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <footer class="notif__foot">
            <a href="/schedule" class="notif__link">Otwórz harmonogram <span class="ms" aria-hidden="true">arrow_forward</span></a>
        </footer>
    </div>
</details>
