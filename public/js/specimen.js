/**
 * Karta okazu: dodawanie i usuwanie zdjęć (ze zmniejszeniem przed wysyłką), drukowanie, udostępnianie.
 * Zabiegi (podlanie) obsługuje collection.js. Wymaga app.js (`window.Komorebi`).
 */
(() => {
    'use strict';

    const K = window.Komorebi;
    if (!K) {
        return;
    }

    const doc = document;

    async function refreshPhotos() {
        try {
            const next = await K.fetchDocument(location.href);
            K.swap(next, ['[data-photos]']);
            K.play(doc.querySelector('[data-photos] .passport-photos__item'), 'fx-in');
        } catch {
            /* odświeżenie jest opcjonalne */
        }
    }

    // Dodawanie zdjęcia: wybór pliku od razu wysyła zmniejszoną wersję
    doc.addEventListener('change', async (event) => {
        const input = event.target.closest('[data-photo-input]');
        const form = input?.form;
        const file = input?.files?.[0];
        if (!form || !file) {
            return;
        }

        const label = form.querySelector('label');
        label?.classList.add('is-busy');

        const blob = await K.resizeImage(file);
        const body = new FormData(form);
        body.set('photo', blob, 'photo.jpg');

        const result = await K.request(form.getAttribute('action'), { method: 'POST', body });
        label?.classList.remove('is-busy');
        input.value = '';

        if (!result.ok) {
            K.toast(result.data.message ?? 'Nie udało się dodać zdjęcia.', 'error');
            return;
        }

        K.toast(result.data.message, 'success');
        await refreshPhotos();
    });

    // Usuwanie zdjęcia
    doc.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-photo-delete]');
        if (!form) {
            return;
        }

        event.preventDefault();
        const item = form.closest('.passport-photos__item');
        item?.classList.add('is-removing');
        const [result] = await Promise.all([K.post(form), K.sleep(K.reduceMotion ? 0 : 220)]);

        if (!result.ok) {
            item?.classList.remove('is-removing');
            K.toast(result.data.message ?? 'Nie udało się usunąć zdjęcia.', 'error');
            return;
        }

        K.toast(result.data.message, 'info');
        await refreshPhotos();
    });

    // Drukowanie i udostępnianie
    doc.addEventListener('click', async (event) => {
        if (event.target.closest('[data-print]')) {
            window.print();
            return;
        }

        const share = event.target.closest('[data-share]');
        if (!share) {
            return;
        }

        const data = { title: share.dataset.shareTitle ?? doc.title, url: location.href };
        try {
            if (navigator.share) {
                await navigator.share(data);
                return;
            }

            await navigator.clipboard.writeText(data.url);
            K.toast('Skopiowano link do karty okazu.', 'info');
        } catch (error) {
            if (error?.name !== 'AbortError') {
                K.toast('Nie udało się udostępnić linku.', 'error');
            }
        }
    });
})();
