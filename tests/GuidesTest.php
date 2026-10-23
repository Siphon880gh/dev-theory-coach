<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\Catalog;
use TheoryCore\Guides;
use TheoryCore\ProgressSchema;

final class GuidesTest extends TestCase
{
    protected function setUp(): void
    {
        Catalog::reset();
    }

    public function testGuidesHubListsEverySlugOnceUnderItsTrack(): void
    {
        $html = Guides::hubHtml();

        foreach (['Data structures', 'Algorithms', 'System design', 'Design patterns'] as $label) {
            $this->assertStringContainsString($label, $html);
        }

        foreach (Catalog::slugs() as $slug) {
            $this->assertSame(
                1,
                substr_count($html, 'data-slug="' . $slug . '"'),
                "slug {$slug} must appear exactly once on the hub",
            );
        }

        $groups = Catalog::groupedByTrackLabel();
        foreach ($groups as $label => $families) {
            $sectionStart = strpos($html, 'data-track-label="' . $label . '"');
            $this->assertNotFalse($sectionStart, $label);
            foreach ($families as $family) {
                $slug = $family['slug'];
                $pos = strpos($html, 'data-slug="' . $slug . '"');
                $this->assertNotFalse($pos);
                $this->assertGreaterThan($sectionStart, $pos, "{$slug} should sit under {$label}");
            }
        }
    }

    public function testGuidePageRendersArraysWithCoachCta(): void
    {
        $response = Guides::pageResponse('arrays');
        $this->assertSame(200, $response['status']);
        $body = $response['body'] ?? '';
        $this->assertStringContainsString('Arrays', $body);
        $this->assertStringContainsString('/coach/arrays', $body);
        $this->assertStringContainsString('Open coach:', $body);
        $guide = Guides::load('arrays');
        $this->assertNotNull($guide);
        $this->assertSame('arrays', $guide['relatedCoachSlug']);
        $this->assertStringContainsString('Rule.', $body);
        $this->assertStringContainsString('class="guide-diagram"', $body);
        $this->assertStringContainsString('data-diagram-id="insert-shift"', $body);
        $this->assertStringContainsString('<svg', $body);
        $this->assertStringNotContainsString('[[diagram:', $body);
        $this->assertDoesNotMatchRegularExpression('/\bthe prompt\b/i', $body);
        $this->assertStringContainsString('vague', $body);
        $this->assertStringContainsString('list', $body);
        $this->assertStringContainsString('linked list when you still need index access', $body);
        $this->assertStringContainsString('youtube.com/results', $body);
        $this->assertStringContainsString('search_query=Data+structures+Arrays', $body);
        $this->assertStringContainsString('target="_blank"', $body);
        $this->assertStringContainsString('rel="noopener noreferrer"', $body);
        $this->assertStringContainsString('Search YouTube: Data structures Arrays', $body);
    }

    public function testYoutubeSearchUrlEncodesTrackAndTitle(): void
    {
        $url = Guides::youtubeSearchUrl('Algorithms', 'Greedy');
        $this->assertStringContainsString('https://www.youtube.com/results?', $url);
        $this->assertStringContainsString('search_query=Algorithms+Greedy', $url);
        $over = Guides::youtubeSearchUrl('Algorithms', 'Greedy', 'custom query');
        $this->assertStringContainsString('search_query=custom+query', $over);
    }


    public function testGuideLessonNavShowsNextTitleOnArrays(): void
    {
        $body = Guides::pageResponse('arrays')['body'] ?? '';
        $this->assertStringContainsString('guide-lesson-nav', $body);
        $this->assertStringContainsString('aria-label="Lesson"', $body);
        $this->assertStringContainsString('Strings', $body);
        $this->assertStringContainsString('/guides/strings', $body);
        $this->assertStringContainsString('is-disabled', $body); // no previous
    }

