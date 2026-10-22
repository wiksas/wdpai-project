<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moja Kolekcja — KOMOREBI</title>
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
                <li><a href="/projects" class="active">Kolekcja drzewek</a></li>
                <li><a href="/project">Studio formowania i diagnostyka</a></li>
                <li><a href="/login">Logowanie</a></li>
            </ul>
        </nav>
        <a href="/register" class="btn btn-primary">+ Dodaj nowe drzewko</a>
    </header>

    <div class="dashboard-3col">
        <aside class="sidebar-nav" aria-label="Filtry kolekcji">
            <a href="/projects" class="sidebar-link active">
                <span>Wszystkie okazy</span>
                <span>12</span>
            </a>
            <a href="/projects" class="sidebar-link">
                <span>Wymagają podlania</span>
                <span>2</span>
            </a>
            <a href="/projects" class="sidebar-link">
                <span>Drzewka zewnętrzne (Outdoor)</span>
                <span>8</span>
            </a>
            <a href="/projects" class="sidebar-link">
                <span>Drzewka domowe (Indoor)</span>
                <span>4</span>
            </a>
            <hr style="border: none; border-top: 1px solid var(--color-border); margin: 0.5rem 0;">
            <a href="/project" class="sidebar-link">
                <span>Studio formowania</span>
                <span>→</span>
            </a>
            <a href="/" class="sidebar-link">
                <span>Protokół resuscytacji</span>
                <span>→</span>
            </a>
        </aside>

        <main>
            <div class="grid-4" style="margin-bottom: 1.5rem;">
                <div class="card">
                    <small style="color: var(--color-text-muted); text-transform: uppercase; font-size: 0.72rem;">Moja kolekcja</small>
                    <div class="serif" style="font-size: 1.5rem; margin-top: 0.2rem;">12 drzewek</div>
                </div>
                <div class="card">
                    <small style="color: var(--color-text-muted); text-transform: uppercase; font-size: 0.72rem;">Średnia wilgotność</small>
                    <div class="serif" style="font-size: 1.5rem; margin-top: 0.2rem;">64%</div>
                </div>
                <div class="card">
                    <small style="color: var(--color-text-muted); text-transform: uppercase; font-size: 0.72rem;">Zabiegi w tym tyg.</small>
                    <div class="serif" style="font-size: 1.5rem; margin-top: 0.2rem; color: var(--color-accent);">2 pilne</div>
                </div>
                <div class="card">
                    <small style="color: var(--color-text-muted); text-transform: uppercase; font-size: 0.72rem;">Indeks wigoru</small>
                    <div class="serif" style="font-size: 1.5rem; margin-top: 0.2rem; color: var(--color-primary);">94 / 100</div>
                </div>
            </div>

            <div class="grid-2">
                <article class="card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                        <span class="badge badge-green">WIGOR WYSOKI • 68% WILG.</span>
                        <small style="color: var(--color-text-muted);">ID #01</small>
                    </div>
                    <h2 style="font-size: 1.25rem;">Klon Palmowy (Acer palmatum)</h2>
                    <p style="font-size: 0.82rem; color: var(--color-text-muted); margin-bottom: 1rem;">
                        Styl Moyogi • Wiek: 14 lat • Donica Tokoname
                    </p>
                    <p style="font-size: 0.85rem; margin-bottom: 1.25rem;">
                        Ostatnie nawożenie: Biogold Original (8 dni temu). Kolejne uszczykiwanie pędów zaplanowane za 10 dni.
                    </p>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="/project" class="btn btn-primary" style="flex: 1;">Karta zabiegów</a>
                        <button type="button" class="btn btn-outline">Zapisz podlanie</button>
                    </div>
                </article>

                <article class="card" style="border-color: var(--color-accent);">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                        <span class="badge badge-orange">ALARM: 24% WILGOTNOŚCI</span>
                        <small style="color: var(--color-text-muted);">ID #02</small>
                    </div>
                    <h2 style="font-size: 1.25rem;">Sosna Drobnokwiatowa (Pinus parviflora)</h2>
                    <p style="font-size: 0.82rem; color: var(--color-text-muted); margin-bottom: 1rem;">
                        Styl Kengai • Wiek: 22 lata • Stanowisko południowe
                    </p>
                    <p style="font-size: 0.85rem; margin-bottom: 1.25rem;">
                        Przesuszenie wierzchniej warstwy akadamy. Dodatkowo wymagana kontrola drutu miedzianego na konarze głównym.
                    </p>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="/project" class="btn btn-accent" style="flex: 1;">Pilna diagnostyka</a>
                        <button type="button" class="btn btn-outline">Podlej teraz</button>
                    </div>
                </article>

                <article class="card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                        <span class="badge badge-green">STABILNY • 59% WILG.</span>
                        <small style="color: var(--color-text-muted);">ID #03</small>
                    </div>
                    <h2 style="font-size: 1.25rem;">Wiąz Drobnolistny (Ulmus parvifolia)</h2>
                    <p style="font-size: 0.82rem; color: var(--color-text-muted); margin-bottom: 1rem;">
                        Styl Hokidachi (Miotlasty) • Wiek: 9 lat
                    </p>
                    <p style="font-size: 0.85rem; margin-bottom: 1.25rem;">
                        Bardzo silny przyrost korony po cięciu prześwietlającym. Zalecana korekta młodych pędów wierzchołkowych.
                    </p>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="/project" class="btn btn-primary" style="flex: 1;">Karta zabiegów</a>
                        <button type="button" class="btn btn-outline">Zapisz podlanie</button>
                    </div>
                </article>

                <article class="card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                        <span class="badge badge-green">STABILNY • 63% WILG.</span>
                        <small style="color: var(--color-text-muted);">ID #04</small>
                    </div>
                    <h2 style="font-size: 1.25rem;">Jałowiec Chiński (Juniperus chinensis)</h2>
                    <p style="font-size: 0.82rem; color: var(--color-text-muted); margin-bottom: 1rem;">
                        Styl Shari / Martwe drewno Jin • Wiek: 31 lat
                    </p>
                    <p style="font-size: 0.85rem; margin-bottom: 1.25rem;">
                        Zakonserwowano martwe drewno płynem Jin. Ekspozycja na pełnym słońcu sprzyja zagęszczaniu łusek.
                    </p>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="/project" class="btn btn-primary" style="flex: 1;">Karta zabiegów</a>
                        <button type="button" class="btn btn-outline">Zapisz podlanie</button>
                    </div>
                </article>
            </div>
        </main>

        <aside class="card" aria-labelledby="alerts-heading">
            <h3 id="alerts-heading" style="font-size: 1.15rem; margin-bottom: 1rem;">Dzisiejsze priorytety</h3>

            <div style="background-color: var(--color-accent-soft); border-left: 3px solid var(--color-accent); padding: 0.85rem; border-radius: var(--radius-sm); font-size: 0.83rem; margin-bottom: 1rem;">
                <strong style="color: var(--color-accent); display: block;">Sosna Drobnokwiatowa (#02)</strong>
                Wymaga podlania metodą zanurzeniową przed godz. 18:00.
            </div>

            <div style="font-size: 0.84rem; display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
                <div style="padding-bottom: 0.6rem; border-bottom: 1px solid var(--color-border);">
                    <strong>Piątek, 10:00</strong>
                    <p style="color: var(--color-text-muted);">Kontrola wrastania drutu: Klon Palmowy</p>
                </div>
                <div style="padding-bottom: 0.6rem; border-bottom: 1px solid var(--color-border);">
                    <strong>Niedziela, 09:00</strong>
                    <p style="color: var(--color-text-muted);">Aplikacja nawozu organicznego Biogold</p>
                </div>
            </div>

            <a href="/project" class="btn btn-outline" style="width: 100%;">Przejdź do Studia Formowania</a>
        </aside>
    </div>

</body>
</html>