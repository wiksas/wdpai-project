/**
 * Klinika: podgląd zdjęcia z animacją skanowania i analizą barwy (w przeglądarce), zapis skanu,
 * czat z bazą wiedzy, protokół do harmonogramu, filtr archiwum, siatka kadrowania, druk/udostępnianie.
 * Wymaga app.js (`window.Komorebi`). Bez JS wszystkie formularze działają przez zwykły POST.
 */
(() => {
    'use strict';

    const K = window.Komorebi;
    if (!K) {
        return;
    }

    const doc = document;
    const LEVELS = {
        good: { label: 'Dobra kondycja', title: 'Zdrowa, intensywna zieleń' },
        watch: { label: 'Do obserwacji', title: 'Lekkie odbarwienia — obserwuj' },
        warning: { label: 'Ostrzeżenie', title: 'Widoczne żółknięcie lub zasychanie' },
        critical: { label: 'Krytyczny', title: 'Dominują tkanki suche lub chlorotyczne' },
    };

    /** Klasyfikuje piksele zdjęcia (zieleń / żółć / brąz-susz) na pomniejszonej kopii. To analiza barwy, nie diagnoza. */
    function analyze(img) {
        const size = 72;
        const canvas = doc.createElement('canvas');
        canvas.width = size;
        canvas.height = size;
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        ctx.drawImage(img, 0, 0, size, size);
        const { data } = ctx.getImageData(0, 0, size, size);

        let green = 0;
        let yellow = 0;
        let dry = 0;
        for (let i = 0; i < data.length; i += 4) {
            const [r, g, b] = [data[i], data[i + 1], data[i + 2]];
            const max = Math.max(r, g, b);
            const min = Math.min(r, g, b);
            if (max < 40 || max - min < 22) {
                continue; // cień lub szarość (tło, donica)
            }
            if (g >= r * 1.05 && g >= b * 1.15) {
                green++;
            } else if (r > 140 && g > 130 && b < Math.min(r, g) * 0.65) {
                yellow++;
            } else if (r > g * 1.1 && g >= b && r < 190) {
                dry++;
            }
        }

        const plant = green + yellow + dry;
        if (plant < size * size * 0.04) {
            return { vitality: 0, level: 'watch', title: 'Nie wykryto wyraźnej tkanki roślinnej', noPlant: true };
        }

        const vitality = Math.round(((green + yellow * 0.35 + dry * 0.5) / plant) * 100);
        const level = vitality >= 80 ? 'good' : vitality >= 60 ? 'watch' : vitality >= 40 ? 'warning' : 'critical';

        return { vitality, level, title: LEVELS[level].title, noPlant: false };
    }

    function loadImage(blob) {
        return new Promise((resolve, reject) => {
            const url = URL.createObjectURL(blob);
            const img = new Image();
            img.onload = () => resolve({ img, url });
            img.onerror = () => {
                URL.revokeObjectURL(url);
                reject(new Error('image'));
            };
            img.src = url;
        });
    }

    // --- Wybór zdjęcia: podgląd + skanowanie + analiza ------------------------------------------------
    let current = null; // { blob, analysis }

    async function handleFile(file, { autoSave = false } = {}) {
        if (!file) {
            return;
        }
        const form = doc.querySelector('[data-scan-form]');
        const preview = doc.querySelector('[data-scan-preview]');
        const summary = doc.querySelector('[data-scan-summary]');

        const blob = await K.resizeImage(file);
        let loaded;
        try {
            loaded = await loadImage(blob);
        } catch {
            K.toast('Nie udało się odczytać tego pliku jako zdjęcia.', 'error');
            return;
        }

        const analysis = analyze(loaded.img);
        current = { blob, analysis };

        if (preview && form) {
            const img = preview.querySelector('[data-scan-preview-img]');
            if (img.dataset.url) {
                URL.revokeObjectURL(img.dataset.url);
            }
            img.src = loaded.url;
            img.dataset.url = loaded.url;
            preview.hidden = false;
            preview.classList.remove('is-scanned');
            preview.classList.add('is-scanning');
            summary.textContent = 'Skanowanie próbki…';

            await K.sleep(K.reduceMotion ? 200 : 1700);

            preview.classList.remove('is-scanning');
            preview.classList.add('is-scanned');
            preview.dataset.level = analysis.level;
            summary.textContent = analysis.noPlant
                ? `${analysis.title}. Zrób zbliżenie na igły lub liście.`
                : `Orientacyjny indeks barwy: ${analysis.vitality}/100 • ${LEVELS[analysis.level].label}. ${analysis.title}.`;

            form.querySelector('[data-scan-level]').value = analysis.level;
            form.querySelector('[data-scan-vitality]').value = String(analysis.vitality);
            form.querySelector('[data-scan-title]').value = analysis.title;
            form.querySelector('.clinic-upload__save')?.classList.add('is-ready');
        }

        if (autoSave) {
            await save(blob, analysis, doc.querySelector('[data-scan-form]'));
        }
    }

    async function save(blob, analysis, form) {
        const body = new FormData(form ?? undefined);
        body.set('photo', blob, 'scan.jpg');
        body.set('level', analysis.level);
        body.set('vitality', String(analysis.vitality));
        body.set('title', analysis.title);
        if (!form) {
            body.set('_csrf', doc.querySelector('input[name="_csrf"]')?.value ?? '');
            body.set('part', 'leaves');
        }

        const result = await K.request('/clinic/scans', { method: 'POST', body });
        if (!result.ok) {
            K.toast(result.data.message ?? 'Nie udało się zapisać skanu.', 'error');
            return;
        }
        K.toast(result.data.message, 'success');
        current = null;
        await refreshScans();
    }

    async function refreshScans() {
        try {
            const next = await K.fetchDocument('/clinic');
            doc.querySelectorAll('[data-scans-list]').forEach((list, index) => {
                const fresh = next.querySelectorAll('[data-scans-list]')[index];
                if (fresh) {
                    list.replaceWith(fresh);
                    K.play(fresh, 'fx-in');
                }
            });
            const tag = next.querySelector('.clinic-previous__tag');
            const own = doc.querySelector('.clinic-previous__tag');
            if (tag && own) {
                own.textContent = tag.textContent;
            }
        } catch {
            /* odświeżenie historii jest opcjonalne */
        }
    }

    doc.addEventListener('change', (event) => {
        const input = event.target.closest('[data-scan-input], [data-scan-input-mobile]');
        if (!input) {
            return;
        }
        const file = input.files?.[0];
        handleFile(file, { autoSave: input.hasAttribute('data-scan-input-mobile') });
        if (input.hasAttribute('data-scan-input-mobile')) {
            input.value = '';
        }
    });

    // Przeciąganie pliku na strefę uploadu
    doc.addEventListener('dragover', (event) => {
        const zone = event.target.closest('[data-dropzone]');
        if (zone) {
            event.preventDefault();
            zone.classList.add('is-drag');
        }
    });
    doc.addEventListener('dragleave', (event) => event.target.closest?.('[data-dropzone]')?.classList.remove('is-drag'));
    doc.addEventListener('drop', (event) => {
        const zone = event.target.closest('[data-dropzone]');
        if (!zone) {
            return;
        }
        event.preventDefault();
        zone.classList.remove('is-drag');
        const file = event.dataTransfer?.files?.[0];
        if (file) {
            handleFile(file);
        }
    });

    doc.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-scan-form]');
        if (!form) {
            return;
        }
        event.preventDefault();

        if (!current) {
            K.toast('Najpierw wybierz zdjęcie do skanowania.', 'error');
            return;
        }
        const button = form.querySelector('.clinic-upload__save');
        button.disabled = true;
        await save(current.blob, current.analysis, form);
        button.disabled = false;
    });

    // --- Czat z bazą wiedzy ---------------------------------------------------------------------------
    function addMessage(thread, { text, meta, user = false }) {
        const wrap = doc.createElement('div');
        wrap.className = 'clinic-chat__row fx-in' + (user ? ' clinic-chat__row--user' : '');
        const icon = user ? '' : '<span class="clinic-chat__bot" aria-hidden="true"><span class="ms ms--fill">eco</span></span>';
        wrap.innerHTML = `${icon}<div class="clinic-chat__message"><p class="clinic-chat__meta"></p><p class="clinic-chat__bubble"></p></div>`;
        wrap.querySelector('.clinic-chat__meta').textContent = meta;
        wrap.querySelector('.clinic-chat__bubble').textContent = text;
        thread.append(wrap);
        wrap.scrollIntoView({ block: 'nearest', behavior: K.reduceMotion ? 'auto' : 'smooth' });

        return wrap;
    }

    doc.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-chat-form]');
        if (!form) {
            return;
        }
        event.preventDefault();

        const thread = doc.querySelector('[data-chat-thread]');
        const input = form.querySelector('input[name="q"]');
        const submitter = event.submitter;
        const faq = submitter?.name === 'faq' ? submitter.value : '';
        const question = (faq || input.value).trim();
        if (question.length < 3) {
            K.toast('Zadaj pytanie (min. 3 znaki).', 'error');
            return;
        }

        const time = new Date().toLocaleTimeString('pl-PL', { hour: '2-digit', minute: '2-digit' });
        addMessage(thread, { text: question, meta: `Ty • ${time}`, user: true });
        input.value = '';

        const typing = addMessage(thread, { text: '', meta: 'Komorebi • pisze…' });
        typing.querySelector('.clinic-chat__bubble').innerHTML = '<span class="clinic-typing" aria-label="Asystent pisze"><i></i><i></i><i></i></span>';

        const body = new FormData();
        body.set('_csrf', form.querySelector('input[name="_csrf"]').value);
        body.set(faq ? 'faq' : 'q', question);

        const [result] = await Promise.all([K.request('/clinic/ask', { method: 'POST', body }), K.sleep(K.reduceMotion ? 0 : 900)]);
        typing.remove();

        if (!result.ok) {
            K.toast(result.data.message ?? 'Nie udało się uzyskać odpowiedzi.', 'error');
            return;
        }

        const reply = addMessage(thread, { text: result.data.answer, meta: `Komorebi • ${result.data.time}` });
        if (!result.data.matched && result.data.topics?.length) {
            const chips = doc.createElement('div');
            chips.className = 'clinic-chat__topics';
            result.data.topics.forEach((topic) => {
                const chip = doc.createElement('button');
                chip.type = 'button';
                chip.className = 'clinic-chat__topic';
                chip.textContent = topic;
                chip.addEventListener('click', () => {
                    input.value = topic;
                    form.requestSubmit();
                });
                chips.append(chip);
            });
            reply.querySelector('.clinic-chat__message').append(chips);
        }
    });

    // --- Protokół ratunkowy → harmonogram -------------------------------------------------------------
    doc.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-protocol-form]');
        if (!form) {
            return;
        }
        event.preventDefault();

        const button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        const result = await K.post(form);
        button.disabled = false;

        if (!result.ok) {
            K.toast(result.data.message ?? 'Nie udało się dodać kroków.', 'error');
            return;
        }
        K.toast(result.data.message, 'success');
        button.classList.add('is-done');
        button.querySelector('.ms').textContent = 'check';
    });

    // --- Filtr archiwum (bez przeładowania) -----------------------------------------------------------
    doc.addEventListener('click', (event) => {
        const filter = event.target.closest('[data-archive-filter]');
        if (!filter) {
            return;
        }
        event.preventDefault();

        const value = filter.dataset.archiveFilter;
        doc.querySelectorAll('[data-archive-filter]').forEach((item) => {
            const on = item === filter;
            item.classList.toggle('is-active', on);
            on ? item.setAttribute('aria-current', 'true') : item.removeAttribute('aria-current');
        });
        let visible = 0;
        doc.querySelectorAll('.clinic-case').forEach((card) => {
            const show = value === 'all' || card.dataset.category === value;
            card.hidden = !show;
            if (show) {
                K.play(card, 'fx-in');
                visible++;
            }
        });
        history.replaceState(null, '', value === 'all' ? '/clinic' : `/clinic?species=${value}`);
    });

    // Filtr po stronie klienta musi odpowiadać serwerowemu stanowi początkowemu.
    const initial = doc.querySelector('[data-archive-filter].is-active');
    if (initial && initial.dataset.archiveFilter !== 'all') {
        doc.querySelectorAll('.clinic-case').forEach((card) => {
            card.hidden = card.dataset.category !== initial.dataset.archiveFilter;
        });
    }

    // --- Chipsy kategorii (mobile) i siatka kadrowania ------------------------------------------------
    doc.addEventListener('click', (event) => {
        const chip = event.target.closest('.clinic-chips__item');
        if (chip) {
            event.preventDefault();
            doc.querySelectorAll('.clinic-chips__item').forEach((item) => {
                item.classList.toggle('is-active', item === chip);
                item === chip ? item.setAttribute('aria-current', 'true') : item.removeAttribute('aria-current');
            });
            return;
        }

        const grid = event.target.closest('[data-grid-toggle]');
        if (grid) {
            const on = grid.getAttribute('aria-pressed') !== 'true';
            grid.setAttribute('aria-pressed', String(on));
            doc.querySelector('.clinic-viewer')?.classList.toggle('has-grid', on);
            return;
        }

        if (event.target.closest('[data-print]')) {
            window.print();
            return;
        }

        const share = event.target.closest('[data-share]');
        if (share) {
            const data = { title: share.dataset.shareTitle ?? doc.title, url: location.href };
            const run = navigator.share ? navigator.share(data) : navigator.clipboard.writeText(data.url).then(() => K.toast('Skopiowano link do Kliniki.', 'info'));
            run.catch((error) => {
                if (error?.name !== 'AbortError') {
                    K.toast('Nie udało się udostępnić linku.', 'error');
                }
            });
        }
    });
})();
