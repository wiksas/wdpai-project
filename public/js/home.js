/**
 * Strona główna: ocena ryzyka liczona na żywo po zmianie parametrów.
 * Model orientacyjny (edukacyjny): sumuje punkty za gatunek, podłoże, podlewanie i stanowisko.
 * Bez JS widok pokazuje wynik dla ustawień domyślnych (te same liczby co poniżej).
 */
(() => {
    'use strict';

    const form = document.querySelector('[data-risk-form]');
    if (!form) {
        return;
    }

    const factors = {
        species: {
            'pinus-thunbergii': { score: 12, note: 'Sosna czarna źle znosi długo mokre podłoże i przesuszenie igieł.' },
            'ulmus-parvifolia': { score: 16, note: 'Wiąz ma cienkie liście i szybko traci turgor w suchym powietrzu.' },
            'ficus-retusa': { score: 8, note: 'Fikus dobrze znosi wahania wilgotności.' },
        },
        substrate: {
            akadama: { score: 14, note: 'Czysta akadama szybko się kruszy i zbija, a przy tym szybko przesycha.' },
            'akadama-kiryu': { score: 8, note: 'Mieszanka akadama i kiryu daje zrównoważony drenaż.' },
            universal: { score: 28, note: 'Ziemia uniwersalna zagęszcza się i dusi korzenie: to największy czynnik ryzyka.' },
        },
        watering: {
            weekly: { score: 14, note: 'Podlewanie według sztywnego kalendarza raz w tygodniu grozi przesuszeniem.' },
            'dry-top': { score: 2, note: 'Podlewanie po przeschnięciu wierzchniej warstwy to najlepsza praktyka.' },
            daily: { score: 20, note: 'Codzienne podlewanie grozi przelaniem i gniciem korzeni.' },
        },
        light: {
            south: { score: 10, note: 'Szyba od południa nagrzewa donicę i przyspiesza parowanie.' },
            east: { score: 4, note: 'Parapet od wschodu daje łagodne poranne słońce.' },
            terrace: { score: 8, note: 'Taras zwiększa parowanie przez wiatr, ale daje dużo światła.' },
        },
    };

    const actions = {
        weekly: ['podlewaj, gdy drzewko potrzebuje', 'Sprawdzaj wilgotność bambusowym patyczkiem. Podlewaj obficie tylko wtedy, gdy górny centymetr podłoża wyschnie.'],
        daily: ['ogranicz podlewanie', 'Codzienne podlewanie zalewa korzenie. Podlewaj dopiero, gdy górny centymetr podłoża przeschnie, i opróżniaj podstawkę.'],
        universal: ['przesadź do mineralnego podłoża', 'Wiosną przesadź drzewko do mieszanki akadama i kiryu (7:3). Korzenie dostaną tlen, a woda rozłoży się równomiernie.'],
        ok: ['utrzymuj obecną pielęgnację', 'Ustawienia są dobre. Nadal sprawdzaj wilgotność patyczkiem i obserwuj jędrność igieł lub liści.'],
    };

    const levels = { low: 'Niskie ryzyko', mid: 'Umiarkowane ryzyko', high: 'Wysokie ryzyko' };

    const els = {
        badge: document.querySelector('[data-risk-badge]'),
        value: document.querySelector('[data-risk-value]'),
        bar: document.querySelector('[data-risk-bar]'),
        progress: document.querySelector('[data-risk-progress]'),
        text: document.querySelector('[data-risk-text]'),
        actionTitle: document.querySelector('[data-risk-action-title]'),
        action: document.querySelector('[data-risk-action]'),
        parts: document.querySelectorAll('[data-risk-part]'),
        result: document.querySelector('.home-result'),
    };
    let shown = Number(els.value.textContent) || 0;

    function tween(from, to) {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || from === to) {
            els.value.textContent = String(to);
            return;
        }
        const start = performance.now();
        const step = (now) => {
            const t = Math.min(1, (now - start) / 450);
            els.value.textContent = String(Math.round(from + (to - from) * (1 - (1 - t) ** 3)));
            if (t < 1) {
                requestAnimationFrame(step);
            }
        };
        requestAnimationFrame(step);
    }

    function update() {
        const data = new FormData(form);
        const picked = Object.keys(factors).map((key) => ({ key, ...factors[key][data.get(key)] }));
        const score = Math.max(0, Math.min(100, picked.reduce((sum, item) => sum + (item.score ?? 0), 0)));
        const level = score < 30 ? 'low' : score < 60 ? 'mid' : 'high';

        els.badge.textContent = levels[level];
        els.result.dataset.level = level;
        els.bar.style.setProperty('--value', `${score}%`);
        els.progress.setAttribute('aria-valuenow', String(score));
        tween(shown, score);
        shown = score;

        els.parts.forEach((part) => {
            const item = picked.find((entry) => entry.key === part.dataset.riskPart);
            part.textContent = `${item?.score ?? 0} pkt`;
        });

        // Czynniki wyraźnie zwiększające ryzyko, od najsilniejszego
        const stress = picked.filter((item) => item.score >= 14).sort((a, b) => b.score - a.score);
        els.text.textContent = stress.length > 0
            ? `Główne czynniki stresu: ${stress.map((item) => item.note).join(' ')}`
            : 'Brak istotnych czynników stresu: ustawienia pielęgnacji są dobrze dobrane.';

        const wateringKey = data.get('watering');
        const key = data.get('substrate') === 'universal' ? 'universal' : wateringKey === 'daily' || wateringKey === 'weekly' ? wateringKey : 'ok';
        els.actionTitle.textContent = `Zalecana akcja: ${actions[key][0]}`;
        els.action.textContent = actions[key][1];
        els.result.classList.remove('is-updated');
        void els.result.offsetWidth;
        els.result.classList.add('is-updated');
    }

    form.addEventListener('change', update);
    // Enter w formularzu tylko przelicza wynik; do Kliniki prowadzi wyłącznie przycisk pod wynikiem.
    form.addEventListener('submit', (event) => {
        if (event.submitter?.hasAttribute('data-risk-submit')) {
            return;
        }
        event.preventDefault();
        update();
    });
    update();
})();
