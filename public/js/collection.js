/**
 * Kolekcja i karta okazu: filtry i szukanie bez przeładowania, zabiegi (podlanie / korekta) z animowanym paskiem,
 * dziennik obserwacji z autozapisem szkicu. Wymaga app.js (`window.Komorebi`).
 */
(() => {
    'use strict';

    const K = window.Komorebi;
    if (!K) {
        return;
    }

    const doc = document;
    const isCollection = () => !!doc.querySelector('[data-collection]');
    // Fragmenty odświeżane po zabiegu: kolekcja albo karta okazu.
    const refreshSelectors = () => (isCollection()
        ? ['[data-collection]', '[data-due]', '[data-summary]', '.dash-calendar']
        : ['.passport-layout']);

    const barValues = () => [...doc.querySelectorAll('main .progress__bar, main .passport-meter__bar, .app-sidebar .progress__bar')]
        .map((bar) => bar.style.getPropertyValue('--value'));

    /** Po podmianie DOM startuje paski od starych wartości i płynnie dochodzi do nowych. */
    function animateBars(before) {
        const bars = [...doc.querySelectorAll('main .progress__bar, main .passport-meter__bar, .app-sidebar .progress__bar')];
        if (K.reduceMotion || bars.length !== before.length) {
            return;
        }

        const targets = bars.map((bar) => bar.style.getPropertyValue('--value'));
        bars.forEach((bar, index) => {
            if (before[index] !== targets[index]) {
                bar.style.setProperty('--value', before[index]);
            }
        });
        void doc.body.offsetWidth;
        requestAnimationFrame(() => bars.forEach((bar, index) => bar.style.setProperty('--value', targets[index])));
    }

    /** Pobiera bieżącą stronę i podmienia wskazane fragmenty oraz pasek boczny. */
    async function refresh(url = location.href, selectors = refreshSelectors()) {
        const before = barValues();
        let next;
        try {
            next = await K.fetchDocument(url);
        } catch {
            return false;
        }

        K.swap(next, [...selectors, '.app-sidebar .status-card']);
        animateBars(before);
        K.refreshNotifications();

        return true;
    }

    // Filtry i szukanie
    let ticket = 0;

    async function showCollection(url, push = true) {
        const mine = ++ticket;
        const region = doc.querySelector('[data-collection]');
        region?.classList.add('is-loading');

        let next;
        try {
            next = await K.fetchDocument(url);
        } catch {
            if (mine === ticket) {
                region?.classList.remove('is-loading');
                K.toast('Nie udało się wczytać kolekcji. Sprawdź połączenie i spróbuj ponownie.', 'error');
            }
            return;
        }

        if (mine !== ticket) {
            return;
        }

        K.swap(next, ['[data-collection]']);
        K.play(doc.querySelector('[data-collection]'), 'fx-in');
        if (push) {
            history.pushState({}, '', url);
        }
    }

    doc.addEventListener('click', (event) => {
        const link = event.target.closest('[data-filter-link]');
        if (!link || !isCollection() || event.metaKey || event.ctrlKey || event.shiftKey || event.button !== 0) {
            return;
        }

        event.preventDefault();
        const q = new URL(link.href).searchParams.get('q') ?? '';
        searches.forEach((input) => {
            input.value = q;
        });
        showCollection(link.href);
    });

    window.addEventListener('popstate', () => {
        if (isCollection()) {
            const q = new URLSearchParams(location.search).get('q') ?? '';
            searches.forEach((input) => {
                input.value = q;
            });
            showCollection(location.href, false);
        }
    });

    // Pole w górnym pasku (desktop) i w sekcji kolekcji (mobile) szukają tak samo i pokazują tę samą frazę.
    const searches = isCollection()
        ? [...doc.querySelectorAll('.app-topbar__search input[name="q"], input[data-search]')]
        : [];
    let timer = 0;
    const buildUrl = (value) => {
        const params = new URLSearchParams(location.search);
        value ? params.set('q', value) : params.delete('q');
        const query = params.toString();

        return `/collection${query ? `?${query}` : ''}`;
    };

    searches.forEach((input) => {
        input.addEventListener('input', () => {
            searches.forEach((other) => {
                if (other !== input) {
                    other.value = input.value;
                }
            });
            clearTimeout(timer);
            timer = setTimeout(() => showCollection(buildUrl(input.value.trim())), 240);
        });

        input.form.addEventListener('submit', (event) => {
            event.preventDefault();
            clearTimeout(timer);
            showCollection(buildUrl(input.value.trim()));
        });
    });

    // Zabiegi: podlanie i korekta
    doc.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!form.matches('[data-care-form]')) {
            return;
        }

        event.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        button?.setAttribute('disabled', '');
        button?.classList.add('is-busy');

        const result = await K.post(form);
        if (!result.ok) {
            button?.removeAttribute('disabled');
            button?.classList.remove('is-busy');
            K.toast(result.data.message ?? 'Nie udało się zapisać zabiegu.', 'error');
            return;
        }

        K.toast(result.data.message, 'success');
        if (!(await refresh())) {
            location.reload();
        }
    });

    // Dziennik obserwacji
    const journalForm = doc.querySelector('[data-journal-form]');
    if (journalForm) {
        const area = journalForm.querySelector('textarea');
        const counter = doc.querySelector('[data-journal-counter]');
        const draft = doc.querySelector('[data-autosave]');
        const key = 'komorebi:journal-draft';
        let saveTimer = 0;

        const updateCounter = () => {
            counter.textContent = `${area.value.length} / ${area.maxLength}`;
        };

        try {
            if (!area.value && localStorage.getItem(key)) {
                area.value = localStorage.getItem(key);
                draft.textContent = 'Przywrócono szkic';
            }
        } catch {
            /* localStorage może być niedostępny (tryb prywatny) */
        }
        updateCounter();

        area.addEventListener('input', () => {
            updateCounter();
            clearTimeout(saveTimer);
            saveTimer = setTimeout(() => {
                try {
                    area.value ? localStorage.setItem(key, area.value) : localStorage.removeItem(key);
                    draft.textContent = area.value ? 'Szkic zapisany w przeglądarce' : '';
                } catch {
                    draft.textContent = '';
                }
            }, 500);
        });

        journalForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (!journalForm.reportValidity()) {
                return;
            }

            const button = journalForm.querySelector('button[type="submit"]');
            button.disabled = true;
            const result = await K.post(journalForm);
            button.disabled = false;

            if (!result.ok) {
                K.toast(result.data.message ?? 'Nie udało się zapisać wpisu.', 'error');
                return;
            }

            const list = doc.querySelector('[data-journal-list]');
            const entry = doc.createElement('article');
            entry.className = 'dash-journal__entry fx-in';
            entry.innerHTML = '<p class="dash-journal__entry-time"></p><p class="dash-journal__entry-text"></p>';
            entry.children[0].textContent = result.data.entry.when;
            entry.children[1].textContent = result.data.entry.text;
            list.prepend(entry);
            list.querySelector('[data-journal-empty]')?.setAttribute('hidden', '');
            [...list.querySelectorAll('.dash-journal__entry')].slice(4).forEach((old) => old.remove());

            area.value = '';
            updateCounter();
            draft.textContent = '';
            try {
                localStorage.removeItem(key);
            } catch {
                /* brak localStorage */
            }
            K.toast(result.data.message, 'success');
        });
    }

    window.KomorebiCollection = { refresh };
})();
