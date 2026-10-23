<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\CaseStudy;
use TheoryCore\Cheatsheet;
use TheoryCore\Coach;
use TheoryCore\Drill;
use TheoryCore\Quiz;
use TheoryCore\UnknownPage;

final class UnknownPageTest extends TestCase
{
    public function testUnknownSlugsOnAllResourceRoutesAre200NoRedirect(): void
    {
        foreach ([
            Coach::pageResponse('not-a-slug'),
            Quiz::pageResponse('not-a-slug'),
            Drill::pageResponse('not-a-slug'),
            CaseStudy::pageResponse('not-a-slug'),
            Cheatsheet::pageResponse('not-a-slug'),
        ] as $r) {
            $this->assertSame(200, $r['status']);
            $this->assertArrayNotHasKey('location', $r);
            $this->assertStringContainsString('href="' . url('/') . '"', $r['body']);
        }
    }

    public function testPathHtmlEscapesAndLinksHome(): void
    {
        $html = UnknownPage::pathHtml('/x<y>');
        $this->assertStringContainsString('href="' . url('/') . '"', $html);
        $this->assertStringContainsString('/x&lt;y&gt;', $html);
    }

    public function testIndexHasNoRedirects(): void
    {
        $raw = (string) file_get_contents(dirname(__DIR__) . '/index.php');
        $this->assertStringNotContainsString('Location:', $raw);
        $this->assertStringNotContainsString('302', $raw);
        $this->assertStringContainsString('UnknownPage::pathHtml', $raw);
    }
}
