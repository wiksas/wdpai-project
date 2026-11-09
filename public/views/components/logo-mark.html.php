<?php
/**
 * Znak KOMOREBI: 木漏れ日 (światło między liśćmi) na bonsai w moyogi. Słońce zagląda między górną i prawą koronę,
 * promienie widać tylko w prześwitach (korony i pień je zasłaniają), a liście przy słońcu jaśnieją jak pod światło.
 * Barwy to tokeny rysunków (`--c-bark`, `--c-needle*`, `--c-pot`, `--c-sun`), więc znak zmienia się razem z motywem;
 * reguły `.logo-*` leżą w sekcji 11 style.css. Opcjonalnie: `$logoClass` (klasa kafelka).
 */
// Znak bywa kilka razy na stronie (np. pasek górny i panel boczny), a przycięcie ukrytego egzemplarza nie działa.
$logoClip = 'logo-crowns-' . bin2hex(random_bytes(4));
$logoCrowns = '<path d="M13.5 13C14 7 19.5 4.4 25.2 4.4C31 4.4 36 7 36.6 13C29 15.2 21 15.2 13.5 13Z"/>'
    . '<path d="M26.8 23.6C27 19.6 31.4 17.4 35.8 17.4C40.2 17.4 44.4 19.6 44.6 23.6C38.6 25.2 32.8 25.2 26.8 23.6Z"/>'
    . '<path d="M3.4 29.8C3.6 25.6 7.6 23.4 11.6 23.4C15.6 23.4 19.6 25.6 20.2 29.8C14.4 31.6 9 31.6 3.4 29.8Z"/>';
?>
<span class="brand__mark<?= isset($logoClass) ? ' ' . htmlspecialchars((string) $logoClass, ENT_QUOTES, 'UTF-8') : '' ?>">
    <svg viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false" class="brand__glyph">
        <clipPath id="<?= $logoClip ?>"><?= $logoCrowns ?></clipPath>
        <path class="logo-ray" d="M35.3 16.4L11 26.7L9.9 23.8L35.2 16.1ZM35 15.3L12.7 16.9L12.6 14.9L35 15ZM38.3 11.4L37.2 -2.9L38.8 -3L38.6 11.4ZM40.2 11.8L46.4 -1.2L47.7 -0.6L40.4 11.9ZM41.5 12.8L51.4 5.4L52.3 6.7L41.7 13.1ZM42.2 14.6L50.5 13.7L50.6 14.6L42.2 14.9ZM42 16.3L49.8 19.4L49.3 20.4L41.8 16.6Z"/>
        <circle class="logo-sun" cx="38.6" cy="15" r="2.6"/>
        <path class="logo-bark" d="M20.6 36.6C20.6 32.6 15.6 31.6 17.3 26.6C18.7 22 25 22.6 24.6 17.6C24.4 15 24.6 13 25.4 11H27.8C27.6 13 27.8 15 28.4 17.4C29.6 23.6 23 24.6 22.4 27.6C21.8 30.6 26.8 32.6 26.8 36.6Z"/>
        <g class="logo-crown"><?= $logoCrowns ?></g>
        <circle class="logo-sunlit" clip-path="url(#<?= $logoClip ?>)" cx="38.6" cy="15" r="7.4"/>
        <path class="logo-pot" d="M9.4 36.4H38.6V39.2H36.8L35.4 44.4H12.6L11.2 39.2H9.4Z"/>
    </svg>
</span>
<?php unset($logoClass, $logoClip, $logoCrowns); ?>
