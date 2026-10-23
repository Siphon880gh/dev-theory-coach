<?php

declare(strict_types=1);

/**
 * Small PHP 8 string helpers for MAMP PHP 7.4.33.
 * Loaded via Composer files autoload and via src/bootstrap.php when vendor is skipped.
 */

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return true;
        }
        $len = strlen($needle);

        return substr($haystack, -$len) === $needle;
    }
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Install directory from SCRIPT_NAME when it ends with /index.php.
 * Empty string at web root or under PHPUnit (SCRIPT_NAME is not .../index.php).
 */
function app_base(): string
{
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    if (preg_match('#^(.*)/index\.php$#', $script, $m) !== 1) {
        return '';
    }
    $base = rtrim($m[1], '/');

    return $base === '/' ? '' : $base;
}

/**
 * App-relative path → absolute URL under the install base (pretty paths).
 */
function url(string $path): string
{
    if ($path === '' || $path[0] !== '/') {
        $path = '/' . ltrim($path, '/');
    }
    $base = app_base();
    if ($path === '/') {
        return $base === '' ? '/' : $base . '/';
    }

    return $base . $path;
}

/**
 * Browser asset under /assets/ (repo root assets/, not public/assets).
 */
function asset_url(string $path): string
{
    return app_base() . '/assets/' . ltrim($path, '/');
}

/**
 * Logical route path after stripping app_base() from REQUEST_URI.
 */
function request_path(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (!is_string($uri) || $uri === '') {
        $uri = '/';
    }
    $path = rawurldecode($uri);
    $path = rtrim($path, '/') ?: '/';

    $base = app_base();
    if ($base !== '') {
        if ($path === $base) {
            return '/';
        }
        if (str_starts_with($path, $base . '/')) {
            $stripped = substr($path, strlen($base));
            return rtrim($stripped, '/') ?: '/';
        }
    }

    return $path;
}
