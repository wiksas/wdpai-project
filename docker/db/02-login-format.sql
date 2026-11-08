-- Format loginu wybieranego przy rejestracji (zgodny z AuthController::LOGIN_PATTERN):
-- zaczyna się literą, potem litery, cyfry, `.`, `_`, `-`; razem 3–30 znaków, zapisany małymi literami.
-- Login bez `@` nie pomyli się z e-mailem, bo logowanie przyjmuje jedno albo drugie. Idempotentne.

DO $$
DECLARE
    account   RECORD;
    base      TEXT;
    candidate TEXT;
    suffix    INTEGER;
BEGIN
    -- Konta założone, zanim formularz miał pole „Login” (login = e-mail), dostają login
    -- z części adresu przed `@`; przy kolizji z numerem na końcu (wiktor, wiktor2, …).
    FOR account IN
        SELECT id, email FROM users WHERE login !~ '^[a-z][a-z0-9._-]{2,29}$' ORDER BY id
    LOOP
        base := regexp_replace(lower(split_part(account.email, '@', 1)), '[^a-z0-9._-]', '', 'g');
        IF base !~ '^[a-z]' THEN
            base := 'u' || base;
        END IF;
        -- left() skraca do 26 znaków (zostaje miejsce na numer); rpad() tylko dopełnia zbyt krótki login.
        base := left(base, 26);
        IF length(base) < 3 THEN
            base := rpad(base, 3, '0');
        END IF;

        candidate := base;
        suffix := 1;
        WHILE EXISTS (SELECT 1 FROM users WHERE lower(login) = candidate AND id <> account.id) LOOP
            suffix := suffix + 1;
            candidate := base || suffix;
        END LOOP;

        UPDATE users SET login = candidate WHERE id = account.id;
    END LOOP;

    IF NOT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'users_login_format') THEN
        ALTER TABLE users
            ADD CONSTRAINT users_login_format CHECK (login ~ '^[a-z][a-z0-9._-]{2,29}$');
    END IF;
END
$$;
