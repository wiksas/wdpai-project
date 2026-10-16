<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komorebi IoT — Autoryzacja Ogrodnika</title>
    <link rel="stylesheet" href="/styles/main.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-hero">
        <div class="brand" style="padding: 0;">
            <div class="brand-logo">木</div>
            <span class="brand-name">Komorebi Bonsai IoT</span>
        </div>

        <div style="max-width: 520px; display:flex; flex-direction:column; gap:16px;">
            <div class="system-tag">
                <span class="status-dot"></span>
                <span>TELEMETRIA BOTANICZNA GENERACJI IV</span>
            </div>
            <h1 style="font-family: 'Plus Jakarta Sans'; font-size: 38px; line-height: 1.2;">
                Harmonijny Ogród Wspomagany Technologią IoT
            </h1>
            <p class="status-desc">
                Monitoruj wilgotność substratu Akadama, przewodność elektrolityczną korzeni oraz harmonogram cięcia Mekiri w czasie rzeczywistym.
            </p>
        </div>

        <div class="mono-label">
            KOMOREBI SYSTEMS • WERSJA STACJI 4.2.0 • KRAKÓW
        </div>
    </div>

    <div class="auth-form-wrapper">
        <form class="auth-card" action="/projects" method="GET">
            <div>
                <span class="mono-label" style="color: var(--accent-green);">DOSTĘP AUTORYZOWANY</span>
                <h2 style="font-family: 'Plus Jakarta Sans'; font-size: 24px; margin-top: 4px;">Zaloguj do stacji ogrodowej</h2>
            </div>

            <div class="form-group">
                <label class="mono-label">IDENTYFIKATOR OGRODNIKA / EMAIL</label>
                <input type="email" name="email" class="form-input" value="wiktor.sasnal@komorebi.jp" required>
            </div>

            <div class="form-group">
                <label class="mono-label">KLUCZ DOSTĘPU STACJI</label>
                <input type="password" name="password" class="form-input" value="••••••••••••" required>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 14px; margin-top: 8px;">
                Połącz ze stacją telemetryczną ➔
            </button>

            <div class="sensor-conditions-box" style="margin-top: 8px;">
                <div class="sensor-row">
                    <span>STATUS BRAMY MQTT</span>
                    <span style="color: var(--accent-green);">AKTYWNA (12ms)</span>
                </div>
                <div class="sensor-row">
                    <span>AKTYWNE SONDY GLEBOWE</span>
                    <span>4 / 4 ONLINE</span>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>