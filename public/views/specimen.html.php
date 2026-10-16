<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matsu Shin — Studium Okazu & Telemetria</title>
    <link rel="stylesheet" href="/styles/main.css">
</head>
<body>
<div class="app-layout">
    <aside class="sidebar">
        <div class="sidebar-top">
            <a href="/projects" class="brand">
                <div class="brand-logo">木</div>
                <span class="brand-name">Komorebi</span>
            </a>

            <div class="iot-station-box">
                <span class="mono-label">AKTYWNA SONDA</span>
                <div class="iot-status">
                    <span class="status-dot"></span>
                    <span>#SN-801 • MATSU SHIN</span>
                </div>
            </div>

            <nav class="nav-menu">
                <a href="/projects" class="nav-link">
                    <span>❖</span>
                    <span>Kolekcja i Ogród</span>
                </a>
                <a href="/project" class="nav-link active-solid">
                    <span>◈</span>
                    <span>Inspektor 3D & Profil</span>
                </a>
                <a href="/project" class="nav-link">
                    <span>☷</span>
                    <span>Kalendarz Pielęgnacji</span>
                </a>
            </nav>
        </div>

        <div class="sidebar-bottom">
            <a href="/projects" class="btn-secondary" style="width:100%; justify-content:center;">
                ← Wróć do kolekcji
            </a>
        </div>
    </aside>

    <div class="main-wrapper">
        <header class="top-header">
            <div class="header-zone-pill">
                <span class="mono-label">OKAZ #BN-01:</span>
                <span class="header-zone-value">SOSNA CZARNA (PINUS THUNBERGII)</span>
            </div>
            <div class="header-right">
                <a href="/projects" class="btn-secondary">Zamknij inspektor</a>
            </div>
        </header>

        <main class="dashboard-container">
            <div class="metrics-grid" style="margin-top: 0;">
                <div class="metric-cell">
                    <span class="mono-label">WILGOTNOŚĆ SUBSTRATU</span>
                    <div class="metric-value-row">
                        <span class="metric-number" style="color: var(--accent-green);">64.2%</span>
                        <span class="metric-sub">OPTYMALNA</span>
                    </div>
                    <div class="progress-bar"><div class="progress-fill" style="width: 64%;"></div></div>
                </div>
                <div class="metric-cell">
                    <span class="mono-label">NATĘŻENIE PAR</span>
                    <div class="metric-value-row">
                        <span class="metric-number">680</span>
                        <span class="metric-sub">µmol/m²/s</span>
                    </div>
                    <div class="progress-bar"><div class="progress-fill" style="width: 75%;"></div></div>
                </div>
                <div class="metric-cell">
                    <span class="mono-label">TEMPERATURA BRYŁY</span>
                    <div class="metric-value-row">
                        <span class="metric-number">19.8</span>
                        <span class="metric-sub">°C</span>
                    </div>
                    <div class="progress-bar"><div class="progress-fill" style="width: 55%;"></div></div>
                </div>
                <div class="metric-cell">
                    <span class="mono-label">ODCZYN GLEBY (PH)</span>
                    <div class="metric-value-row">
                        <span class="metric-number" style="color: var(--accent-lime);">6.2</span>
                        <span class="metric-sub">STABILNE</span>
                    </div>
                    <div class="progress-bar"><div class="progress-fill" style="width: 82%;"></div></div>
                </div>
            </div>

            <div class="workstation-grid">
                <div class="main-status-card" style="min-height: 420px;">
                    <div class="status-header-group">
                        <span class="system-tag">● PROTOKÓŁ ZABIEGOWY • MEKIRI</span>
                        <h1 class="status-title">Korekta pędów wiosennych i drutowanie</h1>
                        <p class="status-desc">
                            Usunięcie silnych świeczek w partii wierzchołkowej (strefa Apikalna A1-A3) w celu wyrównania dystrybucji auksyn do dolnych konarów.
                        </p>
                    </div>

                    <div class="wireframe-viewport" style="height: 280px; margin-top: 20px;">
                        <div class="viewport-overlay-top">
                            <span class="viewport-tag">MODEL 3D KORONY • WIDOK FRONTALNY</span>
                        </div>
                        <svg width="220" height="220" viewBox="0 0 200 200" fill="none">
                            <ellipse cx="100" cy="175" rx="75" ry="14" stroke="#4EDEA3" stroke-dasharray="4 4"/>
                            <path d="M100 175 C92 125, 112 90, 100 30" stroke="#4EDEA3" stroke-width="4"/>
                            <path d="M102 110 C130 105, 155 95, 170 85" stroke="#4EDEA3" stroke-width="2.5"/>
                            <path d="M98 85 C70 80, 45 70, 35 60" stroke="#4EDEA3" stroke-width="2.5"/>
                            <circle cx="100" cy="30" r="24" fill="rgba(78,222,163,0.12)" stroke="#4EDEA3"/>
                            <circle cx="165" cy="85" r="20" fill="rgba(255,183,125,0.18)" stroke="#FFB77D"/>
                            <circle cx="38" cy="60" r="18" fill="rgba(78,222,163,0.12)" stroke="#4EDEA3"/>
                        </svg>
                    </div>
                </div>

                <aside class="inspector-panel">
                    <span class="mono-label" style="color: var(--accent-green);">HISTORIA ZABIEGÓW</span>
                    <div class="sensor-pod">
                        <span class="mono-label">16 PAŹ 2026 • PLANOWANE</span>
                        <span class="tele-val" style="color: var(--accent-orange);">Cięcie świeczek (Mekiri)</span>
                    </div>
                    <div class="sensor-pod">
                        <span class="mono-label">02 WRZ 2026 • WYKONANO</span>
                        <span class="tele-val">Aplikacja nawozu Biogold</span>
                    </div>
                    <div class="sensor-pod">
                        <span class="mono-label">14 KWI 2026 • WYKONANO</span>
                        <span class="tele-val">Przesadzanie do donicy Tokoname</span>
                    </div>
                    <a href="/projects" class="btn-primary" style="width:100%; margin-top:8px;">
                        Zatwierdź wykonanie zabiegu
                    </a>
                </aside>
            </div>
        </main>
    </div>
</div>
</body>
</html>