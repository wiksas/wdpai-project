/**
 * Studio formowania: przełączanie stylów i gatunków bez przeładowania, kalkulator drutu,
 * wzorzec kąta owijania i próbne cięcie. Wymaga app.js (`window.Komorebi`).
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

    // Style i gatunki: podmiana fragmentów
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

    // Kalkulator grubości drutu
    const WIRE = {
        aluminium: { label: 'aluminium', sizes: [1, 1.5, 2, 2.5, 3, 3.5, 4, 5, 6], factor: 1 / 3 },
        copper: { label: 'miedź wyżarzana', sizes: [0.8, 1, 1.3, 1.6, 2, 2.5, 3, 3.5, 4, 5], factor: 1 / 4 },
    };

    function updateWire(form) {
        const input = $('[data-wire-diameter]', form);
        const diameter = Number(input.value);
        const alloy = WIRE[new FormData(form).get('alloy')] ?? WIRE.aluminium;
        const ideal = diameter * alloy.factor;
        const size = alloy.sizes.reduce((best, item) => (Math.abs(item - ideal) < Math.abs(best - ideal) ? item : best), alloy.sizes[0]);

        const time = diameter < 6 ? '2–4 miesiącach' : diameter < 15 ? '6–9 miesiącach' : diameter < 25 ? '8–12 miesiącach' : '10–14 miesiącach';
        const anchor = diameter < 6
            ? 'o sąsiednią gałąź (dwie gałęzie jednym drutem)'
            : diameter < 15
              ? 'o pień, min. półtora zwoju przed gałęzią'
              : diameter < 25
                ? 'dwa druty obok siebie, zakotwiczone o pień'
                : 'odciąg zamiast drutu (linka do donicy lub pnia)';

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

    // Wzorzec kąta owijania
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

        $('[data-angle-output]').textContent = `${angle}°`;
        input.style.setProperty('--value', pct(angle, Number(input.min), Number(input.max)));

        const tight = angle < 30;
        const loose = angle > 55;
        const badge = $('[data-angle-badge]');
        badge.textContent = tight ? `Za ciasno (${angle}°)` : loose ? `Za luźno (${angle}°)` : `Dobry kąt (${angle}°)`;
        badge.classList.toggle('is-warn', tight || loose);

        const note = $('[data-angle-note]');
        note.classList.toggle('is-warn', tight || loose);
        $('.ms', note).textContent = tight || loose ? 'warning' : 'check_circle';
        $('span:last-child', note).innerHTML = tight
            ? '<strong>Za ciasno.</strong> Gęste zwoje uciskają korę i szybko w nią wrastają. Zwiększ kąt do około 45°.'
            : loose
              ? '<strong>Za luźno.</strong> Rzadkie zwoje nie utrzymają gałęzi i wróci ona na miejsce. Zmniejsz kąt do około 45°.'
              : '<strong>Dobry kąt.</strong> Zwoje trzymają gałąź w nowym położeniu i nie uciskają kory w jednym miejscu.';
    }

    doc.addEventListener('input', (event) => {
        const input = event.target.closest('[data-angle-input]');
        if (input) {
            updateAngle(input);
        }
    });

    // Próbne cięcie: tylko geometria rysunku (pole liści i ich położenie względem osi pnia)
    const AXIS_X = 284;
    const CROWN_HALF = 211;
    const MAX_CUT = 1 / 3;
    const leaves = () => $$('.studio-sim__tree .studio-sim__leaf').map((ellipse) => ({
        branch: ellipse.closest('[data-branch]')?.dataset.branch ?? null,
        area: Number(ellipse.getAttribute('rx')) * Number(ellipse.getAttribute('ry')),
        x: Number(ellipse.getAttribute('cx')),
        y: Number(ellipse.getAttribute('cy')),
    }));
    let shown = { foliage: 100, shift: 0 };

    function compute(cuts) {
        const all = leaves();
        const total = all.reduce((sum, leaf) => sum + leaf.area, 0);
        const kept = all.filter((leaf) => !cuts.includes(leaf.branch));
        const keptArea = kept.reduce((sum, leaf) => sum + leaf.area, 0);
        const x = kept.reduce((sum, leaf) => sum + leaf.area * leaf.x, 0) / keptArea;
        const y = kept.reduce((sum, leaf) => sum + leaf.area * leaf.y, 0) / keptArea;

        return { foliage: Math.round((keptArea / total) * 100), shift: Math.round(((x - AXIS_X) / CROWN_HALF) * 100), x, y };
    }

    const shiftText = (shift) => (Math.abs(shift) < 5 ? 'na osi pnia' : `${Math.abs(shift)}% szerokości korony w ${shift < 0 ? 'lewo' : 'prawo'}`);

    function advice(cuts, next) {
        const removed = 100 - next.foliage;
        if (cuts.length === 0) {
            return 'Gałęzie 1 i 2 wyrastają naprzeciw siebie prawie na tej samej wysokości (kannuki-eda). Zwykle usuwa się jedną z nich.';
        }
        if (removed > MAX_CUT * 100) {
            return `Usuwasz ${removed}% ulistnienia. Za jednym razem zdejmuj najwyżej około 1/3, resztę cięć rozłóż na następny sezon.`;
        }
        const pair = cuts.filter((id) => id === '1' || id === '2');
        const side = Math.abs(next.shift) >= 5 ? ` Środek masy korony przesuwa się w ${next.shift < 0 ? 'lewo' : 'prawo'}, więc wierzchołek warto lekko odchylić w przeciwną stronę.` : '';
        if (pair.length === 2) {
            return `Usunięcie obu gałęzi z pary zabiera całe dolne piętro korony. Zwykle wystarcza jedna z nich.${side}`;
        }
        if (pair.length === 1) {
            return `Usunięcie gałęzi ${pair[0]} likwiduje kannuki-eda.${side}`;
        }

        return `Gałęzie 1 i 2 nadal tworzą parę kannuki-eda.${side}`;
    }

    function renderSim(cuts) {
        const next = compute(cuts);
        tween($('[data-sim-metric="foliage"]'), shown.foliage, next.foliage, (n) => `${n}%`);
        $('[data-sim-metric="shift"]').textContent = shiftText(next.shift);
        $('[data-sim-bar]').style.setProperty('--value', `${next.foliage}%`);
        const center = $('[data-sim-center]');
        center.setAttribute('cx', next.x.toFixed(1));
        center.setAttribute('cy', next.y.toFixed(1));
        center.classList.toggle('is-off', Math.abs(next.shift) >= 5);
        const tip = $('[data-sim-advice]');
        tip.textContent = advice(cuts, next);
        tip.classList.toggle('is-warn', 100 - next.foliage > MAX_CUT * 100);

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
        marker.setAttribute('aria-label', `Gałąź ${id}: ${cut ? 'przywróć' : 'zaznacz do usunięcia'}`);
        const branch = $(`.studio-sim__branch[data-branch="${id}"]`);
        branch.classList.toggle('is-cut', cut);
        branch.classList.toggle('is-jin', cut && $('[data-sim-jin]').getAttribute('aria-pressed') === 'true');
        renderSim(cutIds());
    }

    function resetSim() {
        $$('.studio-sim__marker').forEach((marker) => {
            marker.classList.remove('is-cut');
            marker.setAttribute('aria-pressed', 'false');
            marker.setAttribute('aria-label', `Gałąź ${marker.dataset.branchToggle}: zaznacz do usunięcia`);
        });
        $$('.studio-sim__branch').forEach((branch) => branch.classList.remove('is-cut', 'is-jin'));
        $('[data-sim-jin]').setAttribute('aria-pressed', 'false');
        renderSim([]);
    }

    if ($('.studio-sim')) {
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
            return;
        }

        const save = event.target.closest('[data-sim-save]');
        if (save && !save.disabled) {
            const cuts = cutIds();
            const body = new FormData();
            body.set('title', `Plan cięcia: gałęzie ${cuts.join(', ')}`);
            body.set('note', `Próbne cięcie w Studio: zostaje ${shown.foliage}% ulistnienia, środek masy korony ${shiftText(shown.shift)}.`);
            body.set('type', 'shape');
            body.set('time', 'Cały dzień');
            body.set('date', new Date().toLocaleDateString('sv-SE'));
            save.disabled = true;
            const result = await K.request('/schedule/tasks', { method: 'POST', body });
            save.disabled = cutIds().length === 0;
            K.toast(result.ok ? 'Dodano plan cięcia do harmonogramu na dziś.' : (result.data.message ?? 'Nie udało się zapisać planu.'), result.ok ? 'success' : 'error');
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
