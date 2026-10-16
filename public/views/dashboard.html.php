<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komorebi — Kolekcja i Ogród Bonsai</title>
    <link rel="stylesheet" href="/styles/main.css">
</head>
<body>
<div class="app-layout">
    <!-- SIDEBAR (Aside) -->
    <aside class="sidebar">
        <div class="sidebar-top">
            <a href="/projects" class="brand">
                <div class="brand-logo">木</div>
                <span class="brand-name">Komorebi</span>
            </a>

            <div class="iot-station-box">
                <span class="mono-label">STACJA IOT</span>
                <div class="iot-status">
                    <span class="status-dot"></span>
                    <span>ONLINE • 98.4%</span>
                </div>
            </div>

            <nav class="nav-menu">
                <a href="/projects" class="nav-link active-solid">
                    <span>❖</span>
                    <span>Kolekcja i Ogród</span>
                </a>
                <a href="/project" class="nav-link">
                    <span>◈</span>
                    <span>Inspektor 3D & Profil</span>
                </a>
                <a href="/project" class="nav-link">
                    <span>☷</span>
                    <span>Kalendarz Pielęgnacji</span>
                </a>
                <a href="/project" class="nav-link">
                    <span>∿</span>
                    <span>Telemetria Gleby</span>
                </a>
                <a href="/home" class="nav-link">
                    <span>⚙</span>
                    <span>Autoryzacja IoT</span>
                </a>
            </nav>
        </div>

        <div class="sidebar-bottom">
            <div class="sensor-conditions-box">
                <span class="mono-label">WARUNKI SENSOREK</span>
                <div class="sensor-row">
                    <span>TEMP</span>
                    <span style="color: var(--accent-green);">21.4°C</span>
                </div>
                <div class="sensor-row">
                    <span>WILG.</span>
                    <span style="color: var(--accent-lime);">68%</span>
                </div>
                <div class="sensor-row">
                    <span>UV INDEX</span>
                    <span style="color: var(--accent-orange);">UMIARK.</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- GŁÓWNA ZAWARTOŚĆ (Main + Header) -->
    <div class="main-wrapper">
        <header class="top-header">
            <div class="header-zone-pill">
                <span class="mono-label">STREFA:</span>
                <span class="header-zone-value">PAWILON WSCHODNI (ZEWN.)</span>
                <span class="mono-label">▾</span>
            </div>

            <div class="header-right">
                <div class="telemetry-badge">
                    <span class="status-dot"></span>
                    <span>SYNC: 12 SEK TEMU</span>
                </div>
                <a href="/home" class="avatar" title="Profil ogrodnika">WS</a>
            </div>
        </header>

        <main class="dashboard-container">
            <!-- Section: Top Global Garden Status & Executive Metrics Banner -->
            <section class="top-banner-grid">
                <div class="main-status-card">
                    <div class="status-blur"></div>
                    <div class="kanji-watermark">盆栽</div>

                    <div class="status-header-group">
                        <div class="system-tag">
                            <span class="status-dot"></span>
                            <span>SYSTEMY BIOMETRYCZNE ZSYNCHRONIZOWANE</span>
                        </div>
                        <h1 class="status-title">Kolekcja i Ogród Bonsai</h1>
                        <p class="status-desc">
                            Mikroklimat w Pawilonie Wschodnim utrzymuje optymalny punkt rosy. Dwa okazy wymagają korekty nawodnienia oraz sezonowego cięcia pędów.
                        </p>
                        <div class="status-actions">
                            <button class="btn-secondary">
                                <span style="color: var(--accent-green);">●</span>
                                <span>RAPORT TELEMETRII (24H)</span>
                            </button>
                            <a href="/project" class="btn-primary">
                                <span>+</span>
                                <span>Dodaj nowy okaz Bonsai</span>
                            </a>
                        </div>
                    </div>

                    <div class="metrics-grid">
                        <div class="metric-cell">
                            <span class="mono-label">OKAZY AKTYWNE</span>
                            <div class="metric-value-row">
                                <span class="metric-number">4</span>
                                <span class="metric-sub">100% IOT</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 100%;"></div>
                            </div>
                        </div>

                        <div class="metric-cell">
                            <span class="mono-label">ALERTY NAWODNIENIA</span>
                            <div class="metric-value-row">
                                <span class="metric-number" style="color: var(--accent-orange);">1</span>
                                <span class="metric-sub" style="color: var(--accent-orange);">MOMIJI RYU</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 50%; background-color: var(--accent-orange-bold);"></div>
                            </div>
                        </div>

                        <div class="metric-cell">
                            <span class="mono-label">CIĘCIE SEZONOWE</span>
                            <div class="metric-value-row">
                                <span class="metric-number" style="color: var(--accent-lime);">1</span>
                                <span class="metric-sub" style="color: var(--accent-lime);">MEKIRI</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 25%; background-color: var(--accent-lime);"></div>
                            </div>
                        </div>

                        <div class="metric-cell">
                            <span class="mono-label">ŚR. CYKL FOTOSYNTEZY</span>
                            <div class="metric-value-row">
                                <span class="metric-number">94.2</span>
                                <span class="metric-sub">%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 78%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Urgent Pruning & Botanical Action Card -->
                <div class="urgent-card">
                    <div>
                        <div class="urgent-top">
                            <span class="badge-warning">
                                <span class="status-dot orange"></span>
                                PILNY ZABIEG
                            </span>
                            <span class="mono-label">TERMIN: DZIŚ</span>
                        </div>

                        <div class="urgent-specimen">
                            <div class="specimen-icon-box">✂</div>
                            <div>
                                <h2 class="specimen-title">Sosna czarna (Matsu Shin)</h2>
                                <span class="specimen-latin">Pinus thunbergii • #BN-01</span>
                            </div>
                        </div>

                        <p class="urgent-text">
                            Wzrost świeczek osiągnął fazę krytyczną (+4.2 cm). Zalecane skrócenie pędów wiosennych metodą Mekiri w celu stymulacji drugiego rzutu pąków.
                        </p>
                    </div>

                    <div class="urgent-actions">
                        <a href="/project" class="btn-emerald">OTWÓRZ PROTOKÓŁ MEKIRI</a>
                        <button class="btn-icon" title="Odłóż przypomnienie">⏱</button>
                    </div>
                </div>
            </section>

            <!-- Section: Controls Bar: Filter Pills, Sort & Layout Switcher -->
            <section class="controls-bar">
                <div class="filter-pills">
                    <button class="pill active">WSZYSTKIE OKAZY (4)</button>
                    <button class="pill">IGLASTE (2)</button>
                    <button class="pill">LIŚCIASTE (2)</button>
                    <button class="pill alert">
                        <span class="status-dot orange"></span>
                        WYMAGA UWAGI (2)
                    </button>
                </div>

                <div class="sort-controls">
                    <span class="mono-label">SORTUJ:</span>
                    <select class="sort-select">
                        <option>PRIORYTET ZABIEGÓW (PILNE)</option>
                        <option>WILGOTNOŚĆ GLEBY (ROSNĄCO)</option>
                        <option>WIEK OKAZU (MALEJĄCO)</option>
                    </select>
                </div>
            </section>

            <!-- Main Content Workstation: Bonsai Grid & Deep Inspector Side Panel -->
            <section class="workstation-grid">
                <div class="bonsai-cards-grid">
                    <!-- Card 1: Matsu Shin -->
                    <article class="bonsai-card selected">
                        <div class="card-media" style="background-image: url('https://images.unsplash.com/photo-1512428813834-c702c7702b78?auto=format&fit=crop&w=800&q=80');">
                            <div class="card-media-top">
                                <span class="sensor-tag">● CZUJNIK #SN-801</span>
                                <span class="sensor-tag">3D</span>
                            </div>
                            <div class="card-media-bottom">
                                <div>
                                    <span class="mono-label" style="color: var(--accent-green);">PINUS THUNBERGII</span>
                                    <h3 style="font-family: 'Plus Jakarta Sans'; font-size: 20px;">Matsu Shin</h3>
                                </div>
                                <span class="age-badge">42 LATA</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-meta-row">
                                <span>STYL: MOYOGI (SWOBODNY)</span>
                                <span style="color: var(--text-primary);">DONICA: TOKONA</span>
                            </div>
                            <div class="telemetry-strip">
                                <div class="tele-item">
                                    <span class="mono-label">Wilgoć</span>
                                    <span class="tele-val" style="color: var(--accent-green);">64% <small style="font-weight:400; color:var(--text-secondary);">OPT</small></span>
                                </div>
                                <div class="tele-item">
                                    <span class="mono-label">Światło</span>
                                    <span class="tele-val">18.4 <small style="font-weight:400; color:var(--text-secondary);">klx</small></span>
                                </div>
                                <div class="tele-item">
                                    <span class="mono-label">T. korzeni</span>
                                    <span class="tele-val">19.8°C</span>
                                </div>
                            </div>
                            <a href="/project" class="action-bar-tag warning">
                                <span>⚠ CIĘCIE ŚWIECZEK (MEKIRI)</span>
                                <span>➔</span>
                            </a>
                        </div>
                    </article>

                    <!-- Card 2: Momiji Ryu -->
                    <article class="bonsai-card">
                        <div class="card-media" style="background-image: url('https://images.unsplash.com/photo-1599598425947-5202edd56bdb?auto=format&fit=crop&w=800&q=80');">
                            <div class="card-media-top">
                                <span class="sensor-tag muted">● #SN-804</span>
                                <span class="sensor-tag" style="color: var(--accent-orange);">!</span>
                            </div>
                            <div class="card-media-bottom">
                                <div>
                                    <span class="mono-label">ACER PALMATUM</span>
                                    <h3 style="font-family: 'Plus Jakarta Sans'; font-size: 20px;">Momiji Ryu</h3>
                                </div>
                                <span class="age-badge">28 LAT</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-meta-row">
                                <span>STYL: SHAKAN (POCHYŁY)</span>
                                <span style="color: var(--text-primary);">SUBSTRAT: AKADAMA</span>
                            </div>
                            <div class="telemetry-strip">
                                <div class="tele-item">
                                    <span class="mono-label" style="color: var(--accent-orange);">Wilgoć</span>
                                    <span class="tele-val" style="color: var(--accent-orange);">31% <small>NISKA</small></span>
                                </div>
                                <div class="tele-item">
                                    <span class="mono-label">Światło</span>
                                    <span class="tele-val">11.2 <small style="font-weight:400; color:var(--text-secondary);">klx</small></span>
                                </div>
                                <div class="tele-item">
                                    <span class="mono-label">T. korzeni</span>
                                    <span class="tele-val">22.4°C</span>
                                </div>
                            </div>
                            <div class="action-bar-tag">
                                <span><span class="status-dot orange" style="display:inline-block; margin-right:6px;"></span>WYMAGANE ZRASZANIE</span>
                                <a href="/project" style="color: var(--accent-green);">NAWODNIJ</a>
                            </div>
                        </div>
                    </article>

                    <!-- Card 3: Ginseng Shimpaku -->
                    <article class="bonsai-card">
                        <div class="card-media" style="background-image: url('https://images.unsplash.com/photo-1509423350716-97f9360b4e09?auto=format&fit=crop&w=800&q=80');">
                            <div class="card-media-top">
                                <span class="sensor-tag">● CZUJNIK #SN-809</span>
                            </div>
                            <div class="card-media-bottom">
                                <div>
                                    <span class="mono-label">JUNIPERUS CHINENSIS</span>
                                    <h3 style="font-family: 'Plus Jakarta Sans'; font-size: 20px;">Ginseng Shimpaku</h3>
                                </div>
                                <span class="age-badge">65 LAT</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-meta-row">
                                <span>STYL: KENGAI (KASKADA)</span>
                                <span style="color: var(--text-primary);">JIN / SHARI</span>
                            </div>
                            <div class="telemetry-strip">
                                <div class="tele-item">
                                    <span class="mono-label">Wilgoć</span>
                                    <span class="tele-val" style="color: var(--accent-lime);">58% <small style="font-weight:400; color:var(--text-secondary);">STAB.</small></span>
                                </div>
                                <div class="tele-item">
                                    <span class="mono-label">Światło</span>
                                    <span class="tele-val">21.0 <small style="font-weight:400; color:var(--text-secondary);">klx</small></span>
                                </div>
                                <div class="tele-item">
                                    <span class="mono-label">T. korzeni</span>
                                    <span class="tele-val">21.0°C</span>
                                </div>
                            </div>
                            <div class="action-bar-tag">
                                <span style="color: var(--accent-lime);">● DRUTOWANIE STABILNE</span>
                                <span class="mono-label">ZA 14 DNI</span>
                            </div>
                        </div>
                    </article>

                    <!-- Card 4: Zelkova Natsuki -->
                    <article class="bonsai-card">
                        <div class="card-media" style="background-image: url('https://images.unsplash.com/photo-1545241047-6083a3684587?auto=format&fit=crop&w=800&q=80');">
                            <div class="card-media-top">
                                <span class="sensor-tag muted">● CZUJNIK #SN-812</span>
                            </div>
                            <div class="card-media-bottom">
                                <div>
                                    <span class="mono-label">ZELKOVA SERRATA</span>
                                    <h3 style="font-family: 'Plus Jakarta Sans'; font-size: 20px;">Zelkova Natsuki</h3>
                                </div>
                                <span class="age-badge">19 LAT</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-meta-row">
                                <span>STYL: HOKIDACHI</span>
                                <span style="color: var(--text-primary);">KORONA MIOTŁOWA</span>
                            </div>
                            <div class="telemetry-strip">
                                <div class="tele-item">
                                    <span class="mono-label">Wilgoć</span>
                                    <span class="tele-val" style="color: var(--accent-green);">66% <small style="font-weight:400; color:var(--text-secondary);">OPT</small></span>
                                </div>
                                <div class="tele-item">
                                    <span class="mono-label">Światło</span>
                                    <span class="tele-val">15.6 <small style="font-weight:400; color:var(--text-secondary);">klx</small></span>
                                </div>
                                <div class="tele-item">
                                    <span class="mono-label">T. korzeni</span>
                                    <span class="tele-val">18.9°C</span>
                                </div>
                            </div>
                            <div class="action-bar-tag">
                                <span><span class="status-dot" style="display:inline-block; margin-right:6px;"></span>HOMEOSTAZA PEŁNA</span>
                                <span>✓</span>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Aside: Right Docked Inspector -->
                <aside class="inspector-panel">
                    <div class="inspector-header">
                        <div>
                            <div class="system-tag">
                                <span>INSPEKTOR OKAZU</span>
                                <span class="status-dot"></span>
                            </div>
                            <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 20px; margin: 4px 0;">Matsu Shin</h2>
                            <span class="mono-label">Pinus thunbergii • ID #BN-01</span>
                        </div>
                        <div style="display:flex; gap:4px;">
                            <a href="/project" class="btn-icon" style="width:30px; height:34px;" title="Pełny ekran">⛶</a>
                        </div>
                    </div>

                    <!-- Mini Wireframe 3D Viewport Simulation -->
                    <div class="wireframe-viewport">
                        <div class="viewport-overlay-top">
                            <span class="viewport-tag">SIATKA LIDAR 3D</span>
                            <span class="viewport-tag" style="color: var(--text-secondary);">WEKTORY CIĘCIA: 3</span>
                        </div>

                        <svg width="180" height="180" viewBox="0 0 200 200" fill="none">
                            <ellipse cx="100" cy="170" rx="60" ry="12" stroke="#4EDEA3" stroke-opacity="0.4" stroke-dasharray="4 4"/>
                            <path d="M100 170 C95 130, 110 95, 100 40" stroke="#4EDEA3" stroke-width="3" stroke-opacity="0.6"/>
                            <path d="M102 110 C125 105, 145 95, 160 90" stroke="#4EDEA3" stroke-width="2" stroke-opacity="0.5"/>
                            <path d="M98 85 C75 80, 55 70, 45 65" stroke="#4EDEA3" stroke-width="2" stroke-opacity="0.5"/>
                            <circle cx="100" cy="35" r="22" fill="#4EDEA3" fill-opacity="0.08" stroke="#4EDEA3" stroke-opacity="0.5"/>
                            <circle cx="155" cy="88" r="18" fill="#4EDEA3" fill-opacity="0.08" stroke="#4EDEA3" stroke-opacity="0.5"/>
                            <circle cx="48" cy="65" r="16" fill="#4EDEA3" fill-opacity="0.08" stroke="#4EDEA3" stroke-opacity="0.5"/>
                            <circle cx="150" cy="48" r="6" fill="#FFB77D"/>
                            <circle cx="100" cy="22" r="4" fill="#4EDEA3"/>
                        </svg>
                    </div>

                    <!-- Telemetry History Chart (24h Soil Moisture) -->
                    <div class="sparkline-box">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span class="mono-label">WILGOTNOŚĆ GLEBY (24H)</span>
                            <span style="font-family:'JetBrains Mono'; font-size:11px; font-weight:600; color:var(--accent-green);">64.2% (-1.4%)</span>
                        </div>
                        <svg width="100%" height="56" viewBox="0 0 250 56" preserveAspectRatio="none">
                            <path d="M0 35 Q 40 20, 80 28 T 160 22 T 250 32 L 250 56 L 0 56 Z" fill="rgba(78, 222, 163, 0.15)"/>
                            <path d="M0 35 Q 40 20, 80 28 T 160 22 T 250 32" fill="none" stroke="#4EDEA3" stroke-width="2"/>
                            <circle cx="246" cy="32" r="3" fill="#4EDEA3"/>
                        </svg>
                        <div style="display:flex; justify-content:space-between;" class="mono-label">
                            <span>00:00</span>
                            <span>08:00</span>
                            <span>16:00</span>
                            <span>TERAZ</span>
                        </div>
                    </div>

                    <!-- Sensor Pod Specification Grid -->
                    <div class="sensor-pod-grid">
                        <div class="sensor-pod">
                            <span class="mono-label">Sonda pH</span>
                            <span class="tele-val">6.2 Lekko kwaśne</span>
                        </div>
                        <div class="sensor-pod">
                            <span class="mono-label">Przewodność EC</span>
                            <span class="tele-val">1.1 mS/cm</span>
                        </div>
                        <div class="sensor-pod">
                            <span class="mono-label">Cykl nawożenia</span>
                            <span class="tele-val">Aburakasu (Organ.)</span>
                        </div>
                        <div class="sensor-pod">
                            <span class="mono-label">Napowietrzenie</span>
                            <span class="tele-val" style="color: var(--accent-green);">Akadama 80%</span>
                        </div>
                    </div>

                    <!-- Primary Action CTA -->
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <a href="/project" class="btn-primary" style="width:100%; padding:14px;">
                            <span>Otwórz pełne studium i dziennik</span>
                        </a>
                        <button class="btn-secondary" style="width:100%; justify-content:center;">
                            <span>Eksportuj logi sensora CSV</span>
                        </button>
                    </div>
                </aside>
            </section>
        </main>
    </div>
</div>
</body>
</html>