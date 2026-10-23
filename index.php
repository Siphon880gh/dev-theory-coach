<?php

declare(strict_types=1);

require __DIR__ . '/src/bootstrap.php';

// php -S localhost:8080 index.php: let the built-in server serve real asset files.
if (PHP_SAPI === 'cli-server') {
    $staticPath = request_path();
    if (preg_match('#^(?:/public)?/assets/#', $staticPath) === 1 && strpos($staticPath, '..') === false
        && is_file(__DIR__ . $staticPath)) {
        return false;
    }
}

use TheoryCore\CaseStudy;
use TheoryCore\Cheatsheet;
use TheoryCore\Coach;
use TheoryCore\Dashboard;
use TheoryCore\Drill;
use TheoryCore\Guides;
use TheoryCore\Quiz;
use TheoryCore\UnknownPage;

$path = request_path();

header('Content-Type: text/html; charset=UTF-8');

if ($path === '/guides') {
    echo Guides::hubHtml();
    exit;
}

if (preg_match('#^/guides/([a-z0-9-]+)$#', $path, $m) === 1) {
    $response = Guides::pageResponse($m[1]);
    http_response_code((int) ($response['status'] ?? 200));
    echo $response['body'] ?? '';
    exit;
}

if (preg_match('#^/coach/([a-z0-9-]+)$#', $path, $m) === 1) {
    $response = Coach::pageResponse($m[1]);
    http_response_code((int) ($response['status'] ?? 200));
    echo $response['body'] ?? '';
    exit;
}

if (preg_match('#^/quiz/([a-z0-9-]+)$#', $path, $m) === 1) {
    $response = Quiz::pageResponse($m[1]);
    http_response_code((int) ($response['status'] ?? 200));
    echo $response['body'] ?? '';
    exit;
}

if (preg_match('#^/drill/([a-z0-9-]+)$#', $path, $m) === 1) {
    $response = Drill::pageResponse($m[1]);
    http_response_code((int) ($response['status'] ?? 200));
    echo $response['body'] ?? '';
    exit;
}

if (preg_match('#^/case/([a-z0-9-]+)$#', $path, $m) === 1) {
    $response = CaseStudy::pageResponse($m[1]);
    http_response_code((int) ($response['status'] ?? 200));
    echo $response['body'] ?? '';
    exit;
}

if (preg_match('#^/cheatsheet/([a-z0-9-]+)$#', $path, $m) === 1) {
    $response = Cheatsheet::pageResponse($m[1]);
    http_response_code((int) ($response['status'] ?? 200));
    echo $response['body'] ?? '';
    exit;
}

if ($path !== '/') {
    http_response_code(200);
    echo UnknownPage::pathHtml($path);
    exit;
}

echo Dashboard::homeHtml();
