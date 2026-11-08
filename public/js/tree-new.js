/**
 * Formularz nowego drzewka: zmniejsza wybrane zdjęcie przed wysłaniem, żeby zmieściło się w limicie 2 MB.
 * Bez JS formularz działa tak samo, tylko duże zdjęcie zostanie odrzucone przez serwer.
 */
(() => {
    'use strict';

    const K = window.Komorebi;
    const input = document.querySelector('[data-tree-photo]');
    if (!K || !input) {
        return;
    }

    input.addEventListener('change', async () => {
        const file = input.files?.[0];
        if (!file || !file.type.startsWith('image/')) {
            return;
        }

        const blob = await K.resizeImage(file);
        const transfer = new DataTransfer();
        transfer.items.add(new File([blob], 'photo.jpg', { type: blob.type || 'image/jpeg' }));
        input.files = transfer.files;
    });
})();
