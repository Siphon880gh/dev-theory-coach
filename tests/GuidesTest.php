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
        $this->assertStringContainsString('SAMPLE', $html);

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
        $this->assertStringContainsString('SAMPLE', $body);
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
        $this->assertStringContainsString('SAMPLE', $response['body']);
        $this->assertStringContainsString('not-a-slug', $response['body']);
        $this->assertStringContainsString('href="/"', $response['body']);
    }

    public function testBeginnerGuidesMeetWordCountPasteAndCoachSlugRules(): void
    {
        $curriculaNgrams = $this->twelveWordNgramsFromCurricula();

        foreach (ProgressSchema::BEGINNER_LEARNING_ORDER as $slug) {
            $guide = Guides::load($slug);
            $this->assertNotNull($guide, "missing guide for {$slug}");
            $this->assertSame($slug, $guide['relatedCoachSlug']);
            $body = (string) $guide['body'];
            $words = preg_match_all("/\\b[\\w']+\\b/u", $body);
            $this->assertGreaterThanOrEqual(300, $words, "{$slug} body too short");
            $this->assertLessThanOrEqual(800, $words, "{$slug} body too long");

            foreach (['rule', 'why', 'SAMPLE', 'use', 'refuse'] as $needle) {
                $this->assertTrue(
                    stripos($body, $needle) !== false
                    || (isset($guide['parts']) && $guide['parts'] !== []),
                    "{$slug} should cover {$needle}",
                );
            }
            // Stronger: parts object present with five keys
            $this->assertArrayHasKey('parts', $guide);
            foreach (['rule', 'why', 'example', 'use', 'refuse'] as $part) {
                $this->assertArrayHasKey($part, $guide['parts'], "{$slug} missing part {$part}");
                $this->assertNotSame('', trim((string) $guide['parts'][$part]));
            }
            $this->assertStringContainsString('SAMPLE', $guide['parts']['example']);

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
    }


    public function testAllFamilyGuidesMeetFloor(): void
    {
        $ngrams = $this->twelveWordNgramsFromCurricula();
        foreach (Catalog::all() as $family) {
            $slug = $family['slug'];
            $guide = Guides::load($slug);
            $this->assertNotNull($guide, "missing guide {$slug}");
            $this->assertSame($slug, $guide['relatedCoachSlug']);
            $body = (string) $guide['body'];
            $words = preg_match_all("/\\b[\\w']+\\b/u", $body);
            $this->assertGreaterThanOrEqual(300, $words, "{$slug} short");
            $this->assertLessThanOrEqual(800, $words, "{$slug} long");
            foreach (['rule', 'why', 'example', 'use', 'refuse'] as $part) {
                $this->assertArrayHasKey($part, $guide['parts'] ?? [], "{$slug} {$part}");
            }
            $tokens = $this->tokenize($body);
            for ($i = 0; $i <= count($tokens) - 12; $i++) {
                $span = implode(' ', array_slice($tokens, $i, 12));
                $this->assertArrayNotHasKey($span, $ngrams, "{$slug} paste: {$span}");
            }
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
