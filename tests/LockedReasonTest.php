<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\Catalog;
use TheoryCore\Guides;
use TheoryCore\Path;
use TheoryCore\ProgressSchema;

final class LockedReasonTest extends TestCase
{
    protected function setUp(): void
    {
        Catalog::reset();
    }

    public function testLockedRowsNameMissingSlugs(): void
    {
        $state = ProgressSchema::fresh();
        $this->assertStringContainsString('arrays', Guides::lockedReasonFor('strings', $state));
        $reason = Guides::lockedReasonFor('caching', $state);
        foreach (['read-and-write-paths', 'hash-maps', 'linked-lists'] as $slug) {
            $this->assertStringContainsString($slug, $reason);
        }

        $hub = Guides::hubHtmlWithLocks($state);
        $this->assertStringContainsString('Locked — missing: arrays', $this->liFor($hub, 'strings'));

        $rows = Path::rowsHtml(Path::load('structures'), $state);
        $this->assertStringContainsString('Locked — missing: arrays', $this->liFor($rows, 'strings'));
    }

    public function testUnlockedRowsOmitMissingSentence(): void
    {
        $state = ProgressSchema::fresh();
        $this->assertSame('', Guides::lockedReasonFor('arrays', $state));
        $hub = Guides::hubHtmlWithLocks($state);
        $li = $this->liFor($hub, 'arrays');
        $this->assertStringNotContainsString('locked-reason', $li);
        $this->assertStringNotContainsString('Locked — missing', $li);
    }

    private function liFor(string $html, string $slug): string
    {
        if (!preg_match('/<li data-slug="' . preg_quote($slug, '/') . '">.*?<\/li>/s', $html, $m)) {
            $this->fail('missing li for ' . $slug);
        }

        return $m[0];
    }
}
