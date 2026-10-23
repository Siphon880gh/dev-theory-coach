<?php

declare(strict_types=1);

$autoload = __DIR__ . '/vendor/autoload.php';
if (is_file($autoload)) {
    require $autoload;
}

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rawurldecode($uri);
$path = rtrim($path, '/') ?: '/';

if ($path !== '/') {
    header('Location: /', true, 302);
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dev Theory Coach (THEORY_CORE)</title>
</head>
<body>
    <main>
        <p><strong>SAMPLE</strong> data only.</p>
        <h1>Dev Theory Coach (THEORY_CORE)</h1>
        <p>Developer interview concepts — guides, coach, quizzes. Runtime stub.</p>
    </main>
</body>
</html>
