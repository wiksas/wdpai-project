<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Logowanie'; ?></title>
</head>
<body>
    <main>
        <section>
            <h1><?= $pageTitle; ?></h1>
            <form action="/login" method="POST">
                <div>
                    <label for="email">Adres e-mail:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="password">Hasło:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Zaloguj się</button>
            </form>
            <p>Nie masz konta? <a href="/register">Zarejestruj się</a></p>
        </section>
    </main>
</body>
</html>