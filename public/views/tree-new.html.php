<?php
// Układ: layouts/app.html.php
$layout = 'app';
$mainClass = 'treeform';
$pageStyles = ['tree-form'];
$pageScripts = ['tree-new'];
$topbarBack = ['href' => '/collection', 'title' => 'Nowe drzewko'];

/**
 * @var array<string, string> $errors  komunikaty błędów pól (klucz = nazwa pola)
 * @var array<string, string> $old     wpisane wartości
 * @var array<string, string> $styles  styl => opis
 */
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$errorTargets = [
    'name' => ['Nazwa', 'tree-name'],
    'commonName' => ['Gatunek', 'tree-common'],
    'species' => ['Nazwa łacińska', 'tree-species'],
    'category' => ['Rodzaj', 'tree-category'],
    'style' => ['Styl', 'tree-style'],
    'age' => ['Wiek', 'tree-age'],
    'pot' => ['Donica', 'tree-pot'],
    'photo' => ['Zdjęcie', 'tree-photo'],
];
$fieldError = static fn (string $field, string $id): string => isset($errors[$field])
    ? '<p class="treeform-error" id="' . $id . '-error"><span class="ms" aria-hidden="true">error</span>' . $e($errors[$field]) . '</p>'
    : '';
$invalid = static fn (string $field, string $id): string => isset($errors[$field]) ? ' aria-invalid="true" aria-describedby="' . $id . '-error"' : '';
?>
<a href="/collection" class="treeform-back">
    <span class="ms" aria-hidden="true">arrow_back</span>
    Wróć do kolekcji
</a>

<header class="treeform-head">
    <h1 class="treeform-head__title">Dodaj drzewko do kolekcji</h1>
    <p class="treeform-head__lead">Podaj, co wiesz o okazie. Karta okazu zacznie od wartości startowych (wilgotność 60%), a zapisy podlania i zdjęcia w skanerze Kliniki uzupełnią resztę.</p>
</header>

<form class="treeform-form" action="/collection" method="POST" enctype="multipart/form-data" novalidate>
    <?php include __DIR__ . '/partials/csrf.html.php'; ?>

    <?php if ($errors !== []): ?>
        <div class="treeform-errors" role="alert" tabindex="-1" data-error-summary>
            <p class="treeform-errors__title">Drzewko nie zostało dodane. Popraw <?= count($errors) === 1 ? 'jedno pole' : count($errors) . ' pola' ?>:</p>
            <ul>
                <?php foreach ($errorTargets as $field => [$label, $target]): ?>
                    <?php if (isset($errors[$field])): ?>
                        <li><a href="#<?= $target ?>"><?= $e($label) ?>:</a> <?= $e($errors[$field]) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <?php if (isset($errors['photo'])): ?>
                <p class="treeform-errors__note">Zdjęcia nie da się zachować w formularzu: wybierz je ponownie albo dodaj okaz bez zdjęcia.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="treeform-row">
        <div class="field">
            <label class="field__label" for="tree-name">Nazwa drzewka</label>
            <input class="input" type="text" id="tree-name" name="name" value="<?= $e($old['name']) ?>" maxlength="40" required placeholder="np. Matsu"<?= $invalid('name', 'tree-name') ?>>
            <?= $fieldError('name', 'tree-name') ?>
        </div>
        <div class="field">
            <label class="field__label" for="tree-age">Przybliżony wiek (lata)</label>
            <input class="input" type="number" id="tree-age" name="age" value="<?= $e($old['age']) ?>" min="1" max="500" inputmode="numeric" required<?= $invalid('age', 'tree-age') ?>>
            <?= $fieldError('age', 'tree-age') ?>
        </div>
    </div>

    <div class="treeform-row">
        <div class="field">
            <label class="field__label" for="tree-common">Gatunek</label>
            <input class="input" type="text" id="tree-common" name="commonName" value="<?= $e($old['commonName']) ?>" maxlength="60" required placeholder="np. Jałowiec chiński"<?= $invalid('commonName', 'tree-common') ?>>
            <?= $fieldError('commonName', 'tree-common') ?>
        </div>
        <div class="field">
            <label class="field__label" for="tree-species">Nazwa łacińska <span class="treeform-optional">(opcjonalnie)</span></label>
            <input class="input" type="text" id="tree-species" name="species" value="<?= $e($old['species']) ?>" maxlength="60" placeholder="np. Juniperus chinensis"<?= $invalid('species', 'tree-species') ?>>
            <?= $fieldError('species', 'tree-species') ?>
        </div>
    </div>

    <fieldset class="treeform-choices" id="tree-category">
        <legend class="field__label">Rodzaj</legend>
        <label class="treeform-choice">
            <input class="visually-hidden" type="radio" name="category" value="conifer"<?= $old['category'] === 'conifer' ? ' checked' : '' ?>>
            <span class="treeform-choice__name">Iglaste</span>
            <span class="treeform-choice__text">sosna, jałowiec, cis</span>
        </label>
        <label class="treeform-choice">
            <input class="visually-hidden" type="radio" name="category" value="deciduous"<?= $old['category'] === 'deciduous' ? ' checked' : '' ?>>
            <span class="treeform-choice__name">Liściaste</span>
            <span class="treeform-choice__text">klon, wiąz, zelkowa</span>
        </label>
        <?= $fieldError('category', 'tree-category') ?>
    </fieldset>

    <div class="treeform-row">
        <div class="field">
            <label class="field__label" for="tree-style">Styl</label>
            <select class="select" id="tree-style" name="style"<?= $invalid('style', 'tree-style') ?>>
                <?php foreach ($styles as $name => $description): ?>
                    <option value="<?= $e($name) ?>"<?= $old['style'] === $name ? ' selected' : '' ?>><?= $e($name) ?>: <?= $e(mb_strtolower($description)) ?></option>
                <?php endforeach; ?>
            </select>
            <?= $fieldError('style', 'tree-style') ?>
        </div>
        <div class="field">
            <label class="field__label" for="tree-pot">Donica <span class="treeform-optional">(opcjonalnie)</span></label>
            <input class="input" type="text" id="tree-pot" name="pot" value="<?= $e($old['pot']) ?>" maxlength="40" placeholder="np. Tokoname"<?= $invalid('pot', 'tree-pot') ?>>
            <?= $fieldError('pot', 'tree-pot') ?>
        </div>
    </div>

    <div class="field">
        <label class="field__label" for="tree-photo">Zdjęcie <span class="treeform-optional">(opcjonalnie)</span></label>
        <input class="input treeform-file" type="file" id="tree-photo" name="photo" accept="image/jpeg,image/png,image/webp" aria-describedby="tree-photo-hint<?= isset($errors['photo']) ? ' tree-photo-error' : '' ?>"<?= isset($errors['photo']) ? ' aria-invalid="true"' : '' ?> data-tree-photo>
        <p class="treeform-hint" id="tree-photo-hint">JPG, PNG lub WebP. Duże zdjęcia zmniejszamy przed wysłaniem (limit 2 MB). Bez zdjęcia okaz dostanie zdjęcie zastępcze.</p>
        <?= $fieldError('photo', 'tree-photo') ?>
    </div>

    <div class="treeform-actions">
        <button type="submit" class="btn btn--primary">Dodaj do kolekcji</button>
        <a href="/collection" class="btn btn--ghost">Anuluj</a>
    </div>
</form>
