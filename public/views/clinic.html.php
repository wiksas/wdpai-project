<!DOCTYPE html>
<html lang="pl" data-theme="<?= htmlspecialchars((string) ($theme ?? 'system'), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/partials/head.html.php'; ?>
    <link rel="stylesheet" href="<?= $asset('/css/pages/clinic.css') ?>">
</head>
<body>
<?php
$clinicGuide = [
    [
        'category' => 'conifer',
        'image' => '/images/pine-needles-soil.jpg',
        'alt' => 'Zbliżenie igieł sosny nad mineralnym podłożem',
        'species' => 'Sosna czarna',
        'romaji' => 'Kuromatsu',
        'kanji' => '黒松',
        'problem' => 'Żółknące igły',
        'text' => 'Jesienią sosna zrzuca 2–3-letnie igły z wnętrza korony i to jest normalne. Żółknące świeże wierzchołki wskazują raczej na chlorozę albo przesuszenie: sprawdź wilgotność bryły i odczyn podłoża.',
    ],
    [
        'category' => 'deciduous',
        'image' => '/images/garden-red-maple-lantern.jpg',
        'alt' => 'Klon palmowy z czerwonymi liśćmi w ogrodzie z kamienną latarnią',
        'species' => 'Klon palmowy',
        'romaji' => 'Momiji',
        'kanji' => '紅葉',
        'problem' => 'Zasychające brzegi liści',
        'text' => 'Wiatr i ostre popołudniowe słońce przypalają cienkie liście klonu. Latem ustaw drzewko w półcieniu, osłoń je od wiatru i podlewaj rano, nie w pełnym słońcu.',
    ],
    [
        'category' => 'conifer',
        'image' => '/images/bonsai-white-room.jpg',
        'alt' => 'Jałowiec z jasnym drewnem jin na białym tle',
        'species' => 'Jałowiec chiński',
        'romaji' => 'Shimpaku',
        'kanji' => '真柏',
        'problem' => 'Przędziorki',
        'text' => 'Szarawy nalot i delikatna pajęczynka między łuskami to zwykle przędziorki. Potrząśnij gałązką nad białą kartką, spłucz koronę strumieniem wody i powtórz oprysk olejkiem neem po 7 dniach.',
    ],
    [
        'category' => 'deciduous',
        'image' => '/images/bonsai-wooden-table.jpg',
        'alt' => 'Wiąz drobnolistny na drewnianym stole przy oknie',
        'species' => 'Wiąz drobnolistny',
        'romaji' => '',
        'kanji' => '',
        'problem' => 'Gnijące korzenie',
        'text' => 'Wiąz źle znosi stale mokre, zbite podłoże. Czarne, gąbczaste korzenie usuń przy przesadzaniu, a podłoże wymień na gruboziarniste (akadama z kiryu).',
    ],
];
$clinicFilters = ['all' => 'Wszystkie', 'conifer' => 'Iglaste', 'deciduous' => 'Liściaste'];
$clinicProtocol = [
    ['title' => 'Kąpiel zanurzeniowa (15–20 min)', 'text' => 'Zanurz donicę po krawędź w miękkiej, odstanej wodzie lub deszczówce i trzymaj, aż z podłoża przestaną wypływać bąbelki powietrza.'],
    ['title' => 'Półcień przez 5–7 dni', 'text' => 'Przenieś drzewko w miejsce osłonięte od słońca i wiatru. Nie przycinaj i nie drutuj osłabionego okazu.'],
    ['title' => 'Przerwa w nawożeniu (14 dni)', 'text' => 'Wstrzymaj nawozy, zwłaszcza azotowe, do pojawienia się nowych przyrostów. Wilgotność bryły sprawdzaj rano i wieczorem.'],
];
$clinicFaq = [
    'Jak sprawdzić, czy gałązka jeszcze żyje pod korą?',
    'Kiedy bezpiecznie podlać po kąpieli zanurzeniowej?',
    'Czy usuwać zżółknięte igły z zeszłego roku?',
    'Jak dawkować chelat żelaza przy chlorozie?',
];
?>
    <div class="app">
        <?php include __DIR__ . '/partials/app-sidebar.html.php'; ?>
        <div class="app-main">
            <?php include __DIR__ . '/partials/app-topbar.html.php'; ?>
            <main class="app-content clinic">

                <header class="clinic-head">
                    <p class="eyebrow">Klinika</p>
                    <h1 class="clinic-head__title">Co dolega drzewku?</h1>
                    <p class="clinic-head__lead">Wgraj zdjęcie drzewka: model wizyjny przygląda się mu i podpowiada, czy drzewko usycha, czy jest przelane albo ma inny kłopot, oraz co zrobić. Wynik trafia do historii. Pytania o objawy i zabiegi zadasz konsultantowi, który odpowiada z bazy porad pielęgnacyjnych.</p>
                </header>

                <div class="clinic-work">
                    <section class="clinic-card clinic-scanner" aria-labelledby="clinic-scanner-title">
                        <h2 class="clinic-card__title" id="clinic-scanner-title">Skaner zdjęcia</h2>
                        <p class="clinic-card__note">Model ocenia to, co widać na zdjęciu, i podaje pewność wyniku. To podpowiedź, nie diagnoza: przy niskiej pewności sprawdź podłoże palcem.</p>

                        <form class="clinic-scanner__form" action="/clinic/scans" method="post" enctype="multipart/form-data" data-scan-form>
                            <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                            <fieldset class="clinic-parts">
                                <legend class="clinic-parts__legend">Co jest na zdjęciu?</legend>
                                <div class="clinic-parts__grid">
                                    <label class="clinic-part">
                                        <input class="clinic-part__input" type="radio" name="part" value="leaves" checked>
                                        <span class="clinic-part__name">Liście i igły</span>
                                    </label>
                                    <label class="clinic-part">
                                        <input class="clinic-part__input" type="radio" name="part" value="bark">
                                        <span class="clinic-part__name">Kora i pień</span>
                                    </label>
                                    <label class="clinic-part">
                                        <input class="clinic-part__input" type="radio" name="part" value="roots">
                                        <span class="clinic-part__name">Podłoże i korzenie</span>
                                    </label>
                                    <label class="clinic-part">
                                        <input class="clinic-part__input" type="radio" name="part" value="wiring">
                                        <span class="clinic-part__name">Drutowanie</span>
                                    </label>
                                </div>
                            </fieldset>

                            <div class="clinic-upload">
                                <input class="visually-hidden" type="file" id="clinic-photo" name="photo" accept="image/jpeg,image/png,image/webp" data-scan-input required>
                                <label class="clinic-upload__zone" for="clinic-photo" data-dropzone>
                                    <span class="ms clinic-upload__icon" aria-hidden="true">add_photo_alternate</span>
                                    <span class="clinic-upload__title">Wybierz zdjęcie albo upuść je tutaj</span>
                                    <span class="clinic-upload__text">Światło dzienne, ostre zbliżenie. Przy pytaniu o podlewanie złap w kadr liście lub igły razem z wierzchem podłoża. JPG, PNG lub WebP; duże zdjęcia zmniejszamy przed wysłaniem.</span>
                                </label>

                                <figure class="clinic-preview" data-scan-preview hidden>
                                    <div class="clinic-preview__frame">
                                        <img class="clinic-preview__img" data-scan-preview-img alt="Podgląd wybranego zdjęcia" width="512" height="512">
                                        <span class="clinic-preview__scanline" aria-hidden="true"></span>
                                    </div>
                                    <figcaption class="clinic-preview__caption" data-scan-summary aria-live="polite"></figcaption>
                                </figure>

                                <section class="clinic-result" data-scan-result aria-labelledby="clinic-result-cause" hidden>
                                    <p class="clinic-result__confidence" data-result-confidence></p>
                                    <h3 class="clinic-result__cause" id="clinic-result-cause" data-result-cause></h3>
                                    <p class="clinic-result__summary" data-result-summary></p>
                                    <div class="clinic-result__cols">
                                        <div>
                                            <h4 class="clinic-result__heading">Co widać na zdjęciu</h4>
                                            <ul class="clinic-result__list" data-result-observations></ul>
                                        </div>
                                        <div>
                                            <h4 class="clinic-result__heading">Co zrobić</h4>
                                            <ol class="clinic-result__list clinic-result__list--steps" data-result-actions></ol>
                                        </div>
                                    </div>
                                    <p class="clinic-result__verify" data-result-verify-row><strong>Jak to sprawdzić:</strong> <span data-result-verify></span></p>
                                    <button type="button" class="btn btn--tonal clinic-result__schedule" data-result-schedule>
                                        <span class="ms" aria-hidden="true">event_available</span>
                                        <span data-result-schedule-label>Dodaj kroki do harmonogramu</span>
                                    </button>
                                </section>

                                <input type="hidden" name="level" value="watch" data-scan-level>
                                <input type="hidden" name="vitality" value="0" data-scan-vitality>
                                <input type="hidden" name="title" value="" data-scan-title>
                                <input type="hidden" name="analysis" value="" data-scan-analysis>
                                <button type="submit" class="btn btn--primary clinic-upload__save">
                                    <span class="ms" aria-hidden="true">save</span>
                                    Zapisz skan
                                </button>
                            </div>
                        </form>
                    </section>

                    <section class="clinic-card clinic-history" aria-labelledby="clinic-history-title">
                        <header class="clinic-history__head">
                            <h2 class="clinic-card__title" id="clinic-history-title">Twoje skany</h2>
                            <p class="clinic-history__count" data-scans-count><?= $scanCount > 5 ? '5 najnowszych z ' . (int) $scanCount : 'Zapisane: ' . (int) $scanCount ?></p>
                        </header>
                        <?php include __DIR__ . '/partials/clinic-scans.html.php'; ?>
                    </section>
                </div>

                <section class="clinic-consult" id="clinic-consultant" aria-labelledby="clinic-chat-title">
                    <div class="clinic-consult__intro">
                        <h2 class="clinic-consult__title" id="clinic-chat-title">Konsultant</h2>
                        <p class="clinic-consult__lead">Odpowiada model językowy, który zna Twoją kolekcję i ostatni skan, pamięta rozmowę (zapisuje się na koncie) i opiera się na poradach o podlewaniu, przesadzaniu, szkodnikach, drucie i zimowaniu. To podpowiedź, nie diagnoza. Gdy model jest niedostępny, odpowiada gotowa porada z bazy.</p>
                        <p class="clinic-consult__faq-title" id="clinic-faq-title">Częste pytania</p>
                        <ul class="clinic-consult__faq" aria-labelledby="clinic-faq-title">
                            <?php foreach ($clinicFaq as $faq): ?>
                                <li>
                                    <button type="submit" form="clinic-chat-form" class="clinic-consult__question" name="faq" value="<?= htmlspecialchars($faq, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($faq, ENT_QUOTES, 'UTF-8') ?></button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="clinic-card clinic-chat">
                        <div class="clinic-chat__thread" data-chat-thread aria-live="polite">
                            <div class="clinic-chat__row">
                                <div class="clinic-chat__message">
                                    <p class="clinic-chat__meta">Konsultant</p>
                                    <p class="clinic-chat__bubble">Opisz, co się zmieniło i na której części drzewka, albo zapytaj o konkretny zabieg, np. „kiedy przesadzać sosnę?”.</p>
                                </div>
                            </div>
                            <?php foreach ($chat as $message): ?>
                                <?php $isUser = $message['role'] === 'user'; ?>
                                <div class="clinic-chat__row<?= $isUser ? ' clinic-chat__row--user' : '' ?>">
                                    <div class="clinic-chat__message">
                                        <p class="clinic-chat__meta"><?= $isUser ? 'Ty' : 'Konsultant' ?>, <?= htmlspecialchars(\App\Support\PolishDate::now()->setTimestamp((int) $message['at'])->format('H:i'), ENT_QUOTES, 'UTF-8') ?></p>
                                        <p class="clinic-chat__bubble"><?= htmlspecialchars((string) $message['text'], ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <form class="clinic-chat__form" id="clinic-chat-form" action="/clinic/ask" method="post" data-chat-form>
                            <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                            <label class="clinic-chat__label" for="clinic-question">Twoje pytanie</label>
                            <div class="clinic-chat__compose">
                                <input class="clinic-chat__input" type="text" id="clinic-question" name="q" minlength="3" maxlength="300" autocomplete="off" placeholder="Np. jak dawkować chelat żelaza?">
                                <button type="submit" class="clinic-chat__send" aria-label="Wyślij pytanie">
                                    <span class="ms" aria-hidden="true">send</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <section class="clinic-protocol" id="clinic-protocol" aria-labelledby="clinic-protocol-title">
                    <div class="clinic-protocol__intro">
                        <h2 class="clinic-protocol__title" id="clinic-protocol-title">Protokół ratunkowy przy przesuszeniu</h2>
                        <p class="clinic-protocol__lead">Na wypadek, gdy podłoże wyschło na całej głębokości, a igły lub liście straciły jędrność. Kroki trafią do harmonogramu na dziś jako zadania do odhaczenia.</p>
                    </div>
                    <ol class="clinic-protocol__steps">
                        <?php foreach ($clinicProtocol as $index => $step): ?>
                            <li class="clinic-protocol__step">
                                <span class="clinic-protocol__num" aria-hidden="true"><?= $index + 1 ?></span>
                                <h3 class="clinic-protocol__step-title"><?= htmlspecialchars($step['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="clinic-protocol__step-text"><?= htmlspecialchars($step['text'], ENT_QUOTES, 'UTF-8') ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                    <form class="clinic-protocol__form" action="/clinic/protocol" method="post" data-protocol-form>
                        <?php include __DIR__ . '/partials/csrf.html.php'; ?>
                        <?php foreach ($clinicProtocol as $step): ?>
                            <input type="hidden" name="steps[]" value="<?= htmlspecialchars($step['title'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php endforeach; ?>
                        <button type="submit" class="btn clinic-protocol__button">
                            <span class="ms" aria-hidden="true">event_available</span>
                            <span data-protocol-label>Dodaj <?= count($clinicProtocol) ?> kroki do harmonogramu</span>
                        </button>
                    </form>
                </section>

                <section class="clinic-guide" aria-labelledby="clinic-guide-title">
                    <header class="clinic-guide__head">
                        <div>
                            <h2 class="clinic-guide__title" id="clinic-guide-title">Typowe kłopoty według gatunku</h2>
                            <p class="clinic-guide__lead">Problemy, które najczęściej spotykają popularne gatunki bonsai, i pierwszy krok zaradczy.</p>
                        </div>
                        <nav class="clinic-guide__filters" aria-label="Filtr gatunków">
                            <?php foreach ($clinicFilters as $value => $label): ?>
                                <a href="<?= $value === 'all' ? '/clinic' : '/clinic?species=' . $value ?>#clinic-guide-title" class="clinic-guide__filter<?= $archiveFilter === $value ? ' is-active' : '' ?>" data-archive-filter="<?= $value ?>"<?= $archiveFilter === $value ? ' aria-current="true"' : '' ?>><?= $label ?></a>
                            <?php endforeach; ?>
                        </nav>
                    </header>
                    <ul class="clinic-guide__list">
                        <?php foreach ($clinicGuide as $item): ?>
                            <li class="clinic-case" data-category="<?= $item['category'] ?>"<?= $archiveFilter !== 'all' && $archiveFilter !== $item['category'] ? ' hidden' : '' ?>>
                                <img class="clinic-case__img" src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['alt'], ENT_QUOTES, 'UTF-8') ?>" width="512" height="279" loading="lazy">
                                <div class="clinic-case__body">
                                    <p class="clinic-case__species">
                                        <?= htmlspecialchars($item['species'], ENT_QUOTES, 'UTF-8') ?>
                                        <?php if ($item['romaji'] !== ''): ?>
                                            <span class="term"><?= $item['romaji'] ?> <span lang="ja"><?= $item['kanji'] ?></span></span>
                                        <?php endif; ?>
                                    </p>
                                    <h3 class="clinic-case__title"><?= htmlspecialchars($item['problem'], ENT_QUOTES, 'UTF-8') ?></h3>
                                    <p class="clinic-case__text"><?= htmlspecialchars($item['text'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/partials/app-bottom-nav.html.php'; ?>
    <?php include __DIR__ . '/partials/foot.html.php'; ?>
    <script src="<?= $asset('/js/clinic.js') ?>" defer></script>
</body>
</html>
