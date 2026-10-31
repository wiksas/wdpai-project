/**
 * Studio Formowania: przełączanie stylów i gatunków bez przeładowania, kalkulator drutu,
 * wzorzec kąta owijania i symulator balansu korony. Wymaga app.js (`window.Komorebi`).
 * Bez JS linki stylów/gatunków działają jako zwykłe `?style=…&species=…`.
 */
(() => {
    'use strict';

    const K = window.Komorebi;
    if (!K) {
        return;
    }

    const doc = document;
    const $ = (selector, root = doc) => root.querySelector(selector);
    const $$ = (selector, root = doc) => [...root.querySelectorAll(selector)];
    const pct = (value, min, max) => `${(((value - min) / (max - min)) * 100).toFixed(1)}%`;

    /** Płynna zmiana liczby w elemencie. */
    function tween(element, from, to, format) {
        if (!element) {
            return;
        }
        if (K.reduceMotion || from === to) {
            element.textContent = format(to);
            return;
        }
        const start = performance.now();
        const duration = 450;
        const step = (now) => {
            const t = Math.min(1, (now - start) / duration);
            element.textContent = format(Math.round(from + (to - from) * (1 - (1 - t) ** 3)));
            if (t < 1) {
                requestAnimationFrame(step);
            }
        };
        requestAnimationFrame(step);
    }

    // --- Style i gatunki: podmiana fragmentów ----------------------------------------------------------
    let navigating = false;

    async function navigate(url, focusSelector) {
        if (navigating) {
            return;
        }
        navigating = true;
        try {
            const next = await K.fetchDocument(url);
            K.swap(next, ['.studio-styles', '.studio-detail', '.studio-tabs', '.studio-calendar']);
            history.replaceState(null, '', url);
            K.play($(focusSelector), 'fx-in');
        } catch {
            window.location.href = url;
        } finally {
            navigating = false;
        }
    }

    doc.addEventListener('click', (event) => {
        const style = event.target.closest('[data-style]');
        const species = event.target.closest('[data-species]');
        const link = style ?? species;
        if (!link || event.metaKey || event.ctrlKey || event.shiftKey) {
            return;
        }
        event.preventDefault();
        if (link.getAttribute('aria-current') === 'true') {
            return;
        }

        const url = new URL(link.getAttribute('href'), location.origin);
        url.hash = '';
        navigate(url.pathname + url.search, style ? '.studio-detail' : '.studio-calendar');
    });

    doc.addEventListener('click', (event) => {
        if (event.target.closest('[data-print]')) {
            window.print();
        }
    });

    // --- Kalkulator grubości drutu ------------------------------------------------------------------
    const WIRE = {
        aluminium: { label: 'Anodyzowane Aluminium', sizes: [1, 1.5, 2, 2.5, 3, 4, 5, 6], factor: 1 / 3 },
        copper: { label: 'Miedź wyżarzana', sizes: [0.8, 1, 1.5, 2, 2.5, 3, 4, 5], factor: 1 / 4 },
    };

    function updateWire(form) {
        const input = $('[data-wire-diameter]', form);
        const diameter = Number(input.value);
        const alloy = WIRE[new FormData(form).get('alloy')] ?? WIRE.aluminium;
        const ideal = diameter * alloy.factor;
        const size = alloy.sizes.reduce((best, item) => (Math.abs(item - ideal) < Math.abs(best - ideal) ? item : best), alloy.sizes[0]);

        const time = diameter < 6 ? '2-4 miesiące' : diameter < 15 ? '6-9 miesięcy' : diameter < 25 ? '8-12 miesięcy' : '10-14 miesięcy';
        const anchor = diameter < 6 ? 'Pojedyncza spirala' : diameter < 15 ? 'Podwójna spirala (Jin Anchor)' : diameter < 25 ? 'Podwójny drut + kotwa w donicy' : 'Dwa druty równolegle + rafia';

        $('[data-wire-output]', form).textContent = `${diameter.toFixed(1)} mm`;
        input.style.setProperty('--value', pct(diameter, Number(input.min), Number(input.max)));
        const result = $('.studio-result', form);
        result.classList.add('is-updating');
        $('[data-wire-size]', form).textContent = `${size.toFixed(1)} mm`;
        $('[data-wire-alloy]', form).textContent = alloy.label;
        $('[data-wire-time]', form).textContent = time;
        $('[data-wire-anchor]', form).textContent = anchor;
        setTimeout(() => result.classList.remove('is-updating'), 160);
    }

    doc.addEventListener('input', (event) => {
        const form = event.target.closest('[data-wire-calc]');
        if (form) {
            updateWire(form);
        }
    });
    doc.addEventListener('submit', (event) => {
        if (event.target.closest('[data-wire-calc]')) {
            event.preventDefault();
        }
    });

    // --- Wzorzec kąta owijania ----------------------------------------------------------------------
    function updateAngle(input) {
        const angle = Number(input.value);
        const dx = 48 * Math.tan((angle * Math.PI) / 180);
        const pitch = Math.max(16, (75 * dx) / 48);
        const NS = 'http://www.w3.org/2000/svg';
        const wires = [];

        for (let x = 71 - pitch; x < 460; x += pitch) {
            wires.push(x);
        }

        const draw = (group, build) => {
            group.replaceChildren(
                ...wires.map((x) => {
                    const line = doc.createElementNS(NS, 'line');
                    build(line, x);
                    return line;
                }),
            );
        };
        draw($('[data-angle-wire]'), (line, x) => {
            line.setAttribute('x1', x.toFixed(1));
            line.setAttribute('y1', '136');
            line.setAttribute('x2', (x + dx).toFixed(1));
            line.setAttribute('y2', '88');
        });
        draw($('[data-angle-under]'), (line, x) => {
            line.setAttribute('x1', (x + dx + pitch / 2 - dx / 2).toFixed(1));
            line.setAttribute('y1', '88');
            line.setAttribute('x2', (x + dx + pitch / 2 + dx * 0.04).toFixed(1));
            line.setAttribute('y2', '136');
        });

        // Znacznik kąta przy zwoju najbliższym środka rysunku
        const tops = wires.map((x) => x + dx);
        const near = tops.reduce((best, x) => (Math.abs(x - 219) < Math.abs(best - 219) ? x : best), tops[0]);
        const measure = $('[data-angle-measure]');
        const x1 = Math.max(60, Math.min(near, 380));
        measure.setAttribute('x1', x1.toFixed(1));
        measure.setAttribute('x2', (x1 + Math.min(dx, 90)).toFixed(1));
        const label = $('[data-angle-label]');
        label.setAttribute('x', (x1 + Math.min(dx, 90) / 2).toFixed(1));
        label.textContent = `${angle}°`;

        $('[data-angle-output]').textContent = `${angle} stopni`;
        input.style.setProperty('--value', pct(angle, Number(input.min), Number(input.max)));

        const tight = angle < 30;
        const loose = angle > 55;
        const badge = $('[data-angle-badge]');
        badge.textContent = tight ? `Kąt zbyt ostry (${angle}°)` : loose ? `Kąt zbyt łagodny (${angle}°)` : `Kąt optymalny (${angle}°)`;
        badge.classList.toggle('is-warn', tight || loose);

        const note = $('[data-angle-note]');
        note.classList.toggle('is-warn', tight || loose);
        $('.ms', note).textContent = tight || loose ? 'warning' : 'check_circle';
        $('span:last-child', note).innerHTML = tight
            ? '<strong>Splot zbyt ciasny:</strong> zwoje duszą wiązki sitowe i szybko wrastają w korę. Zwiększ kąt do ok. 45°.'
            : loose
              ? '<strong>Splot zbyt luźny:</strong> drut nie ma siły trzymania i gałąź wróci na miejsce. Zmniejsz kąt do ok. 45°.'
              : '<strong>Kąt prawidłowy:</strong> Zapewnia optymalne przełożenie wektora gięcia bez punktowego nacisku na wiązki sitowe.';
    }

    doc.addEventListener('input', (event) => {
        const input = event.target.closest('[data-angle-input]');
        if (input) {
            updateAngle(input);
        }
    });

    // --- Symulator balansu korony ---------------------------------------------------------------------
    const BASE = { density: 100, trunk: 42, balance: 74, light: 48, risk: 12, aesthetic: 68 };
    const EFFECTS = {
        1: { density: -24, trunk: 12, balance: -14, light: 10, risk: 14, aesthetic: -12 },
        2: { density: -16, trunk: 10, balance: 10, light: 14, risk: 4, aesthetic: 14 },
        3: { density: -14, trunk: 8, balance: -3, light: 10, risk: 5, aesthetic: 2 },
        4: { density: -18, trunk: 9, balance: -6, light: 12, risk: 8, aesthetic: -5 },
    };
    const clamp = (value) => Math.max(0, Math.min(100, value));
    let shown = { ...BASE };

    function compute(cuts) {
        const result = { ...BASE };
        cuts.forEach((id) => Object.entries(EFFECTS[id]).forEach(([key, delta]) => (result[key] += delta)));
        Object.keys(result).forEach((key) => (result[key] = clamp(result[key])));

        return result;
    }

    const level = (value) => (value < 30 ? 'Niskie' : value < 55 ? 'Średnie' : 'Wysokie');

    function setTone(element, tone) {
        element.classList.remove('studio-metric__value--green', 'studio-metric__value--terra');
        if (tone) {
            element.classList.add(`studio-metric__value--${tone}`);
        }
    }

    function renderSim(cuts) {
        const next = compute(cuts);
        const metric = (key) => $(`[data-sim-metric="${key}"]`);

        tween(metric('density'), shown.density, next.density, (n) => `${n}%`);
        tween(metric('trunk'), shown.trunk, next.trunk, (n) => `${level(n)} (${n}%)`);
        tween(metric('aesthetic'), shown.aesthetic, next.aesthetic, (n) => `${n} / 100`);
        setTone(metric('aesthetic'), next.aesthetic >= 70 ? 'green' : 'terra');

        const sap = next.risk > 35 ? 'Zaburzony' : next.risk > 20 ? 'Umiarkowany' : 'Zbalansowany';
        metric('sap').textContent = sap;
        setTone(metric('sap'), next.risk > 20 ? 'terra' : 'green');

        const indicator = (key, text) => {
            const label = $(`[data-sim-indicator="${key}"]`);
            label.textContent = text;
            $(`[data-sim-bar="${key}"]`).style.setProperty('--value', `${next[key]}%`);
        };
        indicator('balance', `${next.balance}%`);
        indicator('light', `${next.light}%`);
        indicator('risk', `${next.risk < 20 ? 'Niskie' : next.risk < 35 ? 'Umiarkowane' : 'Wysokie'} (${next.risk}%)`);

        const advice = $('[data-sim-advice]');
        if (next.density < 55) {
            advice.textContent = `Uwaga: usunięto ponad 45% masy liści (zostało ${next.density}%). Jednorazowo zalecane jest maks. 1/3 — rozłóż zabieg na dwa sezony, aby nie osłabić drzewka.`;
        } else if (cuts.includes('2') && next.aesthetic >= 70) {
            advice.textContent = 'Dobra decyzja: usunięcie gałęzi #2 eliminuje błąd Kannonuki i otwiera przestrzeń negatywną (Ma). Balans optyczny wyraźnie się poprawił.';
        } else if (cuts.length > 0 && !cuts.includes('2')) {
            advice.textContent = 'Gałąź #2 nadal tworzy błąd Kannonuki (przeciwległa na wysokości gałęzi głównej #1). Rozważ jej usunięcie lub przekształcenie w suchy sęk Jin.';
        } else if (cuts.length > 0) {
            advice.textContent = `Usunięte gałęzie: ${cuts.map((id) => `#${id}`).join(', ')}. Sprawdź wskaźniki — estetyka ${next.aesthetic}/100, ryzyko odumierania ${next.risk}%.`;
        } else {
            advice.textContent = 'Gałąź #2 tworzy klasyczny błąd Kannonuki (gałąź przeciwległa na tej samej wysokości co gałąź główna #1). Rekomendowane jej całkowite usunięcie lub przekształcenie w suchy sęk Jin.';
        }

        $('[data-sim-save]').disabled = cuts.length === 0;
        $('[data-sim-jin]').disabled = cuts.length === 0;
        shown = next;
    }

    const cutIds = () => $$('.studio-sim__marker.is-cut').map((marker) => marker.dataset.branchToggle).sort();

    function toggleBranch(marker) {
        const id = marker.dataset.branchToggle;
        const cut = !marker.classList.contains('is-cut');
        marker.classList.toggle('is-cut', cut);
        marker.setAttribute('aria-pressed', String(cut));
        marker.setAttribute('aria-label', `Gałąź ${id}: ${cut ? 'przywróć' : 'symuluj usunięcie'}`);
        $(`.studio-sim__branch[data-branch="${id}"]`).classList.toggle('is-cut', cut);
        if (!cut) {
            $(`.studio-sim__branch[data-branch="${id}"]`).classList.remove('is-jin');
        }
        renderSim(cutIds());
    }

    function resetSim() {
        $$('.studio-sim__marker').forEach((marker) => {
            marker.classList.remove('is-cut');
            marker.setAttribute('aria-pressed', 'false');
        });
        $$('.studio-sim__branch').forEach((branch) => branch.classList.remove('is-cut', 'is-jin'));
        const jin = $('[data-sim-jin]');
        jin.setAttribute('aria-pressed', 'false');
        renderSim([]);
    }

    doc.addEventListener('click', async (event) => {
        const marker = event.target.closest('[data-branch-toggle]');
        if (marker) {
            toggleBranch(marker);
            return;
        }

        if (event.target.closest('[data-sim-reset]')) {
            resetSim();
            return;
        }

        const jin = event.target.closest('[data-sim-jin]');
        if (jin && !jin.disabled) {
            const on = jin.getAttribute('aria-pressed') !== 'true';
            jin.setAttribute('aria-pressed', String(on));
            $$('.studio-sim__branch.is-cut').forEach((branch) => branch.classList.toggle('is-jin', on));
            K.toast(on ? 'Usunięte gałęzie zamieniono na martwe drewno (Jin).' : 'Przywrócono zwykłe cięcie.', 'info');
            return;
        }

        const save = event.target.closest('[data-sim-save]');
        if (save && !save.disabled) {
            const cuts = cutIds();
            const body = new FormData();
            body.set('title', `Plan cięcia: gałęzie ${cuts.map((id) => `#${id}`).join(', ')}`);
            body.set('note', `Symulacja w Studio: ${$('[data-sim-model]').textContent.replace('Model: ', '')}. Estetyka ${shown.aesthetic}/100, gęstość liści ${shown.density}%.`);
            body.set('type', 'shape');
            body.set('time', 'Cały dzień');
            body.set('date', new Date().toLocaleDateString('sv-SE'));
            save.disabled = true;
            const result = await K.request('/schedule/tasks', { method: 'POST', body });
            save.disabled = cutIds().length === 0;
            K.toast(result.ok ? 'Zapisano plan cięcia w harmonogramie na dziś.' : (result.data.message ?? 'Nie udało się zapisać planu.'), result.ok ? 'success' : 'error');
            return;
        }

        const load = event.target.closest('[data-style-load]');
        if (load) {
            $('[data-sim-model]').textContent = `Model: ${load.dataset.styleLoad}`;
            resetSim();
            const target = $('#studio-simulator-title');
            target?.scrollIntoView({ behavior: K.reduceMotion ? 'auto' : 'smooth', block: 'center' });
            K.play($('.studio-sim'), 'fx-in');
            K.toast(`Wczytano model do symulatora: ${load.dataset.styleLoad}.`, 'success');
        }
    });

    doc.addEventListener('keydown', (event) => {
        const marker = event.target.closest?.('[data-branch-toggle]');
        if (marker && (event.key === 'Enter' || event.key === ' ')) {
            event.preventDefault();
            toggleBranch(marker);
        }
    });
})();
