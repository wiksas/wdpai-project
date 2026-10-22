<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio Formowania i Diagnostyka — KOMOREBI</title>
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
                <li><a href="/">Przewodnik</a></li>
                <li><a href="/projects">Kolekcja drzewek</a></li>
                <li><a href="/project" class="active">Studio formowania i diagnostyka</a></li>
                <li><a href="/login">Logowanie</a></li>
            </ul>
        </nav>
        <a href="/projects" class="btn btn-outline">← Powrót do kolekcji</a>
    </header>

    <div class="studio-2col">
        <aside class="sidebar-nav" aria-label="Moduły studia">
            <a href="#kalendarz" class="sidebar-link active">1. Kalendarz sezonowy</a>
            <a href="#harmonogram" class="sidebar-link">2. Harmonogram cięcia</a>
            <a href="#drutowanie" class="sidebar-link">3. Technika drutowania</a>
            <a href="#diagnostyka" class="sidebar-link">4. Diagnostyka zdrowia</a>
        </aside>

        <main style="display: flex; flex-direction: column; gap: 2rem;">

            <section class="card" style="background: linear-gradient(135deg, #ffffff 0%, #f1efe7 100%);">
                <span class="badge badge-green">KOMOREBI • MODUŁ BOTANICZNY</span>
                <h1 style="font-size: 2rem; margin: 0.5rem 0;">Studio Formowania, Cięcia i Drutowania</h1>
                <p style="color: var(--color-text-muted); max-width: 760px;">
                    Precyzyjne planowanie zabiegów strukturalnych zgodnie z cyklem krążenia soków roślinnych. Wybierz miesiąc oraz technikę, aby sprawdzić dopuszczalne obciążenie drzewka.
                </p>
            </section>

            <section id="kalendarz" class="card">
                <span class="badge badge-green">CYKL ROCZNY</span>
                <h2 style="font-size: 1.45rem; margin-top: 0.35rem;">Kiedy ciąć, a kiedy dać drzewku spokój?</h2>

                <div class="months-grid">
                    <div class="month-box"><strong>Styczeń</strong><br>Spoczynek zimowy</div>
                    <div class="month-box"><strong>Luty</strong><br>Cięcie prześwietlające</div>
                    <div class="month-box active"><strong>Marzec</strong><br>Przesadzanie i korzenie</div>
                    <div class="month-box active"><strong>Kwiecień</strong><br>Wiosenne uszczykiwanie</div>
                    <div class="month-box"><strong>Maj</strong><br>Selekcja pędów</div>
                    <div class="month-box"><strong>Czerwiec</strong><br>Defoliacja liściastych</div>
                    <div class="month-box"><strong>Lipiec</strong><br>Ochrona przed upałem</div>
                    <div class="month-box"><strong>Sierpień</strong><br>Cięcie korygujące</div>
                    <div class="month-box"><strong>Wrzesień</strong><br>Nawożenie jesienne PK</div>
                    <div class="month-box"><strong>Październik</strong><br>Drutowanie iglastych</div>
                    <div class="month-box"><strong>Listopad</strong><br>Zabezpieczenie mrozowe</div>
                    <div class="month-box"><strong>Grudzień</strong><br>Zimowanie (0–5°C)</div>
                </div>
            </section>

            <section id="harmonogram" class="card">
                <div class="section-header" style="margin-bottom: 1rem;">
                    <div>
                        <h2 style="font-size: 1.45rem;">Kalendarz intensywności zabiegów (Wiosna – Jesień)</h2>
                        <p style="font-size: 0.85rem; color: var(--color-text-muted);">Rozkład bezpiecznych okien biologicznych dla gatunków strefy umiarkowanej.</p>
                    </div>
                    <span class="badge badge-green">GATUNKI OUTDOOR</span>
                </div>

                <div class="gantt-row">
                    <strong>Przesadzanie i redukcja korzeni</strong>
                    <div class="gantt-bar-track"><div class="gantt-bar-fill" style="width: 30%; margin-left: 15%;"></div></div>
                </div>
                <div class="gantt-row">
                    <strong>Cięcie strukturalne konarów</strong>
                    <div class="gantt-bar-track"><div class="gantt-bar-fill" style="width: 25%; margin-left: 10%;"></div></div>
                </div>
                <div class="gantt-row">
                    <strong>Uszczykiwanie młodych przyrostów</strong>
                    <div class="gantt-bar-track"><div class="gantt-bar-fill" style="width: 50%; margin-left: 30%;"></div></div>
                </div>
                <div class="gantt-row">
                    <strong>Drutowanie drutem miedzianym</strong>
                    <div class="gantt-bar-track"><div class="gantt-bar-fill accent" style="width: 35%; margin-left: 55%;"></div></div>
                </div>
            </section>

            <section id="drutowanie" class="card">
                <h2 style="font-size: 1.45rem; margin-bottom: 1rem;">Zasady zakładania drutu pod kątem 45°</h2>
                <div class="grid-3">
                    <div style="background-color: var(--color-bg-main); padding: 1.15rem; border-radius: var(--radius-sm);">
                        <strong>Dobór grubości drutu</strong>
                        <p style="font-size: 0.84rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                            Stosuj drut o średnicy równej 1/3 grubości wyginanej gałęzi (aluminium anodowane dla liściastych, miedź wyżarzana dla iglastych).
                        </p>
                    </div>
                    <div style="background-color: var(--color-bg-main); padding: 1.15rem; border-radius: var(--radius-sm);">
                        <strong>Kotwiczenie o pień</strong>
                        <p style="font-size: 0.84rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                            Jeden odcinek drutu powinien stabilizować dwie sąsiednie gałęzie znajdujące się na różnych wysokościach.
                        </p>
                    </div>
                    <div style="background-color: var(--color-bg-main); padding: 1.15rem; border-radius: var(--radius-sm);">
                        <strong>Ochrona kory przed wrastaniem</strong>
                        <p style="font-size: 0.84rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                            U klonów palmowych zdejmuj uzwojenie już po 6–8 tygodniach wiosennego przyrostu.
                        </p>
                    </div>
                </div>
            </section>

            <section id="diagnostyka" class="card">
                <span class="badge badge-orange">KOMOREBI • MODUŁ KLINICZNY</span>
                <h2 style="font-size: 1.65rem; margin: 0.4rem 0 0.5rem;">Diagnostyka Zdrowia Drzewek</h2>
                <p style="color: var(--color-text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
                    Porównaj objawy widoczne na liściach i korze z matrycą patogenów oraz błędów środowiskowych.
                </p>

                <div class="grid-3">
                    <div style="border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 1.15rem;">
                        <span class="badge badge-orange">CHLOROZA LIŚCI</span>
                        <h3 style="font-size: 1.05rem; margin: 0.5rem 0;">Żółknięcie blaszki przy zielonych nerwach</h3>
                        <p style="font-size: 0.83rem; color: var(--color-text-muted);">
                            <strong>Przyczyna:</strong> Niedobór chelatu żelaza spowodowany zbyt wysokim pH twardej wody kranowej.<br>
                            <strong>Zalecenie:</strong> Zakwaszenie podłoża i oprysk dolistny żelazem.
                        </p>
                    </div>

                    <div style="border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 1.15rem;">
                        <span class="badge badge-orange">SZKODNIKI</span>
                        <h3 style="font-size: 1.05rem; margin: 0.5rem 0;">Drobna pajęczynka i matowienie igieł</h3>
                        <p style="font-size: 0.83rem; color: var(--color-text-muted);">
                            <strong>Przyczyna:</strong> Inwazja przędziorka chmielowca wskutek suchego powietrza w mieszkaniu.<br>
                            <strong>Zalecenie:</strong> Dwukrotny oprysk preparatem olejowym w odstępie 7 dni.
                        </p>
                    </div>

                    <div style="border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 1.15rem;">
                        <span class="badge badge-green">NEKROZA BRZEGOWA</span>
                        <h3 style="font-size: 1.05rem; margin: 0.5rem 0;">Zasychanie końcówek liści klonu</h3>
                        <p style="font-size: 0.83rem; color: var(--color-text-muted);">
                            <strong>Przyczyna:</strong> Zbyt silny, wysuszający wiatr na balkonie lub przenawożenie azotem.<br>
                            <strong>Zalecenie:</strong> Osłonięcie stanowiska siatką cieniującą 30%.
                        </p>
                    </div>
                </div>
            </section>

        </main>
    </div>

</body>
</html>