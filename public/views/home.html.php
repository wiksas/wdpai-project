<!DOCTYPE html>
<html lang="pl">
<head>
    <?php include __DIR__ . '/partials/head.html.php'; ?>
    <link rel="stylesheet" href="<?= $asset('/css/pages/home.css') ?>">
</head>
<body>
    <?php include __DIR__ . '/partials/site-header.html.php'; ?>

    <main class="home container">
        <section class="home-hero" aria-labelledby="home-hero-title">
            <div class="home-hero__content">
                <p class="home-hero__eyebrow"><span class="dot dot--terra" aria-hidden="true"></span>Protokół ochrony korzeni • Wabi-Sabi &amp; Nauka</p>
                <h1 class="home-hero__title" id="home-hero-title">Ocal swoje bonsai przed usychaniem. <em>Sztuka cierpliwości</em> wsparta precyzyjną botaniką.</h1>
                <p class="home-hero__lead">Aż 78% domowych drzewek ginie przez niewidoczne zwiędnięcie naczyń korzeniowych i błędy podłoża. Łączymy japońską tradycję formowania z algorytmiczną diagnostyką turgoru tkanek.</p>
                <div class="home-hero__actions">
                    <a href="/clinic" class="btn btn--primary btn--lg"><span class="ms" aria-hidden="true">stethoscope</span>Zdiagnozuj drzewko teraz</a>
                    <a href="#pierwsza-pomoc" class="btn btn--secondary btn--lg"><span class="ms home-hero__sos" aria-hidden="true">emergency</span>Pogotowie ratunkowe</a>
                </div>
                <dl class="home-hero__stats">
                    <div class="home-hero__stat">
                        <dt class="home-hero__stat-label">Skuteczność reanimacji korzeni</dt>
                        <dd class="home-hero__stat-value">94.2%</dd>
                    </div>
                    <div class="home-hero__stat">
                        <dt class="home-hero__stat-label">Przeanalizowanych okazów</dt>
                        <dd class="home-hero__stat-value">12k+</dd>
                    </div>
                    <div class="home-hero__stat">
                        <dt class="home-hero__stat-label">Kluczowe okno reakcji na suszę</dt>
                        <dd class="home-hero__stat-value">48h</dd>
                    </div>
                </dl>
            </div>

            <article class="home-specimen" aria-label="Okaz miesiąca">
                <figure class="home-specimen__media">
                    <img src="/images/garden-pine-stand.jpg" alt="Sosna czarna bonsai na drewnianym stojaku w japońskim ogrodzie" width="512" height="279">
                    <figcaption class="home-specimen__caption">
                        <span class="home-specimen__eyebrow">Okaz miesiąca</span>
                        <span class="home-specimen__name">Pinus thunbergii (Sosna Czarna)</span>
                        <span class="home-specimen__meta">Wiek: 47 lat • Styl: Moyogi • Wilgotność bryły: 68%</span>
                    </figcaption>
                </figure>
                <div class="home-specimen__sensor">
                    <span class="ms home-specimen__sensor-icon" aria-hidden="true">verified</span>
                    <div class="home-specimen__sensor-text">
                        <strong>Sensor mikroklimatu Akadama</strong>
                        <span>Przewodność wodna w normie</span>
                    </div>
                    <span class="home-specimen__status">Optymalny</span>
                </div>
            </article>
        </section>

        <section class="home-section home-risk" id="ocena-ryzyka" aria-labelledby="home-risk-title">
            <header class="home-section__head home-section__head--split">
                <div>
                    <p class="eyebrow">Interaktywny asystent botaniczny</p>
                    <h2 class="home-section__title" id="home-risk-title">Szybka ocena ryzyka utraty turgoru</h2>
                </div>
                <p class="home-section__aside">Przeliczenie parametrów parowania bryły korzeniowej w oparciu o granulację podłoża i cykl hydrotermiczny.</p>
            </header>

            <div class="home-risk__panel">
                <form class="home-risk__form" action="/clinic" method="GET">
                    <fieldset class="home-risk__group">
                        <legend class="home-risk__label">1. Wybierz gatunek drzewka</legend>
                        <div class="home-risk__choices">
                            <label class="home-choice">
                                <input type="radio" name="species" value="pinus-thunbergii" checked>
                                <span class="home-choice__body">
                                    <span class="ms" aria-hidden="true">park</span>
                                    <span class="home-choice__title">Sosna Czarna</span>
                                    <span class="home-choice__hint home-choice__hint--latin">Pinus thunbergii</span>
                                </span>
                            </label>
                            <label class="home-choice">
                                <input type="radio" name="species" value="ulmus-parvifolia">
                                <span class="home-choice__body">
                                    <span class="ms" aria-hidden="true">nature</span>
                                    <span class="home-choice__title">Wiąz Drobnolistny</span>
                                    <span class="home-choice__hint home-choice__hint--latin">Ulmus parvifolia</span>
                                </span>
                            </label>
                            <label class="home-choice">
                                <input type="radio" name="species" value="ficus-retusa">
                                <span class="home-choice__body">
                                    <span class="ms" aria-hidden="true">potted_plant</span>
                                    <span class="home-choice__title">Fikus Retusa</span>
                                    <span class="home-choice__hint home-choice__hint--latin">Ficus retusa / ginseng</span>
                                </span>
                            </label>
                        </div>
                    </fieldset>

                    <fieldset class="home-risk__group">
                        <legend class="home-risk__label">2. Rodzaj zastosowanego podłoża</legend>
                        <div class="home-risk__choices">
                            <label class="home-choice home-choice--substrate">
                                <input type="radio" name="substrate" value="akadama" checked>
                                <span class="home-choice__body">
                                    <span class="home-choice__title">100% Akadama</span>
                                    <span class="home-choice__hint">Wysoka retencja porowata</span>
                                </span>
                            </label>
                            <label class="home-choice home-choice--substrate">
                                <input type="radio" name="substrate" value="akadama-kiryu">
                                <span class="home-choice__body">
                                    <span class="home-choice__title">Akadama + Kiryu (7:3)</span>
                                    <span class="home-choice__hint">Zrównoważony drenaż</span>
                                </span>
                            </label>
                            <label class="home-choice home-choice--substrate">
                                <input type="radio" name="substrate" value="universal">
                                <span class="home-choice__body">
                                    <span class="home-choice__title">Ziemia uniwersalna</span>
                                    <span class="home-choice__hint">Wysokie ryzyko zagęszczenia</span>
                                </span>
                            </label>
                        </div>
                    </fieldset>

                    <div class="home-risk__selects">
                        <div class="home-risk__field">
                            <label class="home-risk__label" for="home-watering">3. Częstotliwość nawadniania</label>
                            <select class="home-risk__select" id="home-watering" name="watering">
                                <option selected>Raz w tygodniu (wg harmonogramu)</option>
                                <option>Po przeschnięciu wierzchniej warstwy</option>
                                <option>Codziennie</option>
                            </select>
                        </div>
                        <div class="home-risk__field">
                            <label class="home-risk__label" for="home-light">4. Ekspozycja na światło</label>
                            <select class="home-risk__select" id="home-light" name="light">
                                <option selected>Jasne stanowisko za szybą południową</option>
                                <option>Parapet wschodni</option>
                                <option>Taras zewnętrzny (półcień)</option>
                            </select>
                        </div>
                    </div>
                </form>

                <aside class="home-result" aria-label="Wynik oceny ryzyka">
                    <div class="home-result__head">
                        <span class="home-result__kicker">Diagnostyka czasu rzeczywistego</span>
                        <span class="home-result__badge">Umiarkowane ryzyko</span>
                    </div>
                    <div class="home-result__score">
                        <h3 class="home-result__title">Wskaźnik Zagrożenia Korzeni</h3>
                        <span class="home-result__value">50%</span>
                    </div>
                    <div class="progress home-result__progress" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" aria-label="Wskaźnik zagrożenia korzeni">
                        <span class="progress__bar progress__bar--bright" style="--value: 50%"></span>
                    </div>
                    <p class="home-result__text">Główne czynniki stresu: podlewanie wg sztywnego kalendarza raz w tygodniu grozi przesuszeniem.</p>
                    <div class="home-result__alert">
                        <span class="ms" aria-hidden="true">warning</span>
                        <div>
                            <strong>Zalecana natychmiastowa akcja:</strong>
                            <p>Sprawdzaj wilgotność drewnianym patyczkiem bambusowym. Podlewaj obficie tylko wtedy, gdy górny centymetr podłoża wyschnie.</p>
                        </div>
                    </div>
                    <a href="/clinic" class="btn btn--tertiary btn--block home-result__cta">Otwórz Protokół Ratunkowy (PDF &amp; Krok po kroku)</a>
                </aside>
            </div>
        </section>

        <section class="home-section home-causes" aria-labelledby="home-causes-title">
            <header class="home-section__head">
                <p class="eyebrow">Hortikultura bez mitów</p>
                <h2 class="home-section__title" id="home-causes-title">Dlaczego drzewka bonsai umierają?</h2>
                <p class="home-section__lead">Bonsai nie umiera ze starości w kilka dni. Śmierć rośliny to niemal zawsze efekt jednego z czterech krytycznych zaburzeń fizjologii naczyń.</p>
            </header>

            <div class="home-causes__grid">
                <article class="home-cause">
                    <span class="home-cause__icon home-cause__icon--peach"><span class="ms" aria-hidden="true">local_drink</span></span>
                    <p class="home-cause__tag home-cause__tag--terra">Błąd #1 • Asfiksja korzeni</p>
                    <h3 class="home-cause__title">Przelanie i gnicie mikroskopowe</h3>
                    <p class="home-cause__text">Korzenie w płaskiej donicy potrzebują tlenu równie mocno jak wilgoci. Ciągła woda w podstawce dusi włośniki, wywołując gnicie beztlenowe i czernienie pędów.</p>
                    <p class="home-cause__symptom"><span>Objaw:</span> <strong class="home-cause__symptom-value--terra">Wiotkie, brunatne liście</strong></p>
                </article>
                <article class="home-cause">
                    <span class="home-cause__icon home-cause__icon--terra"><span class="ms" aria-hidden="true">light_mode</span></span>
                    <p class="home-cause__tag home-cause__tag--terra">Błąd #2 • Zeszklenie torfu</p>
                    <h3 class="home-cause__title">Skrajne przesuszenie rdzenia</h3>
                    <p class="home-cause__text">Gdy podłoże organiczne wyschnie na kamień, staje się hydrofobowe. Woda z konewki opływa brzegi doniczki, podczas gdy serce bryły korzeniowej pozostaje suche jak popiół.</p>
                    <p class="home-cause__symptom"><span>Objaw:</span> <strong class="home-cause__symptom-value--terra">Kruche, zielone igły</strong></p>
                </article>
                <article class="home-cause">
                    <span class="home-cause__icon home-cause__icon--neutral"><span class="ms" aria-hidden="true">content_cut</span></span>
                    <p class="home-cause__tag">Błąd #3 • Utrata soków</p>
                    <h3 class="home-cause__title">Cięcie poza oknem fenologicznym</h3>
                    <p class="home-cause__text">Radykalna redukcja gałęzi wczesną wiosną u gatunków silnie płaczących (np. klon palmowy) prowadzi do wykrwawienia soków witalnych i infekcji grzybiczych ran.</p>
                    <p class="home-cause__symptom"><span>Objaw:</span> <strong>Zamieranie wierzchołków</strong></p>
                </article>
                <article class="home-cause">
                    <span class="home-cause__icon home-cause__icon--green"><span class="ms" aria-hidden="true">cable</span></span>
                    <p class="home-cause__tag home-cause__tag--green">Błąd #4 • Martwica miazgi</p>
                    <h3 class="home-cause__title">Zbyt ciasne drutowanie gałęzi</h3>
                    <p class="home-cause__text">Aluminiowy lub miedziany drut pozostawiony w okresie intensywnego przyrostu wtórnego wrasta w kambium, trwale przecinając wiązki przewodzące floemu.</p>
                    <p class="home-cause__symptom"><span>Objaw:</span> <strong class="home-cause__symptom-value--terra">Głębokie blizny i nekroza</strong></p>
                </article>
            </div>
        </section>

        <section class="home-section home-rescue" id="pierwsza-pomoc" aria-labelledby="home-rescue-title">
            <span class="badge badge--solid-terra home-rescue__badge"><span class="ms" aria-hidden="true">healing</span>Szpital drzewek bonsai</span>
            <h2 class="home-rescue__title" id="home-rescue-title">Pierwsza Pomoc Bonsai: Protokół Resuscytacji</h2>
            <p class="home-rescue__lead">Zauważyłeś opadanie pąków lub zwiotczenie liści? Wykonaj te 3 kroki w ciągu pierwszych 24 godzin, zanim dojdzie do nieodwracalnej embolii naczyń.</p>

            <ol class="home-rescue__steps">
                <li class="home-step">
                    <div class="home-step__head">
                        <span class="home-step__number">01</span>
                        <span class="ms" aria-hidden="true">waves</span>
                    </div>
                    <h3 class="home-step__title">Kąpiel podsiąkowa bryły</h3>
                    <p class="home-step__text">Zanurz doniczkę w letniej (ok. 20°C), miękkiej wodzie aż po krawędź rantu na 20-30 minut. Czekaj, aż z podłoża całkowicie przestaną wydobywać się pęcherzyki powietrza.</p>
                    <p class="home-step__note"><strong>Wskazówka mistrza:</strong> Nigdy nie dodawaj nawozu do rośliny w stresie wodnym!</p>
                </li>
                <li class="home-step">
                    <div class="home-step__head">
                        <span class="home-step__number">02</span>
                        <span class="ms" aria-hidden="true">shield</span>
                    </div>
                    <h3 class="home-step__title">Namiot mikroklimatyczny</h3>
                    <p class="home-step__text">Dla drzewek liściastych nałóż przezroczysty worek perforowany z mikrootworami. Zmniejszy to transpirację przez aparaty szparkowe o 85%, dając korzeniom czas na odbudowę.</p>
                    <p class="home-step__note"><strong>Czas trwania:</strong> Maksymalnie 5 dni, wietrząc 10 min dziennie.</p>
                </li>
                <li class="home-step">
                    <div class="home-step__head">
                        <span class="home-step__number">03</span>
                        <span class="ms" aria-hidden="true">wb_twilight</span>
                    </div>
                    <h3 class="home-step__title">Zacienienie i stabilizacja</h3>
                    <p class="home-step__text">Odsuń drzewo od bezpośrednich promieni słonecznych i przeciągów. Umieść w jasnym, ale rozproszonym świetle (2000-3000 luksów) o stabilnej temperaturze 18-21°C.</p>
                    <p class="home-step__note"><strong>Kluczowy błąd:</strong> Nie obracaj doniczki w fazie regeneracji.</p>
                </li>
            </ol>
        </section>

        <section class="home-section home-styles" id="style" aria-labelledby="home-styles-title">
            <header class="home-section__head home-section__head--split">
                <div>
                    <p class="eyebrow">Kanon estetyczny</p>
                    <h2 class="home-section__title" id="home-styles-title">Tradycyjne Style Formowania</h2>
                </div>
                <p class="home-section__aside">Styl to nie tylko kształt – to opowieść o warunkach naturalnych: wietrze, skale, grawitacji i czasie.</p>
            </header>

            <div class="home-styles__grid">
                <article class="home-style">
                    <div class="home-style__media">
                        <img src="/images/style-chokkan.jpg" alt="Bonsai w stylu Chokkan — prosty, pionowy pień" width="512" height="279" loading="lazy">
                        <span class="home-style__label" lang="ja-Latn">Chokkan • <span lang="ja">直幹</span></span>
                    </div>
                    <div class="home-style__body">
                        <h3 class="home-style__title">Wyprostowany Formalny</h3>
                        <p class="home-style__text">Idealnie prosty pień zwężający się ku wierzchołkowi. Symbolizuje drzewo rosnące w otwartym terenie bez konkurencji o światło. Wymaga doskonałego nebari (korzeni powierzchniowych).</p>
                        <div class="home-style__difficulty">
                            <span>Trudność formowania:</span>
                            <span class="home-style__stars" role="img" aria-label="4 na 5">
                                <span class="ms ms--fill" aria-hidden="true">star</span><span class="ms ms--fill" aria-hidden="true">star</span><span class="ms ms--fill" aria-hidden="true">star</span><span class="ms ms--fill" aria-hidden="true">star</span><span class="ms" aria-hidden="true">star</span>
                            </span>
                        </div>
                    </div>
                </article>
                <article class="home-style">
                    <div class="home-style__media">
                        <img src="/images/style-moyogi.jpg" alt="Bonsai w stylu Moyogi — pień o łagodnych krzywiznach" width="512" height="279" loading="lazy">
                        <span class="home-style__label" lang="ja-Latn">Moyogi • <span lang="ja">模様木</span></span>
                    </div>
                    <div class="home-style__body">
                        <h3 class="home-style__title">Wyprostowany Nieformalny</h3>
                        <p class="home-style__text">Malowniczy pień z rytmicznymi krzywiznami, lecz wierzchołek drzewa znajduje się dokładnie nad podstawą pnia. Najbardziej uniwersalny i poetycki styl japoński.</p>
                        <div class="home-style__difficulty">
                            <span>Trudność formowania:</span>
                            <span class="home-style__stars" role="img" aria-label="3 na 5">
                                <span class="ms ms--fill" aria-hidden="true">star</span><span class="ms ms--fill" aria-hidden="true">star</span><span class="ms ms--fill" aria-hidden="true">star</span><span class="ms" aria-hidden="true">star</span><span class="ms" aria-hidden="true">star</span>
                            </span>
                        </div>
                    </div>
                </article>
                <article class="home-style">
                    <div class="home-style__media">
                        <img src="/images/style-kengai.jpg" alt="Bonsai w stylu Kengai — kaskada opadająca poniżej doniczki" width="512" height="279" loading="lazy">
                        <span class="home-style__label" lang="ja-Latn">Kengai • <span lang="ja">懸崖</span></span>
                    </div>
                    <div class="home-style__body">
                        <h3 class="home-style__title">Kaskadowy (Urwisko)</h3>
                        <p class="home-style__text">Pień opada stromo poniżej dolnej krawędzi doniczki. Inspiracją są drzewa zmagające się z lawinami śnieżnymi i spadającymi odłamkami skalnymi na zboczach gór.</p>
                        <div class="home-style__difficulty">
                            <span>Trudność formowania:</span>
                            <span class="home-style__stars" role="img" aria-label="5 na 5">
                                <span class="ms ms--fill" aria-hidden="true">star</span><span class="ms ms--fill" aria-hidden="true">star</span><span class="ms ms--fill" aria-hidden="true">star</span><span class="ms ms--fill" aria-hidden="true">star</span><span class="ms ms--fill" aria-hidden="true">star</span>
                            </span>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section class="home-section home-masters" id="mistrzowie" aria-labelledby="home-masters-title">
            <div class="home-masters__main">
                <h2 class="eyebrow" id="home-masters-title">Świadectwa mistrzów</h2>
                <figure class="home-masters__figure">
                    <blockquote class="home-masters__quote">
                        <p>"Bonsai nie należy do nas. Jesteśmy jedynie jego tymczasowymi opiekunami."</p>
                    </blockquote>
                    <figcaption class="home-masters__author">
                        <img src="/images/master-workshop.jpg" alt="" width="48" height="48" loading="lazy">
                        <span>
                            <strong>Toshio Kawabe</strong>
                            <span>Mistrz 5. pokolenia • Omiya Bonsai Village, Saitama</span>
                        </span>
                    </figcaption>
                </figure>
            </div>

            <figure class="home-review">
                <figcaption class="home-review__tag"><span class="ms" aria-hidden="true">verified_user</span>Precyzja podłoża</figcaption>
                <blockquote class="home-review__quote">
                    <p>"Zastąpienie przypadkowej ziemi mieszanką Akadama z Kiryuzuna w połączeniu z monitoringiem wilgotności uratowało moją 30-letnią Sosnę przed chlorozą."</p>
                </blockquote>
                <p class="home-review__author">— Marek S., kolekcjoner z Krakowa (14 okazów)</p>
            </figure>
            <figure class="home-review">
                <figcaption class="home-review__tag"><span class="ms" aria-hidden="true">psychology</span>Diagnostyka AI</figcaption>
                <blockquote class="home-review__quote">
                    <p>"Skan pąków wykrył przędziorka chmielowca na 2 tygodnie przed widocznym zżółknięciem igieł. Bez tej platformy straciłbym cyprysika obtusa."</p>
                </blockquote>
                <p class="home-review__author">— Joanna W., arborystka z Poznania</p>
            </figure>
        </section>

        <section class="home-section home-cta" aria-labelledby="home-cta-title">
            <span class="home-cta__icon"><span class="ms" aria-hidden="true">spa</span></span>
            <h2 class="home-cta__title" id="home-cta-title">Dołącz do elitarnego rejestru opiekunów bonsai</h2>
            <p class="home-cta__lead">Prowadź cyfrową kartotekę każdego okazu, otrzymuj spersonalizowane alerty przesadzania i konsultuj się z ekspertami botaniki.</p>
            <div class="home-cta__actions">
                <a href="/register" class="btn btn--primary btn--lg">Rozpocznij cyfrowy rejestr drzew</a>
                <a href="#ocena-ryzyka" class="btn btn--secondary btn--lg">Powtórz szybki test</a>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/partials/site-footer.html.php'; ?>
    <?php include __DIR__ . '/partials/foot.html.php'; ?>
</body>
</html>
