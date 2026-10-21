<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Rejestracja'; ?></title>
</head>
<body>
    <main>
        <section>
            <h1><?= $pageTitle; ?></h1>
            <form action="/register" method="POST">
                <div>
                    <label for="name">Imię i nazwisko:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div>
                    <label for="email">Adres e-mail:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="password">Hasło:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Utwórz konto</button>
            </form>
            <p>Masz już konto? <a href="/login">Zaloguj się</a></p>
        </section>
    </main>
</body>
</html>