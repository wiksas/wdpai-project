/**
 * KOMOREBI — rdzeń JavaScriptu (bez bibliotek, progressive enhancement).
 * Każda funkcja działa też bez JS (zwykłe linki i formularze); skrypt dodaje płynność:
 * zapisy przez fetch, toasty, odświeżanie fragmentów stron i panel powiadomień.
 * Skrypty stron (schedule.js, collection.js…) korzystają z `window.Komorebi`.
 */
(() => {
    'use strict';

    const doc = document;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const csrf = () => doc.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

    /**
     * Wysyła żądanie z nagłówkami aplikacji. Zwraca `{ ok, status, data }`;
     * przy wygasłej sesji przekierowuje na logowanie, przy braku sieci pokazuje toast.
     */
    async function request(url, { method = 'GET', body = null } = {}) {
        try {
            const response = await fetch(url, {
                method,
                body,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'fetch', 'X-CSRF-Token': csrf(), Accept: 'application/json' },
            });

            let data = {};
            try {
                data = await response.json();
            } catch {
                data = {};
            }

            if (response.status === 401 && data.redirect) {
                window.location.href = data.redirect;
            }

            return { ok: response.ok && data.ok !== false, status: response.status, data };
        } catch {
            toast('Brak połączenia z serwerem. Spróbuj ponownie.', 'error');
            return { ok: false, status: 0, data: {} };
        }
    }

    /** Wysyła formularz (FormData) pod jego `action`. */
    function post(form, extra = {}) {
        const body = new FormData(form);
        Object.entries(extra).forEach(([key, value]) => body.set(key, value));

        return request(form.getAttribute('action'), { method: 'POST', body });
    }

    /** Pobiera stronę HTML i zwraca sparsowany dokument (do podmiany fragmentów). */
    async function fetchDocument(url) {
        const response = await fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'fetch' } });

        if (response.redirected && new URL(response.url).pathname === '/login') {
            window.location.href = '/login';
            throw new Error('session-expired');
        }

        return new DOMParser().parseFromString(await response.text(), 'text/html');
    }

    /**
     * Podmienia fragmenty bieżącej strony fragmentami z `nextDoc` (po selektorach).
     * Zwraca liczbę podmienionych elementów.
     */
    function swap(nextDoc, selectors) {
        let count = 0;
        selectors.forEach((selector) => {
            const current = doc.querySelector(selector);
            const next = nextDoc.querySelector(selector);
            if (current && next) {
                current.replaceWith(next);
                count += 1;
            }
        });

        return count;
    }

    /** Uruchamia animację CSS przez dodanie klasy; zwraca obietnicę zakończenia (lub od razu, gdy ruch jest ograniczony). */
    function play(element, className) {
        if (!element) {
            return Promise.resolve();
        }

        if (reduceMotion) {
            return Promise.resolve();
        }

        element.classList.remove(className);
        void element.offsetWidth;
        element.classList.add(className);

        return new Promise((resolve) => {
            const finish = () => {
                element.removeEventListener('animationend', finish);
                resolve();
            };
            element.addEventListener('animationend', finish, { once: true });
            setTimeout(finish, 600);
        });
    }

    /* ---------------------------------------------------------------- Toasty */
    const toasts = () => doc.querySelector('[data-toasts]');

    function toast(message, type = 'success') {
        const host = toasts();
        if (!host) {
            return;
        }

        while (host.children.length >= 4) {
            host.firstElementChild.remove();
        }

        const icon = type === 'error' ? 'error' : type === 'info' ? 'info' : 'check_circle';
        const element = doc.createElement('div');
        element.className = `toast toast--${type}`;
        element.setAttribute('role', 'status');
        element.innerHTML = `<span class="ms" aria-hidden="true">${icon}</span><span class="toast__text"></span>`;
        element.querySelector('.toast__text').textContent = message;
        host.append(element);

        dismissLater(element, 4600);
    }

    function dismissLater(element, delay) {
        // Serwerowe toasty znikają animacją CSS; tu sprzątamy element po jej zakończeniu.
        setTimeout(() => {
            element.classList.add('is-leaving');
            setTimeout(() => element.remove(), 300);
        }, delay);
    }

    doc.querySelectorAll('[data-toasts] .toast').forEach((element) => dismissLater(element, 4600));

    /* ---------------------------------------------------------- Powiadomienia */
    const notif = () => doc.querySelector('[data-notif]');

    function applyUnread(count) {
        const badge = doc.querySelector('[data-notif-count]');
        const summary = doc.querySelector('.notif__trigger');
        if (!badge) {
            return;
        }

        badge.textContent = count > 9 ? '9+' : String(count);
        badge.hidden = count <= 0;
        if (count > 0) {
            void badge.offsetWidth;
            badge.style.animation = 'none';
            void badge.offsetWidth;
            badge.style.animation = '';
        }
        summary?.setAttribute('aria-label', count > 0 ? `Powiadomienia (${count} nowych)` : 'Powiadomienia');
        doc.querySelector('[data-notif-read-all] button')?.toggleAttribute('disabled', count <= 0);
    }

    /** Pobiera aktualny panel powiadomień z serwera i podmienia go, nie zamykając otwartego dzwonka. */
    async function refreshNotifications() {
        const host = notif();
        if (!host) {
            return;
        }

        try {
            const response = await fetch('/notifications', { credentials: 'same-origin', headers: { 'X-Requested-With': 'fetch' } });
            if (!response.ok) {
                return;
            }

            const next = new DOMParser().parseFromString(await response.text(), 'text/html').querySelector('[data-notif]');
            const panel = host.querySelector('.notif__panel');
            const nextPanel = next?.querySelector('.notif__panel');
            if (panel && nextPanel) {
                panel.replaceWith(nextPanel);
            }

            const nextBadge = next?.querySelector('[data-notif-count]');
            applyUnread(nextBadge && !nextBadge.hidden ? parseInt(nextBadge.textContent, 10) || 0 : 0);
        } catch {
            /* odświeżenie jest opcjonalne */
        }
    }

    // Zamknięcie dzwonka: klik poza panelem lub Escape (z powrotem do przycisku).
    doc.addEventListener('click', (event) => {
        const open = doc.querySelector('[data-notif][open]');
        if (open && !open.contains(event.target)) {
            open.removeAttribute('open');
        }
    });

    doc.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        const open = doc.querySelector('[data-notif][open]');
        if (open) {
            open.removeAttribute('open');
            open.querySelector('summary')?.focus();
        }
    });

    // Otwarcie dzwonka zawsze pokazuje świeże dane.
    doc.addEventListener('toggle', (event) => {
        if (event.target.matches?.('[data-notif]') && event.target.open) {
            refreshNotifications();
        }
    }, true);

    doc.addEventListener('submit', async (event) => {
        const form = event.target;
        const single = form.matches('[data-notif-read]');
        const all = form.matches('[data-notif-read-all]');
        if (!single && !all) {
            return;
        }

        event.preventDefault();
        const result = await post(form);
        if (!result.ok) {
            toast(result.data.message ?? 'Nie udało się zapisać.', 'error');
            return;
        }

        const items = all ? doc.querySelectorAll('.notif__item.is-unread') : [form.closest('.notif__item')];
        items.forEach((item) => {
            item?.classList.remove('is-unread');
            item?.querySelector('.notif__dot')?.setAttribute('disabled', '');
        });
        applyUnread(result.data.unread ?? 0);
    });

    /* --------------------------------------------------------- Pokaż hasło */
    doc.addEventListener('click', (event) => {
        const button = event.target.closest('.auth-input__action');
        if (!button) {
            return;
        }

        const input = button.closest('.input-group')?.querySelector('input');
        if (!input) {
            return;
        }

        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        button.setAttribute('aria-label', visible ? 'Ukryj hasło' : 'Pokaż hasło');
        button.setAttribute('aria-pressed', String(visible));
        const icon = button.querySelector('.ms');
        if (icon) {
            icon.textContent = visible ? 'visibility_off' : 'visibility';
        }
        input.focus();
    });

    /**
     * Zmniejsza zdjęcie przed wysłaniem (maks. `maxSide` px, JPEG) — telefony robią zdjęcia po kilka MB,
     * a serwer przyjmuje do 2 MB. Zwraca Blob; przy błędzie dekodowania oryginalny plik.
     */
    async function resizeImage(file, maxSide = 1600, quality = 0.86) {
        if (!file.type.startsWith('image/')) {
            return file;
        }

        try {
            const bitmap = await createImageBitmap(file);
            const scale = Math.min(1, maxSide / Math.max(bitmap.width, bitmap.height));
            const canvas = doc.createElement('canvas');
            canvas.width = Math.round(bitmap.width * scale);
            canvas.height = Math.round(bitmap.height * scale);
            canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
            bitmap.close?.();

            return await new Promise((resolve) => canvas.toBlob((blob) => resolve(blob ?? file), 'image/jpeg', quality));
        } catch {
            return file;
        }
    }

    /* ----------------------------------------------------------------- Motyw */
    const root = doc.documentElement;
    const systemDark = window.matchMedia('(prefers-color-scheme: dark)');
    const effectiveTheme = () => (['light', 'dark'].includes(root.dataset.theme) ? root.dataset.theme : (systemDark.matches ? 'dark' : 'light'));

    /** Ustawia przyciski motywu zgodnie z motywem widocznym teraz (też systemowym, którego serwer nie zna). */
    function syncThemeToggles() {
        const dark = effectiveTheme() === 'dark';
        doc.querySelectorAll('[data-theme-form] button[name="theme"]').forEach((button) => {
            button.value = dark ? 'light' : 'dark';
            button.setAttribute('aria-pressed', String(dark));
            const icon = button.querySelector('.ms');
            if (icon) {
                icon.textContent = dark ? 'light_mode' : 'dark_mode';
            }
        });
    }

    function applyTheme(theme) {
        root.dataset.theme = theme;
        doc.querySelector('meta[name="color-scheme"]')?.setAttribute('content', ['light', 'dark'].includes(theme) ? theme : 'light dark');
        syncThemeToggles();
    }

    syncThemeToggles();
    // Bez wymuszonego wyboru strona zmienia motyw razem z systemem (CSS robi to sam przez color-scheme).
    systemDark.addEventListener('change', syncThemeToggles);

    doc.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!form.matches('[data-theme-form]')) {
            return;
        }

        event.preventDefault();
        const previous = root.dataset.theme;
        const next = effectiveTheme() === 'dark' ? 'light' : 'dark';
        // Wybór zgodny z systemem nie jest zapisywany: strona wraca do śledzenia ustawień komputera.
        const choice = next === (systemDark.matches ? 'dark' : 'light') ? 'system' : next;

        // Płynne przenikanie motywów tam, gdzie przeglądarka ma View Transitions, a ruch nie jest ograniczony.
        if (doc.startViewTransition && !reduceMotion) {
            doc.startViewTransition(() => applyTheme(choice));
        } else {
            applyTheme(choice);
        }

        const result = await post(form, { theme: choice });
        if (!result.ok) {
            applyTheme(previous);
            toast(result.data.message ?? 'Nie udało się zapisać motywu.', 'error');
        }
    });

    window.Komorebi = { request, post, fetchDocument, swap, play, toast, sleep, reduceMotion, applyUnread, refreshNotifications, resizeImage };
})();
