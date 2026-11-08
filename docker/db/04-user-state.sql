-- Stan aplikacji przypisany do konta: kolekcja, zabiegi, skany, zdjęcia, dziennik, rozmowa z konsultantem.
-- Jeden wiersz na użytkownika, dokument JSONB. Wiersz znika razem z kontem. Polecenia są idempotentne.
-- Stare pliki storage/state/user-{id}.json aplikacja importuje sama przy pierwszym użyciu konta.

CREATE TABLE IF NOT EXISTS user_state (
    user_id    INTEGER     PRIMARY KEY REFERENCES users (id) ON DELETE CASCADE,
    state      JSONB       NOT NULL DEFAULT '{}'::jsonb,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);
