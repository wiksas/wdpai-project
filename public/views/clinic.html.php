<!DOCTYPE html>
<html lang="pl">
<head>
    <?php include __DIR__ . '/partials/head.html.php'; ?>
    <link rel="stylesheet" href="<?= $asset('/css/pages/clinic.css') ?>">
</head>
<body>
    <div class="app">
        <?php include __DIR__ . '/partials/app-sidebar.html.php'; ?>
        <div class="app-main">
            <?php include __DIR__ . '/partials/app-topbar.html.php'; ?>
            <main class="app-content clinic">

                <!-- ===================== MOBILE: Skaner diagnostyczny ===================== -->
                <section class="clinic-mobile" aria-labelledby="clinic-mobile-title">
                    <header class="clinic-mobile__head">
                        <div class="clinic-mobile__heading">
                            <h1 class="clinic-mobile__title" id="clinic-mobile-title">
                                <span class="dot dot--terra" aria-hidden="true"></span>
                                Skaner fitopatologiczny AI
                            </h1>
                            <p class="clinic-mobile__subtitle">Silnik bioniczny v4.2 • Aktywny</p>
                        </div>
                        <div class="clinic-mobile__tools">
                            <button type="button" class="clinic-mobile__tool" aria-label="Lampa błyskowa">
                                <span class="ms" aria-hidden="true">flash_on</span>
                            </button>
                            <button type="button" class="clinic-mobile__tool" aria-label="Siatka kadrowania">
                                <span class="ms" aria-hidden="true">grid_4x4</span>
                            </button>
                            <button type="button" class="clinic-mobile__tool" aria-label="Ustawienia skanera">
                                <span class="ms" aria-hidden="true">tune</span>
                            </button>
                        </div>
                    </header>

                    <figure class="clinic-viewer">
                        <img class="clinic-viewer__img" src="/images/pine-needles-soil.jpg" alt="Makrofotografia igieł sosny czarnej nad podłożem akadama" width="512" height="279">
                        <figcaption class="clinic-viewer__species">
                            <span class="clinic-viewer__species-name">
                                <span class="ms ms--fill" aria-hidden="true">eco</span>
                                Pinus thunbergii (Sosna Czarna)
                            </span>
                            <span class="clinic-viewer__confidence">94% pewności</span>
                        </figcaption>
                        <span class="clinic-viewer__scanline" aria-hidden="true"></span>
                        <span class="clinic-viewer__reticle" aria-hidden="true"></span>
                        <div class="clinic-viewer__point clinic-viewer__point--1">
                            <span class="clinic-viewer__num">1</span>
                            <span class="clinic-viewer__label">
                                <strong>Żółknące wierzchołki</strong>
                                <span>Początek chlorozy (Niedobór żelaza)</span>
                            </span>
                        </div>
                        <div class="clinic-viewer__point clinic-viewer__point--2">
                            <span class="clinic-viewer__label">
                                <strong>Mikrospękania Akadama</strong>
                                <span>Przesuszenie strefy włośnikowej</span>
                            </span>
                            <span class="clinic-viewer__num">2</span>
                        </div>
                        <div class="clinic-viewer__readings">
                            <span class="clinic-viewer__reading">
                                <span class="ms" aria-hidden="true">light_mode</span>
                                Ekspozycja: 4200 lx
                            </span>
                            <span class="clinic-viewer__reading">
                                <span class="ms" aria-hidden="true">water_drop</span>
                                Podłoże: 22% (Sucho)
                            </span>
                        </div>
                    </figure>

                    <nav class="clinic-chips" aria-label="Kategoria skanowania">
                        <a href="/clinic" class="clinic-chips__item is-active" aria-current="true">
                            <span class="ms" aria-hidden="true">psychiatry</span>
                            Igły / Liście
                        </a>
                        <a href="/clinic" class="clinic-chips__item">
                            <span class="ms" aria-hidden="true">texture</span>
                            Kora &amp; Pień
                        </a>
                        <a href="/clinic" class="clinic-chips__item">
                            <span class="ms" aria-hidden="true">grass</span>
                            Bryła Korzeniowa
                        </a>
                    </nav>

                    <article class="clinic-threat">
                        <div class="clinic-threat__top">
                            <div>
                                <p class="clinic-threat__level">
                                    <span class="dot dot--terra" aria-hidden="true"></span>
                                    Zagrożenie umiarkowane
                                </p>
                                <h2 class="clinic-threat__title">Wczesne przesuszenie + chloroza</h2>
                            </div>
                            <div class="clinic-threat__index">
                                <p class="clinic-threat__score"><span>68</span>/100</p>
                                <p class="clinic-threat__index-label">Indeks witalności</p>
                            </div>
                        </div>
                        <div class="clinic-threat__scale" role="img" aria-label="Skala zagrożenia: obecny stan pomiędzy optymalnym a krytycznym">
                            <span class="clinic-threat__seg clinic-threat__seg--ok"></span>
                            <span class="clinic-threat__seg clinic-threat__seg--warn"></span>
                            <span class="clinic-threat__seg clinic-threat__seg--now"></span>
                        </div>
                        <div class="clinic-threat__legend">
                            <span>Optymalny</span>
                            <span class="text-terra">Obecny stan: Chloroza Fe</span>
                            <span>Krytyczny</span>
                        </div>
                        <dl class="clinic-threat__facts">
                            <div class="clinic-threat__fact">
                                <dt>Wilgotność w rdzeniu</dt>
                                <dd class="clinic-threat__fact-value">Niedostateczna</dd>
                                <dd class="clinic-threat__fact-note text-terra">Granica więdnięcia</dd>
                            </div>
                            <div class="clinic-threat__fact">
                                <dt>Przyswajalność żelaza</dt>
                                <dd class="clinic-threat__fact-value">Zablokowana</dd>
                                <dd class="clinic-threat__fact-note">pH podłoża &gt; 7.1</dd>
                            </div>
                        </dl>
                    </article>

                    <aside class="clinic-advice" aria-labelledby="clinic-advice-title">
                        <h2 class="clinic-advice__title" id="clinic-advice-title">
                            <span class="ms ms--fill" aria-hidden="true">health_and_safety</span>
                            Rekomendacja mistrza botaniki
                        </h2>
                        <p class="clinic-advice__text">Zanurz całą ceramiczną donicę w miękkiej deszczówce na <strong>15 minut</strong> aż do ustania pęcherzyków powietrza. Następnie odstaw drzewko na <strong>4 dni w osłonięty półcień</strong>. Bezwzględnie wstrzymaj nawożenie azotem do czasu zazielenienia igieł!</p>
                        <p class="clinic-advice__window">
                            <span class="ms" aria-hidden="true">timer</span>
                            Okno reakcji: najbliższe 24–36 godzin
                        </p>
                    </aside>

                    <div class="clinic-mobile__actions">
                        <a href="#clinic-protocol" class="btn btn--tertiary clinic-mobile__cta">
                            <span class="ms" aria-hidden="true">bolt</span>
                            Generuj Protokół Ratunkowy (Rescue Action)
                        </a>
                        <a href="#clinic-consultant" class="btn clinic-mobile__secondary">
                            <span class="ms" aria-hidden="true">chat_bubble</span>
                            Zapytaj AI Mistrza
                        </a>
                        <a href="/schedule" class="btn clinic-mobile__secondary">
                            <span class="ms" aria-hidden="true">bookmark_add</span>
                            Zapisz w Dzienniku
                        </a>
                    </div>

                    <section class="clinic-previous" aria-labelledby="clinic-previous-title">
                        <header class="clinic-previous__head">
                            <h2 class="clinic-previous__title" id="clinic-previous-title">Poprzednie Skanowania Okazu</h2>
                            <span class="clinic-previous__tag">Pinus #04</span>
                        </header>
                        <a href="/project/1" class="clinic-previous__item">
                            <img src="/images/bonsai-thumb.jpg" alt="" width="40" height="40">
                            <span class="clinic-previous__text">
                                <span class="clinic-previous__meta">14 dni temu • 89 pkt witalności</span>
                                <span class="clinic-previous__note">Przed letnim formowaniem drutem</span>
                            </span>
                            <span class="ms" aria-hidden="true">chevron_right</span>
                        </a>
                    </section>
                </section>

                <!-- ===================== DESKTOP: Klinika ===================== -->
                <div class="clinic-desktop">
                    <header class="clinic-hero">
                        <div class="clinic-hero__intro">
                            <p class="clinic-hero__crumbs">
                                <span class="clinic-hero__badge">
                                    <span class="dot dot--terra" aria-hidden="true"></span>
                                    Klinika Fitopatologiczna AI
                                </span>
                                <span class="clinic-hero__protocol">/ Protokół B-902</span>
                            </p>
                            <h1 class="clinic-hero__title">Diagnostyka Zdrowia Drzewek</h1>
                            <p class="clinic-hero__lead">Połączenie wielowiekowej sztuki pielęgnacji <em>wabi-sabi</em> z mikrobiologią roślin. Natychmiastowa analiza zmian chorobowych, stresu wodnego i deficytów glebowych.</p>
                        </div>
                        <dl class="clinic-stats">
                            <div class="clinic-stats__item">
                                <dt>Czas reakcji</dt>
                                <dd>&lt; 1.8 sekundy</dd>
                            </div>
                            <div class="clinic-stats__item">
                                <dt>Trafność botaniczna</dt>
                                <dd>97.4%</dd>
                            </div>
                            <div class="clinic-stats__item clinic-stats__item--terra">
                                <dt>Przypadki uratowane</dt>
                                <dd>1,248</dd>
                            </div>
                        </dl>
                    </header>

                    <div class="clinic-grid">
                        <div class="clinic-grid__side">
                            <!-- Skaner okazu -->
                            <section class="clinic-card clinic-scanner" aria-labelledby="clinic-scanner-title">
                                <header class="clinic-card__head">
                                    <span class="clinic-card__icon" aria-hidden="true"><span class="ms">document_scanner</span></span>
                                    <div class="clinic-card__heading">
                                        <h2 class="clinic-card__title" id="clinic-scanner-title">Skaner Okazu</h2>
                                        <p class="clinic-card__subtitle">Wykrywanie patogenów i stresu</p>
                                    </div>
                                    <span class="clinic-scanner__version">v4.2 Neural Bonsai</span>
                                </header>

                                <form class="clinic-scanner__form" action="/clinic" method="post" enctype="multipart/form-data">
                                    <fieldset class="clinic-parts">
                                        <legend class="clinic-parts__legend">Wybierz obserwowany element:</legend>
                                        <div class="clinic-parts__grid">
                                            <label class="clinic-part">
                                                <input class="visually-hidden" type="radio" name="part" value="leaves" checked>
                                                <span class="ms" aria-hidden="true">psychiatry</span>
                                                <span class="clinic-part__text">
                                                    <span class="clinic-part__name">Liście / Igły</span>
                                                    <span class="clinic-part__hint">Chloroza, nekrozy</span>
                                                </span>
                                            </label>
                                            <label class="clinic-part">
                                                <input class="visually-hidden" type="radio" name="part" value="bark">
                                                <span class="ms" aria-hidden="true">nature</span>
                                                <span class="clinic-part__text">
                                                    <span class="clinic-part__name">Kora / Pień</span>
                                                    <span class="clinic-part__hint">Pęknięcia, jin, shari</span>
                                                </span>
                                            </label>
                                            <label class="clinic-part">
                                                <input class="visually-hidden" type="radio" name="part" value="roots">
                                                <span class="ms" aria-hidden="true">potted_plant</span>
                                                <span class="clinic-part__text">
                                                    <span class="clinic-part__name">Podłoże i korzenie</span>
                                                    <span class="clinic-part__hint">Akadama, przelanie</span>
                                                </span>
                                            </label>
                                            <label class="clinic-part">
                                                <input class="visually-hidden" type="radio" name="part" value="wiring">
                                                <span class="ms" aria-hidden="true">cable</span>
                                                <span class="clinic-part__text">
                                                    <span class="clinic-part__name">Drutowanie</span>
                                                    <span class="clinic-part__hint">Wrastanie drutu</span>
                                                </span>
                                            </label>
                                        </div>
                                    </fieldset>

                                    <div class="clinic-upload">
                                        <input class="clinic-upload__input visually-hidden" type="file" id="clinic-photo" name="photo" accept="image/jpeg,image/png,image/heic">
                                        <label class="clinic-upload__zone" for="clinic-photo">
                                            <span class="clinic-upload__icon" aria-hidden="true"><span class="ms">photo_camera</span></span>
                                            <span class="clinic-upload__title">Przeciągnij lub wgraj makrofotografię</span>
                                            <span class="clinic-upload__text">Zrób zbliżenie w naturalnym świetle dziennym (min. 1200x1200px dla dokładności splotu aparatów szparkowych)</span>
                                        </label>
                                        <div class="clinic-upload__row">
                                            <label class="clinic-upload__button" for="clinic-photo">Wybierz plik</label>
                                            <span class="clinic-upload__formats">JPG, PNG, HEIC do 25MB</span>
                                        </div>
                                    </div>
                                </form>

                                <div class="clinic-context">
                                    <div class="clinic-context__head">
                                        <h3 class="clinic-context__title">Kontekst środowiskowy</h3>
                                        <p class="clinic-context__note">Opcjonalne, zwiększa trafność do 99%</p>
                                    </div>
                                    <dl class="clinic-context__grid">
                                        <div class="clinic-context__item">
                                            <dt>Stanowisko</dt>
                                            <dd>Balkon południowy, 6h słońca</dd>
                                        </div>
                                        <div class="clinic-context__item">
                                            <dt>Ostatnie przesadzanie</dt>
                                            <dd>Marzec 2023 (14 msc)</dd>
                                        </div>
                                    </dl>
                                </div>
                            </section>

                            <!-- Katalog symptomów -->
                            <section class="clinic-card clinic-catalog" aria-labelledby="clinic-catalog-title">
                                <header class="clinic-catalog__head">
                                    <div>
                                        <h2 class="clinic-card__title" id="clinic-catalog-title">Katalog Symptomów</h2>
                                        <p class="clinic-card__subtitle">Szybkie porównanie organoleptyczne</p>
                                    </div>
                                    <span class="ms clinic-catalog__book" aria-hidden="true">menu_book</span>
                                </header>
                                <ul class="clinic-catalog__list">
                                    <li class="clinic-symptom">
                                        <span class="ms clinic-symptom__icon clinic-symptom__icon--terra" aria-hidden="true">home_health</span>
                                        <div class="clinic-symptom__body">
                                            <h3 class="clinic-symptom__name">Gnicie korzeni (Root Rot)</h3>
                                            <p class="clinic-symptom__desc">Czarne, gąbczaste końcówki korzeni...</p>
                                        </div>
                                        <span class="clinic-symptom__level clinic-symptom__level--critical">Krytyczny</span>
                                    </li>
                                    <li class="clinic-symptom">
                                        <span class="ms clinic-symptom__icon clinic-symptom__icon--terra" aria-hidden="true">local_fire_department</span>
                                        <div class="clinic-symptom__body">
                                            <h3 class="clinic-symptom__name">Przenawożenie (Fertilizer Burn)</h3>
                                            <p class="clinic-symptom__desc">Brązowienie i zasychanie...</p>
                                        </div>
                                        <span class="clinic-symptom__level clinic-symptom__level--frequent">Częsty</span>
                                    </li>
                                    <li class="clinic-symptom">
                                        <span class="ms clinic-symptom__icon" aria-hidden="true">pest_control</span>
                                        <div class="clinic-symptom__body">
                                            <h3 class="clinic-symptom__name">Przędziorki i Mszyce korowe</h3>
                                            <p class="clinic-symptom__desc">Cienka pajęczynka u...</p>
                                        </div>
                                        <span class="clinic-symptom__level">Umiarkowany</span>
                                    </li>
                                    <li class="clinic-symptom">
                                        <span class="ms clinic-symptom__icon clinic-symptom__icon--green" aria-hidden="true">light_mode</span>
                                        <div class="clinic-symptom__body">
                                            <h3 class="clinic-symptom__name">Oparzenia słoneczne (Sun Scorch)</h3>
                                            <p class="clinic-symptom__desc">Wyblakłe, pergaminowe plamy</p>
                                        </div>
                                        <span class="clinic-symptom__level">Łagodny</span>
                                    </li>
                                </ul>
                            </section>
                        </div>

                        <div class="clinic-grid__main">
                            <!-- Aktywny wynik analizy -->
                            <article class="clinic-result" aria-labelledby="clinic-result-title">
                                <header class="clinic-result__head">
                                    <div class="clinic-result__top">
                                        <h2 class="clinic-result__title" id="clinic-result-title">
                                            <span class="dot dot--terra" aria-hidden="true"></span>
                                            <span>Aktywny Wynik Analizy</span>
                                        </h2>
                                        <span class="clinic-result__specimen">Okaz #B-104: Pinus Thunbergii (Sosna Czarna)</span>
                                    </div>
                                    <div class="clinic-result__meta">
                                        <span>Czas analizy: Dzisiaj, 14:28</span>
                                        <button type="button" class="clinic-result__action" aria-label="Drukuj raport">
                                            <span class="ms" aria-hidden="true">print</span>
                                        </button>
                                        <button type="button" class="clinic-result__action" aria-label="Udostępnij raport">
                                            <span class="ms" aria-hidden="true">share</span>
                                        </button>
                                    </div>
                                </header>

                                <div class="clinic-result__body">
                                    <figure class="clinic-photo">
                                        <img class="clinic-photo__img" src="/images/pine-pot-closeup.jpg" alt="Zbliżenie sosny czarnej z żółknącymi igłami i suchym podłożem" width="512" height="279">
                                        <div class="clinic-photo__marker clinic-photo__marker--1">
                                            <span class="clinic-photo__num">1</span>
                                            <span class="clinic-photo__label">Chloroza wierzchołków</span>
                                        </div>
                                        <div class="clinic-photo__marker clinic-photo__marker--2">
                                            <span class="clinic-photo__label">Zbita akadama (warstwa &gt;4cm)</span>
                                            <span class="clinic-photo__num">2</span>
                                        </div>
                                        <figcaption class="clinic-photo__caption">Siatka sensoryczna 4K • RGB + NIR</figcaption>
                                    </figure>

                                    <div class="clinic-diagnosis">
                                        <div class="clinic-diagnosis__top">
                                            <p class="clinic-diagnosis__eyebrow">Główna diagnoza kliniczna</p>
                                            <span class="clinic-diagnosis__confidence">92% Pewności</span>
                                        </div>
                                        <h3 class="clinic-diagnosis__title">Wczesne stadium przesuszenia podłoża połączone z chlorozą żelazową</h3>
                                        <p class="clinic-diagnosis__text">Drzewko wykazuje zablokowanie pobierania mikroelementów spowodowane gwałtowną desykacją głębszych warstw bryły korzeniowej. Pąki świeczkowe zachowują żywotność miazgi, rokowanie pomyślne przy szybkiej interwencji.</p>

                                        <dl class="clinic-vitals">
                                            <div class="clinic-vitals__item">
                                                <dt>Wilgotność bryły</dt>
                                                <dd class="clinic-vitals__value clinic-vitals__value--terra">14%</dd>
                                                <dd class="clinic-vitals__note clinic-vitals__note--terra">Krytycznie sucho</dd>
                                            </div>
                                            <div class="clinic-vitals__item">
                                                <dt>Stan Aparatów Szp.</dt>
                                                <dd class="clinic-vitals__value clinic-vitals__value--terra">Zamknięte</dd>
                                                <dd class="clinic-vitals__note">Obrona turgoru</dd>
                                            </div>
                                            <div class="clinic-vitals__item">
                                                <dt>Witalność miazgi</dt>
                                                <dd class="clinic-vitals__value clinic-vitals__value--green">88%</dd>
                                                <dd class="clinic-vitals__note clinic-vitals__note--green">Tkanki nienaruszone</dd>
                                            </div>
                                        </dl>

                                        <div class="clinic-symptoms">
                                            <h4 class="clinic-symptoms__title">Potwierdzone objawy:</h4>
                                            <ul class="clinic-symptoms__list">
                                                <li><span class="ms" aria-hidden="true">check_circle</span>Matowienie i żółknięcie od nasady starych igieł (retencja chlorofilu zredukowana).</li>
                                                <li><span class="ms" aria-hidden="true">check_circle</span>Pęknięcia powierzchniowe i rozpad ziaren twardej akadamy przy ściance donicy Tokoname.</li>
                                                <li><span class="ms" aria-hidden="true">check_circle</span>Brak wilgoci higroskopijnej na głębokości 35mm (odczyt profilometru).</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <section class="clinic-protocol" id="clinic-protocol" aria-labelledby="clinic-protocol-title">
                                    <header class="clinic-protocol__head">
                                        <span class="clinic-protocol__icon" aria-hidden="true"><span class="ms">medical_services</span></span>
                                        <h3 class="clinic-protocol__title" id="clinic-protocol-title">Natychmiastowy Protokół Ratunkowy (Rescue Action Protocol)</h3>
                                        <p class="clinic-protocol__deadline">Wymaga wdrożenia w ciągu 6h</p>
                                    </header>
                                    <ol class="clinic-steps">
                                        <li class="clinic-step">
                                            <div class="clinic-step__top">
                                                <span class="clinic-step__badge">Krok 01</span>
                                                <span class="ms" aria-hidden="true">waves</span>
                                            </div>
                                            <h4 class="clinic-step__title">Kąpiel zanurzeniowa (15 min)</h4>
                                            <p class="clinic-step__text">Zanurz całą donicę po krawędź w miękkiej deszczówce lub wodzie odstałej o temp. 18-20°C. Czekaj do ustania ulatywania bąbelków powietrza z akadamy.</p>
                                            <p class="clinic-step__foot">
                                                <span>Priorytet: Natychmiast</span>
                                                <span class="ms" aria-hidden="true">alarm</span>
                                            </p>
                                        </li>
                                        <li class="clinic-step">
                                            <div class="clinic-step__top">
                                                <span class="clinic-step__badge">Krok 02</span>
                                                <span class="ms" aria-hidden="true">wb_twilight</span>
                                            </div>
                                            <h4 class="clinic-step__title">Odstawienie do półcienia</h4>
                                            <p class="clinic-step__text">Przenieś sosnę ze stanowiska pełnego słońca w zaciszne miejsce osłonięte od wiatru (maksymalnie 40% nasłonecznienia), aby zredukować stres transpiracyjny.</p>
                                            <p class="clinic-step__foot">
                                                <span>Czas trwania: 5-7 dni</span>
                                                <span class="ms" aria-hidden="true">event_available</span>
                                            </p>
                                        </li>
                                        <li class="clinic-step clinic-step--terra">
                                            <div class="clinic-step__top">
                                                <span class="clinic-step__badge">Krok 03</span>
                                                <span class="ms" aria-hidden="true">block</span>
                                            </div>
                                            <h4 class="clinic-step__title">Blokada nawożenia azotem</h4>
                                            <p class="clinic-step__text">Całkowity zakaz nawożenia przez 14 dni. Po 5 dniach zaaplikuj wyłącznie chelat żelaza (Fe-EDDHA) w 20% dawki rekomendowanej bezpośrednio dolistnie.</p>
                                            <p class="clinic-step__foot">
                                                <span>Karencja: 14 dni</span>
                                                <span class="ms" aria-hidden="true">warning</span>
                                            </p>
                                        </li>
                                    </ol>
                                    <div class="clinic-protocol__confirm">
                                        <p class="clinic-protocol__note">Zastosowanie procedury przywraca optymalne ciśnienie osmotyczne włośników korzeniowych.</p>
                                        <button type="button" class="clinic-protocol__button">
                                            <span class="ms" aria-hidden="true">check</span>
                                            Zatwierdź wykonanie protokołu
                                        </button>
                                    </div>
                                </section>
                            </article>

                            <!-- Konsultant AI -->
                            <section class="clinic-card clinic-chat" id="clinic-consultant" aria-labelledby="clinic-chat-title">
                                <header class="clinic-chat__head">
                                    <span class="clinic-chat__avatar" aria-hidden="true">AI</span>
                                    <div class="clinic-chat__heading">
                                        <h2 class="clinic-chat__title" id="clinic-chat-title">Konsultant Botaniczny Komorebi</h2>
                                        <p class="clinic-chat__subtitle">Dedykowany model fitopatologiczny • Aktywny</p>
                                    </div>
                                    <p class="clinic-chat__status"><span class="dot dot--green" aria-hidden="true"></span>Gotowy do odpowiedzi</p>
                                </header>
                                <div class="clinic-chat__thread">
                                    <span class="clinic-chat__bot" aria-hidden="true"><span class="ms ms--fill">eco</span></span>
                                    <div class="clinic-chat__message">
                                        <p class="clinic-chat__meta">Komorebi AI • 14:29</p>
                                        <p class="clinic-chat__bubble">Na podstawie zdjęcia Twojej sosny czarnej: igły wykazują typową utratę turgoru bez martwicy drewna. Nie wykonuj teraz żadnego cięcia formującego ani drutowania gałęzi giętych – drewno jest kruche. Czy chcesz sprawdzić metodę drapania kory (cambium test)?</p>
                                    </div>
                                </div>
                                <form class="clinic-chat__form" action="/clinic" method="get">
                                    <p class="clinic-chat__faq-title">Częste pytania do tej diagnozy:</p>
                                    <div class="clinic-chat__faq">
                                        <button type="submit" class="clinic-chat__question" name="question" value="Jak sprawdzić czy gałązka jeszcze żyje pod korą?">
                                            <span class="ms" aria-hidden="true">help</span>
                                            Jak sprawdzić czy gałązka jeszcze żyje pod korą?
                                        </button>
                                        <button type="submit" class="clinic-chat__question" name="question" value="Kiedy bezpiecznie podlać po kąpieli zanurzeniowej?">
                                            <span class="ms" aria-hidden="true">help</span>
                                            Kiedy bezpiecznie podlać po kąpieli zanurzeniowej?
                                        </button>
                                        <button type="submit" class="clinic-chat__question" name="question" value="Czy usunąć całkowicie zżółknięte igły z zeszłego roku?">
                                            <span class="ms" aria-hidden="true">help</span>
                                            Czy usunąć całkowicie zżółknięte igły z zeszłego roku?
                                        </button>
                                    </div>
                                    <div class="clinic-chat__compose">
                                        <label class="visually-hidden" for="clinic-question">Pytanie do asystenta</label>
                                        <input class="clinic-chat__input" type="text" id="clinic-question" name="q" placeholder="Zadaj pytanie asystentowi (np. jak dawkować chelat żelaza)...">
                                        <button type="submit" class="clinic-chat__send" aria-label="Wyślij pytanie">
                                            <span class="ms" aria-hidden="true">send</span>
                                        </button>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </div>

                    <!-- Archiwum uzdrowionych okazów -->
                    <section class="clinic-archive" aria-labelledby="clinic-archive-title">
                        <header class="clinic-archive__head">
                            <div>
                                <h2 class="clinic-archive__title" id="clinic-archive-title">Archiwum Uzdrowionych Okazów</h2>
                                <p class="clinic-archive__lead">Przykłady powrotu do równowagi biologicznej dzięki wczesnej detekcji algorytmu.</p>
                            </div>
                            <nav class="clinic-archive__filters" aria-label="Filtr gatunków">
                                <a href="/clinic" class="clinic-archive__filter">Wszystkie gatunki</a>
                                <a href="/clinic" class="clinic-archive__filter is-active" aria-current="true">Iglaste (Conifers)</a>
                                <a href="/clinic" class="clinic-archive__filter">Liściaste (Deciduous)</a>
                            </nav>
                        </header>
                        <div class="clinic-archive__grid">
                            <article class="clinic-case">
                                <div class="clinic-case__media">
                                    <img src="/images/garden-red-maple-lantern.jpg" alt="Klon palmowy z czerwonymi liśćmi w ogrodzie z latarnią" width="512" height="279" loading="lazy">
                                    <span class="clinic-case__badge">Uzdrowiono w 28 dni</span>
                                </div>
                                <p class="clinic-case__meta"><span>Acer Palmatum (Klon Palmowy)</span><span>Wiek: 34 lata</span></p>
                                <h3 class="clinic-case__title">Martwica wierzchołkowa po wietrze</h3>
                                <p class="clinic-case__text">Interwencja: Osłona parawanem bambusowym i zraszanie poranne....</p>
                            </article>
                            <article class="clinic-case">
                                <div class="clinic-case__media">
                                    <img src="/images/bonsai-white-room.jpg" alt="Jałowiec Shimpaku z jasnym drewnem jin na białym tle" width="512" height="279" loading="lazy">
                                    <span class="clinic-case__badge">Uzdrowiono w 14 dni</span>
                                </div>
                                <p class="clinic-case__meta"><span>Juniperus chinensis 'Shimpaku'</span><span>Wiek: 52 lata</span></p>
                                <h3 class="clinic-case__title">Infestacja przędziorka chmielowca</h3>
                                <p class="clinic-case__text">Interwencja: Mechaniczne spłukiwanie strumieniem wody i oprysk olejkiem neem</p>
                            </article>
                            <article class="clinic-case">
                                <div class="clinic-case__media">
                                    <img src="/images/bonsai-wooden-table.jpg" alt="Wiąz drobnolistny na drewnianym stole przy oknie" width="512" height="279" loading="lazy">
                                    <span class="clinic-case__badge">Uzdrowiono w 42 dni</span>
                                </div>
                                <p class="clinic-case__meta"><span>Ulmus Parvifolia (Wiąz)</span><span>Wiek: 19 lat</span></p>
                                <h3 class="clinic-case__title">Zakwaszenie i zablokowanie korzeni</h3>
                                <p class="clinic-case__text">Interwencja: Płukanie podłoża roztworem z dodatkiem węgla aktywnego oraz...</p>
                            </article>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
    <?php include __DIR__ . '/partials/app-bottom-nav.html.php'; ?>
</body>
</html>