    public function testGuideLessonNavShowsPrevAndNextOnHashMaps(): void
    {
        $body = Guides::pageResponse('hash-maps')['body'] ?? '';
        $this->assertStringContainsString('Strings', $body);
        $this->assertStringContainsString('/guides/strings', $body);
        $this->assertStringContainsString('Linked lists', $body);
        $this->assertStringContainsString('/guides/linked-lists', $body);
    }

    public function testGuideLessonNavLastHasNoNext(): void
    {
        $body = Guides::pageResponse('composition')['body'] ?? '';
        $this->assertStringContainsString('Proxy', $body);
        $this->assertStringContainsString('/guides/proxy', $body);
        // next disabled
        $this->assertMatchesRegularExpression(
            '/guide-lesson-nav__link--next is-disabled/',
            $body,
        );
    }

    public function testFormatBodyRendersDiagramFigureForArrays(): void
    {
        $guide = Guides::load('arrays');
        $this->assertNotNull($guide);
        /** @var list<array{id?: mixed, title?: mixed, svg?: mixed}> $diagrams */
        $diagrams = $guide['diagrams'] ?? [];
        $html = Guides::formatBody((string) $guide['body'], $diagrams);
        $this->assertStringContainsString('figure class="guide-diagram"', $html);
        $this->assertStringContainsString('insert-shift', $html);
        $this->assertStringContainsString('<svg', $html);
        $this->assertStringNotContainsString('[[diagram:', $html);
    }

    public function testSanitizeSvgStripsScriptAndEvents(): void
    {
        $dirty = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">'
            . '<script>alert(1)</script>'
            . '<rect x="0" y="0" width="10" height="10" onclick="evil()" fill="#fff"/>'
            . '<foreignObject></foreignObject>'
            . '</svg>';
        $safe = Guides::sanitizeSvg($dirty);
        $this->assertStringContainsString('<svg', $safe);
        $this->assertStringContainsString('<rect', $safe);
        $this->assertStringNotContainsString('script', strtolower($safe));
        $this->assertStringNotContainsString('onclick', strtolower($safe));
        $this->assertStringNotContainsString('foreignobject', strtolower($safe));
    }


    public function testStrategyGuideRendersMermaidAndBootScript(): void
    {
        $response = Guides::pageResponse('strategy');
        $body = $response['body'] ?? '';
        $this->assertStringContainsString('class="mermaid"', $body);
        $this->assertStringContainsString('flowchart', $body);
        $this->assertStringContainsString('mermaid.min.js', $body);
        $this->assertStringContainsString('mermaid-boot.js', $body);
        $this->assertStringContainsString('data-diagram-type="mermaid"', $body);
    }

    public function testComplexityGuideRendersImageDiagram(): void
    {
        $response = Guides::pageResponse('complexity');
        $body = $response['body'] ?? '';
        $this->assertStringContainsString('diagrams/complexity/growth.png', $body);
        $this->assertStringContainsString('data-diagram-type="image"', $body);
        $this->assertStringContainsString('<img', $body);
    }

    public function testUnknownGuideSlugRendersUnknownPageNot404OrRedirect(): void
    {
        $response = Guides::pageResponse('not-a-slug');
        $this->assertSame(200, $response['status']);
        $this->assertArrayNotHasKey('location', $response);
        $this->assertStringContainsString('not-a-slug', $response['body']);
        $this->assertStringContainsString('href="/"', $response['body']);
    }

    public function testBeginnerGuidesMeetWordCountPasteAndCoachSlugRules(): void
    {
        $curriculaNgrams = $this->twelveWordNgramsFromCurricula();

        foreach (ProgressSchema::BEGINNER_LEARNING_ORDER as $slug) {
            $guide = Guides::load($slug);
            $this->assertNotNull($guide, "missing guide for {$slug}");
            $this->assertGuideQuality($slug, $guide, $curriculaNgrams);
        }
    }

