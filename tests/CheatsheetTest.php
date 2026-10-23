<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\Catalog;
use TheoryCore\Cheatsheet;

final class CheatsheetTest extends TestCase
{
    protected function setUp(): void
    {
        Catalog::reset();
    }

    public function testStringsAndArraysSheetsRender(): void
    {
        $strings = Cheatsheet::pageResponse('strings');
        $this->assertSame(200, $strings['status']);
        $this->assertStringNotContainsString('not authored yet', $strings['body'] ?? '');
        $this->assertStringContainsString('Pick when:', $strings['body'] ?? '');

        $sheet = Cheatsheet::load('arrays');
        $this->assertNotNull($sheet);
        $this->assertLessThanOrEqual(12, count($sheet['lines']));
        $html = Cheatsheet::renderHtml($sheet);
        $this->assertStringContainsString('Pick when:', $html);
        $this->assertStringContainsString('Skip when:', $html);

        $skill = file_get_contents(dirname(__DIR__) . '/.agents/skills/add-cheatsheet/SKILL.md');
        $this->assertNotFalse($skill);
        $this->assertStringNotContainsString('TODO', $skill);
    }

    public function testEveryFamilyHasCheatsheet(): void
    {
        foreach (Catalog::slugs() as $slug) {
            $sheet = Cheatsheet::load($slug);
            $this->assertNotNull($sheet, $slug);
            Cheatsheet::assertValid($sheet);
            $this->assertSame($slug, $sheet['slug']);
        }
    }
}
