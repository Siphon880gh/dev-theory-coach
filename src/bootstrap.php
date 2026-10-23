<?php

declare(strict_types=1);

if (PHP_VERSION_ID < 70400) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Dev Theory Coach needs PHP 7.4 or newer.\n";
    echo 'This request is using PHP ' . PHP_VERSION . ".\n\n";
    echo "MAMP: Preferences → PHP → 7.4.33 or newer, then restart Apache.\n";
    exit(1);
}

const THEORY_CORE_ROOT = __DIR__ . '/..';

$autoload = THEORY_CORE_ROOT . '/vendor/autoload.php';
// Dev dependencies (PHPUnit 11) need PHP 8.2+. On PHP 7.4, bypass Composer
// platform checks and use the small app autoloader below instead.
if (PHP_VERSION_ID >= 80200 && is_file($autoload)) {
    require $autoload;

    return;
}

// No production packages. Composer stays optional so Apache/MAMP can serve
// this folder immediately. Composer is still useful for PHPUnit on PHP 8.2+.
require THEORY_CORE_ROOT . '/src/helpers.php';
spl_autoload_register(static function (string $class): void {
    $prefix = 'TheoryCore\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = THEORY_CORE_ROOT . '/src/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});
