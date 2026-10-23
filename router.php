<?php

declare(strict_types=1);

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = rawurldecode($uri);

// Serve static assets under assets/ or public/assets/ when present (php -S).
if (preg_match('#^(?:/public)?/assets(/.*)$#', $uri, $m) === 1) {
    $roots = [];
    $assetsDir = realpath(__DIR__ . '/assets');
    if ($assetsDir !== false) {
        $roots[] = $assetsDir;
    }
    $publicAssets = realpath(__DIR__ . '/public/assets');
    if ($publicAssets !== false) {
        $roots[] = $publicAssets;
    }

    foreach ($roots as $assetRoot) {
        $file = realpath($assetRoot . $m[1]);
        if ($file !== false && strpos($file, $assetRoot . DIRECTORY_SEPARATOR) === 0 && is_file($file)) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $types = [
                'css' => 'text/css; charset=UTF-8',
                'js' => 'text/javascript; charset=UTF-8',
                'map' => 'application/json',
                'svg' => 'image/svg+xml',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'ico' => 'image/x-icon',
                'woff' => 'font/woff',
                'woff2' => 'font/woff2',
            ];
            header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
            header('Content-Length: ' . (string) filesize($file));
            readfile($file);

            return true;
        }
    }
}

require __DIR__ . '/index.php';
