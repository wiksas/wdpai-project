/**
 * Klinika: podgląd zdjęcia z animacją skanowania i analizą barwy (w przeglądarce), zapis skanu,
 * czat z bazą wiedzy (dopasowanie słów kluczowych po stronie serwera), protokół do harmonogramu, filtr gatunków.
 * Wymaga app.js (`window.Komorebi`). Bez JS wszystkie formularze i filtr działają przez zwykły POST/GET.
 */
(() => {
    'use strict';

    const K = window.Komorebi;
    if (!K) {
        return;
    }

    const doc = document;
    const LEVELS = {
        good: { label: 'dobra kondycja', title: 'Przeważa zdrowa, intensywna zieleń' },
        watch: { label: 'do obserwacji', title: 'Lekkie odbarwienia, warto obserwować' },
        warning: { label: 'ostrzeżenie', title: 'Widoczne żółknięcie lub zasychanie' },
        critical: { label: 'stan krytyczny', title: 'Przeważają tkanki suche lub żółte' },
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
    const form = doc.querySelector('[data-scan-form]');
    const fileInput = doc.querySelector('[data-scan-input]');
    // JS sprawdza wybór zdjęcia sam (także upuszczonego), więc natywne `required` blokowałoby wysyłkę.
    if (fileInput) {
        fileInput.required = false;
    }
    let current = null; // { blob, analysis }

    async function handleFile(file) {
        if (!file || !form) {
            return;
        }
        if (!file.type.startsWith('image/')) {
            K.toast('Wybierz zdjęcie w formacie JPG, PNG lub WebP.', 'error');
            return;
        }
        const preview = form.querySelector('[data-scan-preview]');
        const summary = form.querySelector('[data-scan-summary]');
        const button = form.querySelector('.clinic-upload__save');

        let loaded;
        try {
            const blob = await K.resizeImage(file);
            loaded = await loadImage(blob);
            loaded.blob = blob;
        } catch {
            K.toast('Nie udało się odczytać tego pliku jako zdjęcia.', 'error');
            return;
        }

        const analysis = analyze(loaded.img);
        current = null;
        button.disabled = true;

        const img = preview.querySelector('[data-scan-preview-img]');
        if (img.dataset.url) {
            URL.revokeObjectURL(img.dataset.url);
        }
        img.src = loaded.url;
        img.dataset.url = loaded.url;
        preview.hidden = false;
        delete preview.dataset.level;
        preview.classList.add('is-scanning');
        summary.textContent = 'Liczę barwy na zdjęciu…';

        await K.sleep(K.reduceMotion ? 150 : 1400);

        preview.classList.remove('is-scanning');
        preview.dataset.level = analysis.noPlant ? 'none' : analysis.level;
        summary.textContent = analysis.noPlant
            ? `${analysis.title}. Zrób zbliżenie na igły lub liście, żeby wypełniały większość kadru.`
            : `Indeks barwy ${analysis.vitality}/100, ${LEVELS[analysis.level].label}. ${analysis.title}.`;

        form.querySelector('[data-scan-level]').value = analysis.level;
        form.querySelector('[data-scan-vitality]').value = String(analysis.vitality);
        form.querySelector('[data-scan-title]').value = analysis.title;
        current = { blob: loaded.blob, analysis };
        button.disabled = false;
    }

    async function refreshScans() {
        try {
            const next = await K.fetchDocument('/clinic');
            const fresh = next.querySelector('[data-scans-list]');
            const list = doc.querySelector('[data-scans-list]');
            if (fresh && list) {
                list.replaceWith(fresh);
                K.play(fresh, 'fx-in');
            }
            const count = next.querySelector('[data-scans-count]');
            const own = doc.querySelector('[data-scans-count]');
            if (count && own) {
                own.textContent = count.textContent;
            }
        } catch {
            K.toast('Skan zapisany. Odśwież stronę, żeby zobaczyć go w historii.', 'info');
        }
    }

    doc.addEventListener('change', (event) => {
        if (event.target === fileInput) {
            handleFile(fileInput.files?.[0]);
        }
    });

    // Upuszczanie pliku na strefę uploadu
    doc.addEventListener('dragover', (event) => {
        const zone = event.target.closest?.('[data-dropzone]');
        if (zone) {
            event.preventDefault();
            zone.classList.add('is-drag');
        }
    });
    doc.addEventListener('dragleave', (event) => event.target.closest?.('[data-dropzone]')?.classList.remove('is-drag'));
    doc.addEventListener('drop', (event) => {
        const zone = event.target.closest?.('[data-dropzone]');
        if (!zone) {
            return;
        }
        event.preventDefault();
        zone.classList.remove('is-drag');
        handleFile(event.dataTransfer?.files?.[0]);
    });

    doc.addEventListener('submit', async (event) => {
        if (event.target !== form) {
            return;
        }
        event.preventDefault();

        if (!current) {
            K.toast('Najpierw wybierz zdjęcie i poczekaj na wynik analizy.', 'error');
            return;
        }
        const button = form.querySelector('.clinic-upload__save');
        const body = new FormData(form);
        body.set('photo', current.blob, 'scan.jpg');

        button.disabled = true;
        const result = await K.request('/clinic/scans', { method: 'POST', body });
        button.disabled = false;

        if (!result.ok) {
            K.toast(result.data.message ?? 'Nie udało się zapisać skanu. Spróbuj ponownie.', 'error');
            return;
        }
        K.toast(result.data.message, 'success');
        current = null;
        fileInput.value = '';
        await refreshScans();
    });

    // --- Czat z bazą wiedzy ---------------------------------------------------------------------------
    const thread = doc.querySelector('[data-chat-thread]');

    function addMessage({ text, meta, user = false }) {
        const row = doc.createElement('div');
        row.className = 'clinic-chat__row fx-in' + (user ? ' clinic-chat__row--user' : '');
        row.innerHTML = '<div class="clinic-chat__message"><p class="clinic-chat__meta"></p><p class="clinic-chat__bubble"></p></div>';
        row.querySelector('.clinic-chat__meta').textContent = meta;
        row.querySelector('.clinic-chat__bubble').textContent = text;
        thread.append(row);
        thread.scrollTo({ top: thread.scrollHeight, behavior: K.reduceMotion ? 'auto' : 'smooth' });

        return row;
    }

    doc.addEventListener('submit', async (event) => {
        const chatForm = event.target.closest('[data-chat-form]');
        if (!chatForm || !thread) {
            return;
        }
        event.preventDefault();

        const input = chatForm.querySelector('input[name="q"]');
        const submitter = event.submitter;
        const faq = submitter?.name === 'faq' ? submitter.value : '';
        const question = (faq || input.value).trim();
        if (question.length < 3) {
            K.toast('Wpisz pytanie (co najmniej 3 znaki).', 'error');
            input.focus();
            return;
        }

        const time = new Date().toLocaleTimeString('pl-PL', { hour: '2-digit', minute: '2-digit' });
        addMessage({ text: question, meta: `Ty, ${time}`, user: true });
        if (!faq) {
            input.value = '';
        }

        const pending = addMessage({ text: 'Szukam w bazie porad…', meta: 'Konsultant' });
        pending.classList.add('is-pending');

        const body = new FormData();
        body.set('_csrf', chatForm.querySelector('input[name="_csrf"]').value);
        body.set(faq ? 'faq' : 'q', question);

        const result = await K.request('/clinic/ask', { method: 'POST', body });
        pending.remove();

        if (!result.ok) {
            K.toast(result.data.message ?? 'Nie udało się pobrać odpowiedzi. Spróbuj ponownie.', 'error');
            return;
        }

        const reply = addMessage({ text: result.data.answer, meta: `Konsultant, ${result.data.time}` });
        if (!result.data.matched && result.data.topics?.length) {
            const topics = doc.createElement('div');
            topics.className = 'clinic-chat__topics';
            result.data.topics.forEach((topic) => {
                const chip = doc.createElement('button');
                chip.type = 'button';
                chip.className = 'clinic-chat__topic';
                chip.textContent = topic;
                chip.addEventListener('click', () => {
                    input.value = topic;
                    chatForm.requestSubmit();
                });
                topics.append(chip);
            });
            reply.querySelector('.clinic-chat__message').append(topics);
        }
    });

    // --- Protokół ratunkowy → harmonogram -------------------------------------------------------------
    doc.addEventListener('submit', async (event) => {
        const protocolForm = event.target.closest('[data-protocol-form]');
        if (!protocolForm) {
            return;
        }
        event.preventDefault();

        const button = protocolForm.querySelector('button[type="submit"]');
        button.disabled = true;
        const result = await K.post(protocolForm);

        if (!result.ok) {
            button.disabled = false;
            K.toast(result.data.message ?? 'Nie udało się dodać kroków. Spróbuj ponownie.', 'error');
            return;
        }
        K.toast(result.data.message, 'success');
        // Kroki są już w harmonogramie; przycisk zamienia się w link, żeby nie dodać ich drugi raz.
        const link = doc.createElement('a');
        link.className = 'btn clinic-protocol__button is-done';
        link.href = result.data.href ?? '/schedule';
        link.innerHTML = '<span class="ms" aria-hidden="true">check</span><span></span>';
        link.lastChild.textContent = 'Dodano. Otwórz harmonogram';
        button.replaceWith(link);
        link.focus();
    });

    // --- Filtr gatunków (bez przeładowania) -----------------------------------------------------------
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
        doc.querySelectorAll('.clinic-case').forEach((card) => {
            const show = value === 'all' || card.dataset.category === value;
            const reveal = show && card.hidden;
            card.hidden = !show;
            if (reveal) {
                K.play(card, 'fx-in');
            }
        });
        history.replaceState(null, '', value === 'all' ? '/clinic' : `/clinic?species=${value}`);
    });
})();
