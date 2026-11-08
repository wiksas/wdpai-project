<?php
/**
 * Czujnik wilgotności okazu: dodawanie, token (pokazywany raz), ostatnie odczyty, kalibracja, wskazówka zakupowa.
 * Wymaga: $tree, $treeUrl, $sensor (albo null), $sensorToken (albo null), $sensorEndpoint, $e.
 */
use App\Support\PolishDate;

$sensorAgo = static function (int $timestamp): string {
    $seconds = max(0, PolishDate::now()->getTimestamp() - $timestamp);

    return match (true) {
        $seconds < 90 => 'przed chwilą',
        $seconds < 3600 => (int) round($seconds / 60) . ' min temu',
        $seconds < 86400 => (int) round($seconds / 3600) . ' godz. temu',
        default => PolishDate::dayMonthYear(PolishDate::now()->setTimestamp($timestamp)),
    };
};
$readings = $sensor['readings'] ?? [];
$latest = $readings[0] ?? null;
$calibrated = $sensor !== null && $sensor['dryRaw'] !== null && $sensor['wetRaw'] !== null;
?>
<section class="passport-sensor" id="passport-sensor" aria-labelledby="passport-sensor-title">
    <header class="passport-section-header">
        <h2 class="passport-section-header__title" id="passport-sensor-title">Czujnik wilgotności</h2>
        <?php if ($sensor !== null): ?>
            <span class="passport-section-header__count"><?= $e($sensor['name'] !== '' ? $sensor['name'] : 'podłączony') ?></span>
        <?php endif; ?>
    </header>

    <?php if ($sensor === null): ?>
        <p class="passport-sensor__lead">Czujnik w donicy wysyła odczyty do aplikacji. Z odczytem okaz pokazuje prawdziwą wilgotność podłoża zamiast wartości startowej, a suche podłoże trafia do listy zabiegów, powiadomień i harmonogramu.</p>
        <form action="<?= $treeUrl ?>/sensor" method="POST" class="passport-sensor__add">
            <?php include __DIR__ . '/csrf.html.php'; ?>
            <div class="field">
                <label class="field__label" for="sensor-name">Nazwa czujnika <span class="passport-sensor__optional">(opcjonalnie)</span></label>
                <input class="input" type="text" id="sensor-name" name="name" maxlength="40" placeholder="np. ESP32 przy oknie">
            </div>
            <button type="submit" class="btn btn--tonal">
                <span class="ms" aria-hidden="true">sensors</span>
                Dodaj czujnik
            </button>
        </form>
    <?php else: ?>
        <?php if ($sensorToken !== null): ?>
            <div class="passport-sensor__token" role="status">
                <p class="passport-sensor__token-title">Token czujnika (pokazany tylko teraz)</p>
                <code class="passport-sensor__code"><?= $e($sensorToken) ?></code>
                <p class="passport-sensor__hint">Wpisz go w urządzeniu. W bazie zostaje tylko jego skrót, więc po odświeżeniu strony nie da się go odczytać. Gdy go zgubisz, wystaw nowy.</p>
                <p class="passport-sensor__hint">Adres do wysyłania odczytów: <code><?= $e($sensorEndpoint) ?></code>. Urządzenie w sieci domowej potrzebuje adresu IP komputera zamiast <code>localhost</code> (np. <code>http://192.168.0.12:8080/api/sensor/readings</code>).</p>
                <pre class="passport-sensor__curl">curl -X POST <?= $e($sensorEndpoint) ?> \
  -H "Authorization: Bearer <?= $e($sensorToken) ?>" \
  -H "Content-Type: application/json" \
  -d '{"raw": 1850}'</pre>
            </div>
        <?php endif; ?>

        <?php if ($latest === null): ?>
            <p class="passport-sensor__status">Czekamy na pierwszy odczyt. Wyślij go z urządzenia lub poleceniem <code>curl</code> z tokenem.</p>
        <?php else: ?>
            <p class="passport-sensor__status">
                Ostatni odczyt <?= $e($sensorAgo($latest['at'])) ?>:
                <strong><?= $latest['percent'] !== null ? (int) $latest['percent'] . '%' : 'wartość surowa ' . (int) $latest['raw'] ?></strong>
            </p>
            <?php if (!$calibrated): ?>
                <p class="passport-sensor__hint">Czujnik nie jest skalibrowany, więc aplikacja nie zna jeszcze wilgotności w procentach. Kalibracja to dwa kliknięcia: gdy sonda tkwi w suchym podłożu, zapisz odczyt jako suche; po obfitym podlaniu, gdy odczyt się ustabilizuje, zapisz go jako mokre.</p>
            <?php else: ?>
                <p class="passport-sensor__hint">Kalibracja: sucho = <?= (int) $sensor['dryRaw'] ?>, mokro = <?= (int) $sensor['wetRaw'] ?>.</p>
            <?php endif; ?>

            <form action="<?= $treeUrl ?>/sensor/calibrate" method="POST" class="passport-sensor__calibrate">
                <?php include __DIR__ . '/csrf.html.php'; ?>
                <button type="submit" name="point" value="dry" class="btn btn--tonal btn--sm">Ostatni odczyt = suche (0%)</button>
                <button type="submit" name="point" value="wet" class="btn btn--tonal btn--sm">Ostatni odczyt = mokre (100%)</button>
            </form>

            <ul class="passport-sensor__readings" aria-label="Ostatnie odczyty">
                <?php foreach ($readings as $reading): ?>
                    <li>
                        <span><?= $e($sensorAgo($reading['at'])) ?></span>
                        <span><?= $reading['percent'] !== null ? (int) $reading['percent'] . '%' : (int) $reading['raw'] ?></span>
                        <?php if ($reading['percent'] !== null): ?><span class="passport-sensor__raw">surowo <?= (int) $reading['raw'] ?></span><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="passport-sensor__manage">
            <form action="<?= $treeUrl ?>/sensor" method="POST">
                <?php include __DIR__ . '/csrf.html.php'; ?>
                <button type="submit" class="btn btn--ghost btn--sm">Wystaw nowy token</button>
            </form>
            <details class="passport-sensor__remove">
                <summary class="btn btn--ghost btn--sm">Usuń czujnik</summary>
                <form action="<?= $treeUrl ?>/sensor/delete" method="POST">
                    <?php include __DIR__ . '/csrf.html.php'; ?>
                    <p class="passport-sensor__hint">Odczyty zostaną skasowane, a okaz wróci do wartości startowej.</p>
                    <button type="submit" class="btn btn--tertiary btn--sm">Tak, usuń czujnik</button>
                </form>
            </details>
        </div>
    <?php endif; ?>

    <details class="passport-sensor__buy">
        <summary>Jaki czujnik kupić</summary>
        <ul>
            <li><strong>Sprawdzony wybór:</strong> płytka ESP32 z Wi-Fi i czujnik pojemnościowy wilgotności gleby (Capacitive Soil Moisture Sensor v1.2 albo DFRobot SEN0193). Razem ok. 40-70 zł, zasilanie z USB lub akumulatora. ESP32 samo wysyła odczyty do aplikacji; przykładowy kod jest w <code>docs/czujnik-wilgotnosci.md</code>.</li>
            <li><strong>Omiń czujniki rezystancyjne</strong> (widełki FC-28, YL-69): w mokrej ziemi korodują w kilka tygodni i po miesiącu pokazują bzdury.</li>
            <li><strong>Gotowe czujniki Xiaomi Mi Flora, Zigbee lub Tuya</strong> nie wyślą odczytu do tej aplikacji bez dodatkowej bramki (np. Home Assistant). To więcej konfiguracji, a nie gotowe rozwiązanie.</li>
            <li><strong>Bonsai ma kłopotliwe podłoże:</strong> w akadamie, pumeksie i lawie czujnik łatwo łapie powietrze, więc wetknij sondę w strefę korzeni i dociśnij podłoże. Skalibruj każdy czujnik osobno: odczyt zależy od donicy i mieszanki.</li>
        </ul>
    </details>
</section>
