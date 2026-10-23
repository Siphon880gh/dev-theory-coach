<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\Dashboard;
use TheoryCore\ProgressSchema;

final class IndexEntryTest extends TestCase
{
    public function testIndexPhpExistsAndDelegatesHomeToDashboard(): void
    {
        $path = dirname(__DIR__) . '/index.php';
        $this->assertFileExists($path);
        $this->assertIsReadable($path);

        $raw = file_get_contents($path);
        $this->assertNotFalse($raw);
        $this->assertStringContainsString('declare(strict_types=1)', $raw);
        $this->assertStringContainsString('Dashboard::homeHtml()', $raw);
        $html = Dashboard::homeHtml(ProgressSchema::fresh());
        $this->assertStringContainsString('Dev Theory Coach', $html);
        $this->assertStringNotContainsString('THEORY_CORE', $html);
        $this->assertStringContainsString('site-header', $html);
    }

    public function testNoRouterPhpAndServeUsesIndex(): void
    {
        $root = dirname(__DIR__);
        $this->assertFileDoesNotExist($root . '/router.php');
        $composer = json_decode((string) file_get_contents($root . '/composer.json'), true);
        $this->assertSame('php -S localhost:8080 index.php', $composer['scripts']['serve'] ?? null);
        $raw = (string) file_get_contents($root . '/index.php');
        $this->assertStringContainsString("PHP_SAPI === 'cli-server'", $raw);
        $this->assertStringContainsString('request_path()', $raw);
    }
}
