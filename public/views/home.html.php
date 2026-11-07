<!DOCTYPE html>
<html lang="pl" data-theme="<?= htmlspecialchars((string) ($theme ?? 'system'), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/partials/head.html.php'; ?>
    <link rel="stylesheet" href="<?= $asset('/css/pages/home.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/partials/site-header.html.php'; ?>

    <main class="home container">
        <section class="home-hero" aria-labelledby="home-hero-title">
            <div class="home-hero__content">
                <h1 class="home-hero__title" id="home-hero-title">Bonsai rośnie latami. <em>Twoje notatki</em> powinny nadążać.</h1>
                <p class="home-hero__lead">KOMOREBI prowadzi kartę każdego drzewka z zabiegami, zdjęciami i dziennikiem, układa kalendarz podlewania, nawożenia i formowania, a gdy liście więdną, w Klinice podpowiada, od czego zacząć.</p>
                <div class="home-hero__actions">
                    <?php if ($currentUser === null): ?>
                        <a href="/register" class="btn btn--primary btn--lg">Załóż konto</a>
                    <?php else: ?>
                        <a href="/collection" class="btn btn--primary btn--lg">Otwórz kolekcję</a>
                    <?php endif; ?>
                    <a href="#ocena-ryzyka" class="btn btn--secondary btn--lg">Sprawdź ryzyko</a>
                </div>

                <nav class="home-tools" aria-labelledby="home-tools-title">
                    <p class="home-tools__title" id="home-tools-title"><?= $currentUser === null ? 'Po zalogowaniu' : 'Twoje narzędzia' ?></p>
                    <ul class="home-tools__list">
                        <li><a href="/collection" class="home-tools__link"><strong>Kolekcja</strong><span>karta okazu i historia zabiegów</span></a></li>
                        <li><a href="/schedule" class="home-tools__link"><strong>Harmonogram</strong><span>zadania na dziś i na kolejne dni</span></a></li>
                        <li><a href="/clinic" class="home-tools__link"><strong>Klinika</strong><span>objawy, przyczyny, pierwsze kroki</span></a></li>
                        <li><a href="/studio" class="home-tools__link"><strong>Studio</strong><span>plan cięcia i drutowania</span></a></li>
                    </ul>
                </nav>
            </div>

            <figure class="home-hero__media">
                <img src="/images/garden-pine-stand.jpg" alt="Sosna czarna bonsai w płytkiej donicy na drewnianym podeście w japońskim ogrodzie" width="512" height="279" fetchpriority="high">
            </figure>
        </section>

        <section class="home-section home-risk" id="ocena-ryzyka" aria-labelledby="home-risk-title">
            <header class="home-section__head home-section__head--split">
                <div>
                    <p class="eyebrow">Ocena ryzyka</p>
                    <h2 class="home-section__title" id="home-risk-title">Co najbardziej szkodzi Twojemu drzewku?</h2>
                </div>
                <p class="home-section__aside">Wybierz gatunek, podłoże, sposób podlewania i stanowisko. Wynik to suma punktów za każdy z czterech czynników: szacunek do porównywania ustawień, a nie diagnoza.</p>
            </header>

            <div class="home-risk__panel">
                <form class="home-risk__form" id="home-risk-form" action="/clinic" method="GET" data-risk-form>
                    <fieldset class="home-risk__group">
                        <legend class="home-risk__label">Gatunek</legend>
                        <div class="home-risk__choices">
                            <label class="home-choice">
                                <input type="radio" name="species" value="pinus-thunbergii" checked>
                                <span class="home-choice__body">
                                    <span class="home-choice__title">Sosna czarna</span>
                                    <span class="home-choice__hint home-choice__hint--latin">Pinus thunbergii</span>
                                </span>
                            </label>
                            <label class="home-choice">
                                <input type="radio" name="species" value="ulmus-parvifolia">
                                <span class="home-choice__body">
                                    <span class="home-choice__title">Wiąz drobnolistny</span>
                                    <span class="home-choice__hint home-choice__hint--latin">Ulmus parvifolia</span>
                                </span>
                            </label>
                            <label class="home-choice">
                                <input type="radio" name="species" value="ficus-retusa">
                                <span class="home-choice__body">
                                    <span class="home-choice__title">Fikus</span>
                                    <span class="home-choice__hint home-choice__hint--latin">Ficus retusa, ginseng</span>
                                </span>
                            </label>
                        </div>
                    </fieldset>

                    <fieldset class="home-risk__group">
                        <legend class="home-risk__label">Podłoże</legend>
                        <div class="home-risk__choices">
                            <label class="home-choice">
                                <input type="radio" name="substrate" value="akadama" checked>
                                <span class="home-choice__body">
                                    <span class="home-choice__title">Sama akadama</span>
                                    <span class="home-choice__hint">Trzyma wodę, z czasem się kruszy</span>
                                </span>
                            </label>
                            <label class="home-choice">
                                <input type="radio" name="substrate" value="akadama-kiryu">
                                <span class="home-choice__body">
                                    <span class="home-choice__title">Akadama i kiryu (7:3)</span>
                                    <span class="home-choice__hint">Zrównoważony drenaż</span>
                                </span>
                            </label>
                            <label class="home-choice">
                                <input type="radio" name="substrate" value="universal">
                                <span class="home-choice__body">
                                    <span class="home-choice__title">Ziemia uniwersalna</span>
                                    <span class="home-choice__hint">Zbija się i dusi korzenie</span>
                                </span>
                            </label>
                        </div>
                    </fieldset>

                    <div class="home-risk__selects">
                        <div class="home-risk__field">
                            <label class="home-risk__label" for="home-watering">Podlewanie</label>
                            <select class="home-risk__select" id="home-watering" name="watering">
                                <option value="weekly" selected>Raz w tygodniu, według kalendarza</option>
                                <option value="dry-top">Gdy wierzchnia warstwa przeschnie</option>
                                <option value="daily">Codziennie</option>
                            </select>
                        </div>
                        <div class="home-risk__field">
                            <label class="home-risk__label" for="home-light">Stanowisko</label>
                            <select class="home-risk__select" id="home-light" name="light">
                                <option value="south" selected>Jasne miejsce za szybą od południa</option>
                                <option value="east">Parapet od wschodu</option>
                                <option value="terrace">Taras w półcieniu</option>
                            </select>
                        </div>
                    </div>
                </form>

                <aside class="home-result" data-level="mid" aria-labelledby="home-result-title">
                    <div class="home-result__head">
                        <h3 class="home-result__title" id="home-result-title">Wynik</h3>
                        <span class="home-result__badge" data-risk-badge>Umiarkowane ryzyko</span>
                    </div>
                    <p class="home-result__score"><span class="home-result__value" data-risk-value>50</span><span class="home-result__unit">pkt na 100</span></p>
                    <div class="progress home-result__progress" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" aria-label="Wynik oceny ryzyka w punktach" data-risk-progress>
                        <span class="progress__bar" style="--value: 50%" data-risk-bar></span>
                    </div>

                    <dl class="home-result__parts">
                        <div><dt>Gatunek</dt><dd data-risk-part="species">12 pkt</dd></div>
                        <div><dt>Podłoże</dt><dd data-risk-part="substrate">14 pkt</dd></div>
                        <div><dt>Podlewanie</dt><dd data-risk-part="watering">14 pkt</dd></div>
                        <div><dt>Stanowisko</dt><dd data-risk-part="light">10 pkt</dd></div>
                    </dl>

                    <div class="home-result__live" aria-live="polite">
                        <p class="home-result__text" data-risk-text>Główne czynniki stresu: Czysta akadama szybko się kruszy i zbija, a przy tym szybko przesycha. Podlewanie według sztywnego kalendarza raz w tygodniu grozi przesuszeniem.</p>
                        <div class="home-result__alert">
                            <span class="ms" aria-hidden="true">water_drop</span>
                            <div>
                                <strong data-risk-action-title>Zalecana akcja: podlewaj, gdy drzewko potrzebuje</strong>
                                <p data-risk-action>Sprawdzaj wilgotność bambusowym patyczkiem. Podlewaj obficie tylko wtedy, gdy górny centymetr podłoża wyschnie.</p>
                            </div>
                        </div>
                    </div>
                    <noscript><p class="home-result__note">Bez JavaScriptu widzisz wynik dla ustawień domyślnych.</p></noscript>
                    <button type="submit" form="home-risk-form" class="btn btn--primary btn--block home-result__cta" data-risk-submit>Opisz objawy w Klinice</button>
                </aside>
            </div>
        </section>

        <section class="home-section home-causes" id="przyczyny" aria-labelledby="home-causes-title">
            <header class="home-section__head">
                <p class="eyebrow">Przyczyny</p>
                <h2 class="home-section__title" id="home-causes-title">Dlaczego bonsai usychają w domu</h2>
                <p class="home-section__lead">Drzewko rzadko ginie z dnia na dzień. Zwykle stoi za tym jeden z czterech błędów w pielęgnacji, a każdy zostawia inne ślady.</p>
            </header>

            <article class="home-cause-lead">
                <figure class="home-cause-lead__media">
                    <img src="/images/pine-pot-closeup.jpg" alt="Brązowiejące igły sosny nad mokrym podłożem w glinianej donicy" width="512" height="279" loading="lazy">
                </figure>
                <div class="home-cause-lead__body">
                    <p class="home-cause-lead__kicker">Błąd, który najtrudniej zauważyć</p>
                    <h3 class="home-cause-lead__title">Przelanie i gnicie korzeni</h3>
                    <p class="home-cause-lead__text">Korzenie w płytkiej donicy potrzebują tlenu tak samo jak wody. Woda stojąca w podstawce dusi drobne korzenie włośnikowe, które zaczynają gnić, a liście i igły brązowieją mimo mokrego podłoża.</p>
                    <p class="home-cause__symptom"><span>Objaw</span> wiotkie, brunatne liście przy wilgotnym podłożu</p>
                </div>
            </article>

            <ul class="home-causes__list">
                <li class="home-cause">
                    <h3 class="home-cause__title">Przesuszenie bryły korzeniowej</h3>
                    <p class="home-cause__text">Podłoże organiczne, które wyschnie na kamień, przestaje chłonąć wodę. Woda z konewki spływa po brzegach donicy, a środek bryły zostaje suchy.</p>
                    <p class="home-cause__symptom"><span>Objaw</span> kruche igły i liście zasychające na zielono</p>
                </li>
                <li class="home-cause">
                    <h3 class="home-cause__title">Cięcie w złym terminie</h3>
                    <p class="home-cause__text">Mocne cięcie wczesną wiosną u gatunków, które silnie krwawią sokiem (np. klon palmowy), osłabia drzewo i otwiera rany na infekcje grzybowe.</p>
                    <p class="home-cause__symptom"><span>Objaw</span> zamierające wierzchołki pędów</p>
                </li>
                <li class="home-cause">
                    <h3 class="home-cause__title">Drut zostawiony za długo</h3>
                    <p class="home-cause__text">Drut aluminiowy lub miedziany, który zostaje na gałęzi w okresie przyrostu na grubość, wrasta w korę i przecina tkanki przewodzące wodę.</p>
                    <p class="home-cause__symptom"><span>Objaw</span> głębokie blizny po zwojach drutu</p>
                </li>
            </ul>
        </section>

        <section class="home-section home-rescue" id="pierwsza-pomoc" aria-labelledby="home-rescue-title">
            <div class="home-rescue__intro">
                <p class="home-rescue__eyebrow">Pierwsza pomoc</p>
                <h2 class="home-rescue__title" id="home-rescue-title">Drzewko więdnie? Zacznij od tych trzech kroków.</h2>
                <p class="home-rescue__lead">Opadające pąki i zwiotczałe liście znaczą, że korzenie nie nadążają z dostarczaniem wody. Najwięcej możesz zrobić w ciągu pierwszej doby.</p>
                <a href="/clinic" class="home-rescue__link">Nie wiesz, co się dzieje? Opisz objawy w Klinice</a>
            </div>

            <ol class="home-rescue__steps">
                <li class="home-step">
                    <h3 class="home-step__title">Kąpiel bryły korzeniowej</h3>
                    <p class="home-step__text">Zanurz donicę po krawędź w letniej (ok. 20°C), miękkiej wodzie na 20–30 minut, aż z podłoża przestaną wychodzić pęcherzyki powietrza.</p>
                    <p class="home-step__note home-step__note--warn">Nie nawoź drzewka w stresie wodnym.</p>
                </li>
                <li class="home-step">
                    <h3 class="home-step__title">Osłona przed parowaniem</h3>
                    <p class="home-step__text">Drzewko liściaste przykryj przezroczystym, perforowanym workiem. Ograniczy to parowanie z liści i da korzeniom czas na odbudowę.</p>
                    <p class="home-step__note">Najdłużej 5 dni, z wietrzeniem po 10 minut dziennie.</p>
                </li>
                <li class="home-step">
                    <h3 class="home-step__title">Cień i spokój</h3>
                    <p class="home-step__text">Odsuń drzewko od bezpośredniego słońca i przeciągów. Postaw je w jasnym, rozproszonym świetle, w stałej temperaturze 18–21°C.</p>
                    <p class="home-step__note">Nie obracaj donicy, dopóki drzewko nie wypuści nowych pąków.</p>
                </li>
            </ol>
        </section>

        <section class="home-section home-styles" id="style" aria-labelledby="home-styles-title">
            <header class="home-section__head home-section__head--split">
                <div>
                    <p class="eyebrow">Style formowania</p>
                    <h2 class="home-section__title" id="home-styles-title">Trzy klasyczne kształty</h2>
                </div>
                <p class="home-section__aside">Każdy styl odwzorowuje drzewo z innego miejsca: z lasu, z otwartej łąki albo ze skalnego zbocza. W Studio rozpiszesz plan cięcia i drutowania dla wybranego stylu.</p>
            </header>

            <div class="home-styles__grid">
                <article class="home-style home-style--lead">
                    <div class="home-style__media">
                        <img src="/images/style-moyogi.jpg" alt="Bonsai w stylu Moyogi, pień o łagodnych krzywiznach" width="512" height="279" loading="lazy">
                    </div>
                    <div class="home-style__body">
                        <p class="home-style__name"><span class="term">Moyogi <span lang="ja">模様木</span></span></p>
                        <h3 class="home-style__title">Wyprostowany nieformalny</h3>
                        <p class="home-style__text">Pień wije się łagodnymi łukami, które maleją ku górze, a wierzchołek wraca dokładnie nad podstawę. Odwzorowuje drzewo z lasu, które szukało światła. Wybacza błędy, dlatego dobrze sprawdza się jako pierwszy styl.</p>
                        <p class="home-style__level">Trudność: <strong>średnia</strong></p>
                        <a href="/studio?style=moyogi" class="home-style__link">Zaplanuj Moyogi w Studio</a>
                    </div>
                </article>
                <article class="home-style">
                    <div class="home-style__media">
                        <img src="/images/style-chokkan.jpg" alt="Bonsai w stylu Chokkan, prosty pionowy pień" width="512" height="279" loading="lazy">
                    </div>
                    <div class="home-style__body">
                        <p class="home-style__name"><span class="term">Chokkan <span lang="ja">直幹</span></span></p>
                        <h3 class="home-style__title">Wyprostowany formalny</h3>
                        <p class="home-style__text">Prosty pień zwęża się ku wierzchołkowi, jak drzewo na otwartej łące bez walki o światło. Wymaga równomiernego nebari, czyli korzeni rozchodzących się na wszystkie strony.</p>
                        <p class="home-style__level">Trudność: <strong>wysoka</strong></p>
                        <a href="/studio?style=chokkan" class="home-style__link">Zaplanuj Chokkan w Studio</a>
                    </div>
                </article>
                <article class="home-style">
                    <div class="home-style__media">
                        <img src="/images/style-kengai.jpg" alt="Bonsai w stylu Kengai, kaskada opadająca poniżej donicy" width="512" height="279" loading="lazy">
                    </div>
                    <div class="home-style__body">
                        <p class="home-style__name"><span class="term">Kengai <span lang="ja">懸崖</span></span></p>
                        <h3 class="home-style__title">Kaskadowy</h3>
                        <p class="home-style__text">Pień opada poniżej dna donicy, jak drzewo przyciśnięte śniegiem i osuwającymi się skałami na górskim zboczu. Potrzebuje wysokiej donicy i starannego drutowania.</p>
                        <p class="home-style__level">Trudność: <strong>bardzo wysoka</strong></p>
                        <a href="/studio?style=kengai" class="home-style__link">Zaplanuj Kengai w Studio</a>
                    </div>
                </article>
            </div>
        </section>

        <section class="home-section home-cta" aria-labelledby="home-cta-title">
            <?php if ($currentUser === null): ?>
                <h2 class="home-cta__title" id="home-cta-title">Zobacz kartotekę, harmonogram i Klinikę w działaniu</h2>
                <p class="home-cta__lead">Wystarczy jeden formularz. Po rejestracji od razu przejdziesz do kolekcji.</p>
                <div class="home-cta__actions">
                    <a href="/register" class="btn btn--primary btn--lg">Załóż konto</a>
                    <a href="/login" class="btn btn--ghost btn--lg">Mam już konto</a>
                </div>
            <?php else: ?>
                <h2 class="home-cta__title" id="home-cta-title">Sprawdź, co dziś czeka na Twoje drzewka</h2>
                <p class="home-cta__lead">Harmonogram zbiera podlewanie, nawożenie i zabiegi formowania dla całej kolekcji.</p>
                <div class="home-cta__actions">
                    <a href="/schedule" class="btn btn--primary btn--lg">Otwórz harmonogram</a>
                    <a href="/collection" class="btn btn--ghost btn--lg">Przejdź do kolekcji</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include __DIR__ . '/partials/site-footer.html.php'; ?>
    <?php include __DIR__ . '/partials/foot.html.php'; ?>
    <script src="<?= $asset('/js/home.js') ?>" defer></script>
</body>
</html>
