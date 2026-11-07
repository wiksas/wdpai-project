<?php
// Adres zasobu z wersją (czas modyfikacji pliku): przeglądarka pobiera CSS ponownie po każdej zmianie.
$asset = static function (string $path): string {
    $file = dirname(__DIR__, 2) . $path;

    return $path . (is_file($file) ? '?v=' . filemtime($file) : '');
};
?>
<meta charset="UTF-8">
<meta name="color-scheme" content="<?= in_array($theme ?? 'system', ['light', 'dark'], true) ? $theme : 'light dark' ?>">
<meta name="csrf-token" content="<?= htmlspecialchars((string) ($csrfToken ?? ''), ENT_QUOTES, 'UTF-8') ?>">
<script>document.documentElement.classList.add('js');</script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars((string) ($pageTitle ?? 'KOMOREBI'), ENT_QUOTES, 'UTF-8') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Zen+Kaku+Gothic+New:wght@400;500;700&family=Zen+Old+Mincho:wght@400;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..24,400,0..1,0&display=block" rel="stylesheet">
<link rel="stylesheet" href="<?= $asset('/css/style.css') ?>">
