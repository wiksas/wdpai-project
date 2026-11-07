<?php
/**
 * Menu konta zalogowanego użytkownika (natywne <details>, bez JavaScriptu).
 *
 * Wymaga: $currentUser. Opcjonalnie: $userMenuTrigger = 'chip' (awatar + imię + rola) | 'avatar' (sam awatar).
 */
$userMenuTrigger = $userMenuTrigger ?? 'avatar';
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<details class="user-menu user-menu--<?= $escape($userMenuTrigger) ?>">
    <summary class="user-menu__trigger" aria-label="Menu konta: <?= $escape($currentUser['name']) ?>">
        <span class="avatar" aria-hidden="true"><?= $escape($currentUser['initials']) ?></span>
        <?php if ($userMenuTrigger === 'chip'): ?>
            <span class="user-chip__text">
                <span class="user-chip__name"><?= $escape($currentUser['name']) ?></span>
                <span class="user-chip__role"><?= $escape($currentUser['label']) ?></span>
            </span>
            <span class="ms user-chip__chevron" aria-hidden="true">expand_more</span>
        <?php endif; ?>
    </summary>
    <div class="user-menu__panel">
        <div class="user-menu__who">
            <p class="user-menu__name"><?= $escape($currentUser['name']) ?></p>
            <p class="user-menu__email">Login: <?= $escape($currentUser['login']) ?></p>
            <p class="user-menu__email"><?= $escape($currentUser['email']) ?></p>
        </div>
        <a href="/collection" class="user-menu__item">
            <span class="ms" aria-hidden="true">potted_plant</span>
            Moja Kolekcja
        </a>
        <form action="/logout" method="POST">
            <?php include __DIR__ . '/csrf.html.php'; ?>
            <button type="submit" class="user-menu__item user-menu__item--danger">
                <span class="ms" aria-hidden="true">logout</span>
                Wyloguj się
            </button>
        </form>
    </div>
</details>
