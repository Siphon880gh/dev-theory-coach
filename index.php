<?php

declare(strict_types=1);

$autoload = __DIR__ . '/vendor/autoload.php';
if (is_file($autoload)) {
    require $autoload;
}

use TheoryCore\Coach;
use TheoryCore\Guides;
use TheoryCore\Quiz;

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rawurldecode($uri);
$path = rtrim($path, '/') ?: '/';

header('Content-Type: text/html; charset=UTF-8');

if ($path === '/guides') {
    echo Guides::hubHtml();
    exit;
}

if (preg_match('#^/guides/([a-z0-9-]+)$#', $path, $m) === 1) {
    $response = Guides::pageResponse($m[1]);
    if (($response['status'] ?? 200) === 302) {
        header('Location: ' . ($response['location'] ?? '/'), true, 302);
        exit;
    }
    http_response_code((int) ($response['status'] ?? 200));
    echo $response['body'] ?? '';
    exit;
}

if (preg_match('#^/coach/([a-z0-9-]+)$#', $path, $m) === 1) {
    $response = Coach::pageResponse($m[1]);
    if (($response['status'] ?? 200) === 302) {
        header('Location: ' . ($response['location'] ?? '/'), true, 302);
        exit;
    }
    http_response_code((int) ($response['status'] ?? 200));
    echo $response['body'] ?? '';
    exit;
}

if (preg_match('#^/quiz/([a-z0-9-]+)$#', $path, $m) === 1) {
    $response = Quiz::pageResponse($m[1]);
    if (($response['status'] ?? 200) === 302) {
        header('Location: ' . ($response['location'] ?? '/'), true, 302);
        exit;
    }
    http_response_code((int) ($response['status'] ?? 200));
    echo $response['body'] ?? '';
    exit;
}

if ($path !== '/') {
    header('Location: /', true, 302);
    exit;
}
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
        <p><a href="/guides">Open Guides hub</a></p>
    </main>
</body>
</html>