    public function testAllFamilyGuidesMeetFloorAndQualityGates(): void
    {
        $ngrams = $this->twelveWordNgramsFromCurricula();
        foreach (Catalog::all() as $family) {
            $slug = $family['slug'];
            $guide = Guides::load($slug);
            $this->assertNotNull($guide, "missing guide {$slug}");
            $this->assertGuideQuality($slug, $guide, $ngrams);
        }
    }

    /**
     * @param array<string, mixed> $guide
     * @param array<string, true> $curriculaNgrams
     */
    private function assertGuideQuality(string $slug, array $guide, array $curriculaNgrams): void
    {
        $this->assertSame($slug, $guide['relatedCoachSlug']);
        $this->assertArrayNotHasKey('sample', $guide, "{$slug} must not carry a sample stamp");
        if (isset($guide['meta']) && is_array($guide['meta'])) {
            $this->assertArrayNotHasKey('sample', $guide['meta'], "{$slug} meta.sample stamp forbidden");
        }

        $body = (string) $guide['body'];
        $words = preg_match_all("/\\b[\\w']+\\b/u", $body);
        $this->assertGreaterThanOrEqual(300, $words, "{$slug} body too short");
        $this->assertLessThanOrEqual(800, $words, "{$slug} body too long");

        $this->assertArrayHasKey('parts', $guide);
        foreach (['rule', 'why', 'example', 'use', 'refuse'] as $part) {
            $this->assertArrayHasKey($part, $guide['parts'], "{$slug} missing part {$part}");
            $this->assertNotSame('', trim((string) $guide['parts'][$part]));
        }

        $this->assertArrayHasKey('diagrams', $guide, "{$slug} missing diagrams");
        $this->assertIsArray($guide['diagrams']);
        $this->assertGreaterThanOrEqual(1, count($guide['diagrams']), "{$slug} needs ≥1 diagram");
        $seenIds = [];
        foreach ($guide['diagrams'] as $di => $diagram) {
            $this->assertIsArray($diagram, "{$slug} diagram {$di}");
            $this->assertNotSame('', trim((string) ($diagram['id'] ?? '')));
            $this->assertNotSame('', trim((string) ($diagram['title'] ?? '')));
            $type = strtolower((string) ($diagram['type'] ?? 'svg'));
            $svg = (string) ($diagram['svg'] ?? '');
            $mermaid = (string) ($diagram['mermaid'] ?? '');
            $src = (string) ($diagram['src'] ?? '');
            if ($type === 'mermaid' || ($mermaid !== '' && $svg === '')) {
                $this->assertNotSame('', trim($mermaid), "{$slug} mermaid diagram empty");
            } elseif ($type === 'image' || ($src !== '' && $svg === '')) {
                $this->assertNotSame('', trim($src), "{$slug} image diagram missing src");
                $this->assertFileExists(dirname(__DIR__) . '/assets/' . $src, "{$slug} image missing file");
            } else {
                $this->assertStringContainsString('<svg', $svg, "{$slug} diagram missing svg");
            }
            $this->assertMatchesRegularExpression(
                '/\d/',
                $svg . $mermaid . $src . (string) ($diagram['title'] ?? ''),
                "{$slug} diagram should carry a digit/label",
            );
            $seenIds[] = (string) $diagram['id'];
            $this->assertStringContainsString(
                '[[diagram:' . $diagram['id'] . ']]',
                $body,
                "{$slug} body missing marker for {$diagram['id']}",
            );
        }
        $this->assertSame(count($seenIds), count(array_unique($seenIds)), "{$slug} duplicate diagram ids");

        $haystacks = [$body];
        foreach ($guide['parts'] as $partText) {
            $haystacks[] = (string) $partText;
        }
        foreach ($haystacks as $text) {
            $this->assertDoesNotMatchRegularExpression(
                '/\\bSAMPLE\\b/i',
                $text,
                "{$slug} must not contain SAMPLE in learner-facing prose",
            );
            $this->assertDoesNotMatchRegularExpression(
                '/recall\\s+checkpoint/i',
                $text,
                "{$slug} must not pad with Recall checkpoint lines",
            );
            $this->assertDoesNotMatchRegularExpression(
                '/\\bthe prompt\\b/i',
                $text,
                "{$slug} must not use interview jargon 'the prompt'",
            );
            foreach ([
                'Walk one concrete path with numbers before generalizing.',
                'Keep the tone focused and honest.',
                'Name the force before the mechanism.',
                'Hold the method and the cost in mind before naming a tool.',
                'Trace the same numbers once more and confirm the cost matches the rule before you leave the example.',
            ] as $filler) {
                $this->assertStringNotContainsString(
                    $filler,
                    $text,
                    "{$slug} contains generated filler: {$filler}",
                );
            }
        }

        $example = (string) $guide['parts']['example'];
        $hasWorkedLead = (bool) preg_match('/worked\\s+example\\./i', $body);
        $hasDigit = (bool) preg_match('/\\d/', $example);
        $this->assertTrue(
            $hasWorkedLead || $hasDigit,
            "{$slug} needs a concrete worked example (Worked example. lead or digit in parts.example)",
        );
        $this->assertTrue(
            $hasDigit,
            "{$slug} parts.example must contain at least one digit (concrete values)",
        );

        $this->assertNoRepeatedPaddingParagraphs($slug, $body);
        $this->assertNoRepeatedProseSentences($slug, $body);

        $tokens = $this->tokenize($body);
        for ($i = 0; $i <= count($tokens) - 12; $i++) {
            $span = implode(' ', array_slice($tokens, $i, 12));
            $this->assertArrayNotHasKey(
                $span,
                $curriculaNgrams,
                "{$slug} guide body pastes a 12-word span from context/curriculums/: {$span}",
            );
        }
    }

