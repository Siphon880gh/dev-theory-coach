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
        $this->assertStringContainsString('Dev Theory Coach', Dashboard::homeHtml(ProgressSchema::fresh()));
        $this->assertStringContainsString('SAMPLE', Dashboard::homeHtml(ProgressSchema::fresh()));
    }

    public function testRouterPhpRequiresIndex(): void
    {
        $path = dirname(__DIR__) . '/router.php';
        $this->assertFileExists($path);
        $raw = file_get_contents($path);
        $this->assertNotFalse($raw);
        $this->assertStringContainsString("require __DIR__ . '/index.php'", $raw);
    }
}
