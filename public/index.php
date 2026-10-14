<?php

declare(strict_types=1);

$message = "Hi There! Welcome to my WDPAI project!";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My first Docker app</title>
</head>
<body>
    <h1><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></h1>
</body>
</html>