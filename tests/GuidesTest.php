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
        $this->assertStringContainsString(substr($guide['body'], 0, 40), $body);
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
