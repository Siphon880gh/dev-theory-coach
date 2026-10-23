<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;

final class IndexEntryTest extends TestCase
{
    public function testIndexPhpExistsAndIsReadable(): void
    {
        $path = dirname(__DIR__) . '/index.php';
        $this->assertFileExists($path);
        $this->assertIsReadable($path);

        $raw = file_get_contents($path);
        $this->assertNotFalse($raw);
        $this->assertStringContainsString('declare(strict_types=1)', $raw);
        $this->assertStringContainsString('SAMPLE', $raw);
        $this->assertStringContainsString('Dev Theory Coach', $raw);
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