    private function assertNoRepeatedPaddingParagraphs(string $slug, string $body): void
    {
        $paras = preg_split('/\\n\\s*\\n/', trim($body)) ?: [];
        $counts = [];
        foreach ($paras as $para) {
            $norm = trim(preg_replace('/\\s+/', ' ', $para) ?? '');
            if ($norm === '') {
                continue;
            }
            $counts[$norm] = ($counts[$norm] ?? 0) + 1;
        }
        foreach ($counts as $para => $n) {
            $this->assertLessThan(
                3,
                $n,
                "{$slug} has identical paragraph {$n} times (padding): " . substr($para, 0, 80),
            );
        }
    }

    private function assertNoRepeatedProseSentences(string $slug, string $body): void
    {
        preg_match_all('/[^.!?]+[.!?]+/u', $body, $matches);
        $counts = [];
        foreach ($matches[0] ?? [] as $sentence) {
            $normalized = trim(preg_replace('/\\s+/', ' ', $sentence) ?? '');
            if (mb_strlen($normalized) < 25) {
                continue;
            }
            $key = mb_strtolower($normalized);
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }
        foreach ($counts as $sentence => $count) {
            $this->assertSame(
                1,
                $count,
                "{$slug} repeats a prose sentence {$count} times: " . substr($sentence, 0, 100),
            );
        }
    }

    /** @return array<string, true> */
    private function twelveWordNgramsFromCurricula(): array
    {
        $dir = dirname(__DIR__) . '/context/curriculums';
        $out = [];
        foreach (glob($dir . '/*.md') ?: [] as $file) {
            $raw = file_get_contents($file);
            if ($raw === false) {
                continue;
            }
            $tokens = $this->tokenize($raw);
            for ($i = 0; $i <= count($tokens) - 12; $i++) {
                $out[implode(' ', array_slice($tokens, $i, 12))] = true;
            }
        }

        return $out;
    }

    /** @return list<string> */
    private function tokenize(string $text): array
    {
        preg_match_all("/[a-z0-9']+/", strtolower($text), $m);

        return $m[0];
    }
}
