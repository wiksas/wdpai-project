/**
 * Karta okazu: zdjęcia (dodawanie ze zmniejszeniem, usuwanie), skan kondycji, harmonogram okazu (odhaczanie), drukowanie, udostępnianie.
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

    // Harmonogram okazu: odhaczanie zadania zapisuje od razu, a odświeżenie sekcji pokazuje stan z serwera
    async function refreshSchedule() {
        try {
            const next = await K.fetchDocument(location.pathname);
            K.swap(next, ['[data-tree-schedule]']);
        } catch {
            location.reload();
        }
    }

    doc.addEventListener('change', (event) => {
        if (event.target.matches?.('.passport-schedule__check')) {
            event.target.form.requestSubmit();
        }
    });

    doc.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-tree-task]');
        if (!form) {
            return;
        }
        event.preventDefault();

        const result = await K.post(form);
        if (!result.ok) {
            K.toast(result.data.message ?? 'Nie udało się zapisać zadania.', 'error');
        }
        await refreshSchedule();
    });

    // Skan kondycji: nowe zdjęcie (wybór pliku od razu skanuje) albo zdjęcie już dodane do karty
    async function refreshScan() {
        try {
            const next = await K.fetchDocument(location.pathname);
            K.swap(next, ['[data-passport-scan]', '[data-photos]', '[data-tree-schedule]']);
            K.play(doc.querySelector('[data-passport-scan] .passport-scan__result'), 'fx-in');
        } catch {
            location.reload();
        }
    }

    function setScanning(section, on) {
        section?.classList.toggle('is-scanning', on);
        section?.querySelectorAll('[data-scan-form] button, [data-scan-form] label').forEach((control) => {
            control.toggleAttribute('disabled', on);
            control.classList.toggle('is-busy', on);
        });
        const status = section?.querySelector('[data-scan-status]');
        if (status) {
            status.hidden = !on;
        }
    }

    doc.addEventListener('change', (event) => {
        const input = event.target.closest?.('[data-scan-input]');
        if (input?.files?.length) {
            input.form.requestSubmit();
        }
    });

    doc.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-scan-form]');
        if (!form) {
            return;
        }
        event.preventDefault();

        const body = new FormData(form);
        if (event.submitter?.name === 'photo_id') {
            body.set('photo_id', event.submitter.value);
        }
        const file = body.get('photo');
        if (body.get('photo_id')) {
            body.delete('photo');
        } else if (file instanceof File && file.size > 0) {
            body.set('photo', await K.resizeImage(file), 'photo.jpg');
        } else {
            K.toast('Wybierz zdjęcie do zeskanowania.', 'error');
            return;
        }

        const section = doc.querySelector('[data-passport-scan]');
        setScanning(section, true);
        doc.querySelector('[data-scan-status]')?.scrollIntoView({ block: 'nearest', behavior: K.reduceMotion ? 'auto' : 'smooth' });
        const result = await K.request(form.getAttribute('action'), { method: 'POST', body });

        if (!result.ok) {
            setScanning(section, false);
            K.toast(result.data.message ?? 'Nie udało się zeskanować zdjęcia. Spróbuj ponownie.', 'error');
            if (body.get('photo')) {
                await refreshScan();
            }
            return;
        }

        K.toast(result.data.message, 'success');
        await refreshScan();
        doc.querySelector('[data-passport-scan] .passport-scan__result')?.scrollIntoView({ block: 'nearest', behavior: K.reduceMotion ? 'auto' : 'smooth' });
    });

    doc.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-scan-schedule]');
        if (!form) {
            return;
        }
        event.preventDefault();

        const button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        const result = await K.post(form);

        if (!result.ok) {
            button.disabled = false;
            K.toast(result.data.message ?? 'Nie udało się dodać kroków. Spróbuj ponownie.', 'error');
            return;
        }
        K.toast(result.data.message, 'success');
        await refreshScan();
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
