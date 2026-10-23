<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\Catalog;

final class CatalogTest extends TestCase
{
    public function testFamilySlugsMatchResourcesMdOrderAndSpelling(): void
    {
        $expected = $this->slugsFromResourcesMd();
        $actual = Catalog::slugs();

        $this->assertSame(
            $expected,
            $actual,
            'content/families.json slugs must match the slug column of RESOURCES.md in order and spelling',
        );
    }

    /** @return list<string> */
    private function slugsFromResourcesMd(): array
    {
        $path = dirname(__DIR__) . '/RESOURCES.md';
        $raw = file_get_contents($path);
        $this->assertNotFalse($raw, 'RESOURCES.md must be readable');

        if (!preg_match_all('/^\|\s*\d+\s*\|\s*`([^`]+)`\s*\|/m', $raw, $matches)) {
            $this->fail('Could not parse slug column from RESOURCES.md');
        }

        return $matches[1];
    }
}
