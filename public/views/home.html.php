<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOMOREBI — Sztuka cierpliwości i precyzyjna botanika</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

    <header class="top-navbar">
        <a href="/" class="brand-logo">🌿 KOMOREBI</a>
        <nav aria-label="Główna nawigacja">
            <ul class="nav-links">
                <li><a href="/" class="active">Przewodnik</a></li>
                <li><a href="/projects">Kolekcja drzewek</a></li>
                <li><a href="/project">Studio formowania i diagnostyka</a></li>
                <li><a href="/login">Logowanie</a></li>
            </ul>
        </nav>
        <a href="/register" class="btn btn-primary">Dołącz do rejestru</a>
    </header>

    <main class="container">
        <!-- 1. HERO SEKCJA -->
        <section class="hero-grid section-block">
            <div>
                <span class="badge badge-green">KOMOREBI • CYFROWY DZIENNIK OPIEKUNA BONSAI</span>
                <h1 class="hero-title">
                    Ocal swoje bonsai przed usychaniem. <em>Sztuka cierpliwości</em> wsparta precyzyjną botaniką.
                </h1>
                <p style="color: var(--color-text-muted); margin-bottom: 1.5rem;">
                    Większość drzewek obumiera w pierwszych 6 miesiącach z powodu błędnego nawadniania i braku spoczynku zimowego. Zbuduj harmonogram dopasowany do gatunku i mikroklimatu Twojego mieszkania.
                </p>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <a href="/projects" class="btn btn-primary">Otwórz panel kolekcji</a>
                    <a href="/project" class="btn btn-outline">Uruchom diagnostykę</a>
                </div>

                <div class="hero-metrics">
                    <div>
                        <strong class="serif" style="font-size: 1.4rem;">98.4%</strong>
                        <p style="font-size: 0.78rem; color: var(--color-text-muted);">Przeżywalność kolekcji</p>
                    </div>
                    <div>
                        <strong class="serif" style="font-size: 1.4rem;">42 gatunki</strong>
                        <p style="font-size: 0.78rem; color: var(--color-text-muted);">W bazie protokołów</p>
                    </div>
                    <div>
                        <strong class="serif" style="font-size: 1.4rem;">24/7</strong>
                        <p style="font-size: 0.78rem; color: var(--color-text-muted);">Monitoring wilgotności</p>
                    </div>
                </div>
            </div>

            <div class="card" style="background-color: var(--color-primary); color: #fff; text-align: center; padding: 2rem;">
                <span class="badge badge-orange" style="margin-bottom: 1rem;">OKAZ MIESIĄCA KOMOREBI</span>
                <h2 style="color: #fff; font-size: 1.5rem; margin-bottom: 0.5rem;">Sosna Drobnokwiatowa</h2>
                <p style="font-size: 0.85rem; opacity: 0.85; margin-bottom: 1.5rem;">
                    Pinus parviflora • Styl Moyogi • Wiek: 28 lat
                </p>
                <div style="background: rgba(255,255,255,0.1); padding: 1.25rem; border-radius: var(--radius-sm); text-align: left; font-size: 0.82rem;">
                    <div>🌱 Podłoże: Akadama + Kiryuzuna (60/40)</div>
                    <div style="margin-top: 0.4rem;">💧 Wilgotność bryły: <strong>62% (Optymalna)</strong></div>
                    <div style="margin-top: 0.4rem;">✂️ Następny zabieg: Uszczykiwanie świeczek (Maj)</div>
                </div>
            </div>
        </section>

        <!-- 2. SZYBKA OCENA RYZYKA UTRATY WIGORU -->
        <section class="card section-block">
            <div class="section-header">
                <div>
                    <span class="badge badge-orange">KALKULATOR DIAGNOSTYCZNY KOMOREBI</span>
                    <h2 style="font-size: 1.6rem; margin-top: 0.35rem;">Szybka ocena ryzyka utraty wigoru</h2>
                </div>
                <small style="color: var(--color-text-muted);">Zaktualizowano dla sezonu wiosna/lato</small>
            </div>

            <div class="grid-2">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="calc-species">1. Wybierz gatunek drzewka</label>
                        <select id="calc-species" class="form-select">
                            <option>Klon Palmowy (Acer palmatum)</option>
                            <option>Wiąz Drobnolistny (Ulmus parvifolia)</option>
                            <option>Fikus Retusa (Ficus microcarpa)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="calc-place">2. Ekspozycja świetlna</label>
                        <select id="calc-place" class="form-select">
                            <option>Wewnątrz — powyżej 2m od okna (Wysokie ryzyko)</option>
                            <option>Parapet południowy / wschodni</option>
                            <option>Taras zewnętrzny (półcień)</option>
                        </select>
                    </div>
                </div>

                <div style="background-color: var(--color-accent-soft); border: 1px solid var(--color-accent); border-radius: var(--radius-md); padding: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="badge badge-orange">WYNIK: PODWYŻSZONE RYZYKO (74%)</span>
                        <strong style="color: var(--color-accent);">Interwencja w 48h</strong>
                    </div>
                    <p style="font-size: 0.88rem; margin: 0.85rem 0 1.25rem;">
                        Klon palmowy trzymany w ogrzewanym pomieszczeniu szybko traci turgor liści. Przenieś okaz w chłodniejsze miejsce zewnętrzne i zastosuj zraszanie korony.
                    </p>
                    <a href="/project" class="btn btn-accent" style="width: 100%;">Pobierz pełny plan ratunkowy</a>
                </div>
            </div>
        </section>

        <!-- 3. DLACZEGO DRZEWKA BONSAI UMIERAJĄ? -->
        <section class="section-block">
            <div class="section-header">
                <div>
                    <h2 style="font-size: 1.65rem;">Dlaczego drzewka bonsai umierają?</h2>
                    <p style="color: var(--color-text-muted); font-size: 0.9rem;">Cztery najczęstsze błędy pielęgnacyjne popełniane przez początkujących opiekunów.</p>
                </div>
            </div>

            <div class="grid-4">
                <article class="card">
                    <span class="badge badge-orange">01 • KORZENIE</span>
                    <h3 style="font-size: 1.1rem; margin: 0.65rem 0 0.4rem;">Podlewanie według kalendarza</h3>
                    <p style="font-size: 0.84rem; color: var(--color-text-muted);">
                        Nawadnianie „co środę” zamiast po przeschnięciu wierzchniej warstwy akadamy prowadzi do gnicia korzeni włośnikowych.
                    </p>
                </article>
                <article class="card">
                    <span class="badge badge-orange">02 • ŚWIATŁO</span>
                    <h3 style="font-size: 1.1rem; margin: 0.65rem 0 0.4rem;">Głód fotonowy w głębi pokoju</h3>
                    <p style="font-size: 0.84rem; color: var(--color-text-muted);">
                        Natężenie światła 2 metry od okna spada o 80%. Drzewko zrzuca wewnętrzne liście i wydłuża międzywęźla.
                    </p>
                </article>
                <article class="card">
                    <span class="badge badge-orange">03 • KLIMAT</span>
                    <h3 style="font-size: 1.1rem; margin: 0.65rem 0 0.4rem;">Uprawa gatunków leśnych w domu</h3>
                    <p style="font-size: 0.84rem; color: var(--color-text-muted);">
                        Sosny, jałowce i klony wymagają bezwzględnego okresu zimowego spoczynku w temperaturze 0–7°C.
                    </p>
                </article>
                <article class="card">
                    <span class="badge badge-orange">04 • PODŁOŻE</span>
                    <h3 style="font-size: 1.1rem; margin: 0.65rem 0 0.4rem;">Ziemia uniwersalna zamiast granulatu</h3>
                    <p style="font-size: 0.84rem; color: var(--color-text-muted);">
                        Ciężki torf odcina dostęp tlenu do bryły korzeniowej. Wymagana jest mineralna mieszanka o frakcji 3–5 mm.
                    </p>
                </article>
            </div>
        </section>

        <!-- 4. PIERWSZA POMOC BONSAI: PROTOKÓŁ RESUSCYTACJI -->
        <section class="emergency-banner section-block">
            <span class="badge badge-orange">TRYB RATUNKOWY SOS</span>
            <h2 style="font-size: 1.8rem; margin: 0.5rem 0 0.4rem;">Pierwsza Pomoc Bonsai: Protokół Resuscytacji</h2>
            <p style="opacity: 0.85; font-size: 0.9rem; margin-bottom: 1.75rem;">
                Postępuj zgodnie z trzema krokami, jeśli Twoje drzewko nagle zrzuciło liście lub jego gałązki zaczęły marszczyć korę.
            </p>

            <div class="grid-3">
                <div class="emergency-card">
                    <h3>Krok 1: Test kambium</h3>
                    <p style="font-size: 0.84rem; opacity: 0.85;">
                        Delikatnie zarysuj paznokciem korę u nasady pnia. Żywa, zielona warstwa pod spodem oznacza, że drzewko nadal przewodzi soki.
                    </p>
                </div>
                <div class="emergency-card">
                    <h3>Krok 2: Kąpiel zanurzeniowa</h3>
                    <p style="font-size: 0.84rem; opacity: 0.85;">
                        Zanurz całą doniczkę w odstanej wodzie na 15 minut, aż przestaną uchodzić pęcherzyki powietrza z przesuszonej bryły.
                    </p>
                </div>
                <div class="emergency-card">
                    <h3>Krok 3: Wstrzymanie nawożenia</h3>
                    <p style="font-size: 0.84rem; opacity: 0.85;">
                        Nigdy nie podawaj nawozów mineralnych osłabionym okazom — sole mineralne spalą uszkodzone włośniki korzeniowe.
                    </p>
                </div>
            </div>
        </section>

        <!-- 5. TRADYCJNE STYLE FORMOWANIA -->
        <section class="section-block">
            <div class="section-header">
                <div>
                    <h2 style="font-size: 1.65rem;">Tradycyjne Style Formowania</h2>
                    <p style="color: var(--color-text-muted); font-size: 0.9rem;">Klasyfikacja japońskich kanonów estetycznych w bazie wiedzy KOMOREBI.</p>
                </div>
                <a href="/project" class="btn btn-outline">Zobacz pełny atlas</a>
            </div>

            <div class="grid-3">
                <article class="card">
                    <span class="badge badge-green">CHOKKAN</span>
                    <h3 style="font-size: 1.2rem; margin: 0.5rem 0;">Styl formalny wyprostowany</h3>
                    <p style="font-size: 0.85rem; color: var(--color-text-muted);">
                        Idealnie prosty, zwężający się ku górze pień z symetrycznym układem konarów. Polecany dla sosen i cyprysików.
                    </p>
                </article>
                <article class="card">
                    <span class="badge badge-green">MOYOGI</span>
                    <h3 style="font-size: 1.2rem; margin: 0.5rem 0;">Styl nieformalny wyprostowany</h3>
                    <p style="font-size: 0.85rem; color: var(--color-text-muted);">
                        Pień wygina się w harmonijną literę S, a gałęzie wyrastają zawsze po zewnętrznej stronie łuków.
                    </p>
                </article>
                <article class="card">
                    <span class="badge badge-green">KENGAI</span>
                    <h3 style="font-size: 1.2rem; margin: 0.5rem 0;">Styl kaskadowy</h3>
                    <p style="font-size: 0.85rem; color: var(--color-text-muted);">
                        Odwzorowuje drzewa rosnące na stromych klifach górskich, których wierzchołek opada poniżej dna donicy.
                    </p>
                </article>
            </div>
        </section>

        <!-- 6. CYTAT I CTA REJESTRU -->
        <section class="card section-block" style="text-align: center; padding: 3rem 2rem;">
            <blockquote class="serif" style="font-size: 1.45rem; max-width: 680px; margin: 0 auto 1rem;">
                „Bonsai nie należy do nas. Jesteśmy jedynie jego tymczasowymi opiekunami dla kolejnych pokoleń.”
            </blockquote>
            <p style="color: var(--color-text-muted); font-size: 0.9rem; margin-bottom: 1.75rem;">
                Dołącz do elitarnego rejestru opiekunów bonsai KOMOREBI i prowadź pełną dokumentację swoich drzewek.
            </p>
            <a href="/register" class="btn btn-primary">Załóż bezpłatne konto opiekuna</a>
        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-grid">
            <div>
                <div class="brand-logo" style="margin-bottom: 0.5rem;">🌿 KOMOREBI</div>
                <p>System wspomagania pielęgnacji, formowania i diagnostyki drzewek bonsai.</p>
            </div>
            <div>
                <h4>Nawigacja</h4>
                <ul>
                    <li><a href="/">Strona główna</a></li>
                    <li><a href="/projects">Kolekcja drzewek</a></li>
                    <li><a href="/project">Studio formowania</a></li>
                </ul>
            </div>
            <div>
                <h4>Konto</h4>
                <ul>
                    <li><a href="/login">Zaloguj się</a></li>
                    <li><a href="/register">Rejestracja</a></li>
                </ul>
            </div>
            <div>
                <h4>Baza wiedzy</h4>
                <ul>
                    <li>Kalendarz przesadzania</li>
                    <li>Mieszanki glebowe Akadama</li>
                </ul>
            </div>
        </div>
    </footer>

</body>
</html>