<?php
/**
 * Panel marki na stronach logowania i rejestracji (lewa kolumna od 1025 px, niżej pod formularzem).
 * Ciemny pas (`--c-band`) w obu motywach: rysunek tuszem leży na nim jak na kartce.
 */
?>
<aside class="auth-panel" aria-labelledby="auth-panel-title">
    <a href="/" class="brand auth-panel__brand" aria-label="KOMOREBI, strona główna">
        <span class="brand__mark auth-panel__mark"><span class="ms" aria-hidden="true">potted_plant</span></span>
        <span class="brand__name auth-panel__name">KOMOREBI</span>
    </a>

    <figure class="auth-panel__art">
        <img src="/images/ink-bonsai-paper.jpg" alt="Rysunek tuszem: sosna bonsai w płaskiej donicy na czerpanym papierze" width="264" height="264">
    </figure>

    <h2 class="auth-panel__title" id="auth-panel-title">
        <span class="term">Komorebi <span lang="ja">木漏れ日</span></span>
    </h2>
    <p class="auth-panel__lead">Światło przesączające się przez liście. Tu prowadzisz dziennik swoich drzew.</p>

    <ul class="auth-perks">
        <li class="auth-perk">
            <span class="ms" aria-hidden="true">potted_plant</span>
            <p><strong>Kolekcja.</strong> Karta każdego drzewa ze zdjęciami i dziennikiem zabiegów.</p>
        </li>
        <li class="auth-perk">
            <span class="ms" aria-hidden="true">calendar_month</span>
            <p><strong>Harmonogram.</strong> Podlewanie, nawożenie i cięcie rozpisane na kolejne dni.</p>
        </li>
        <li class="auth-perk">
            <span class="ms" aria-hidden="true">healing</span>
            <p><strong>Klinika.</strong> Opisz objawy, a baza wiedzy podpowie możliwe przyczyny.</p>
        </li>
    </ul>
</aside>
