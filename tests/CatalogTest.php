<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\Catalog;

final class CatalogTest extends TestCase
{
    protected function setUp(): void
    {
        Catalog::reset();
    }

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

    public function testEveryFamilyStoresTrackLabelTitleSummaryAfterAndRungs(): void
    {
        $requiredLabels = [
            'Data structures',
            'Algorithms',
            'System design',
            'Design patterns',
        ];

        $this->assertSame(
            $requiredLabels,
            array_values(Catalog::trackLabels()),
            'Catalog must expose the four track labels verbatim',
        );

        $slugs = Catalog::slugs();
        $slugSet = array_fill_keys($slugs, true);
        $seenLabels = [];

        foreach (Catalog::all() as $family) {
            $this->assertIsString($family['slug'] ?? null);
            $this->assertContains($family['track'] ?? null, ['A', 'B', 'C', 'D']);
            $this->assertIsString($family['track_id'] ?? null);
            $this->assertIsString($family['track_label'] ?? null);
            $this->assertContains($family['track_label'], $requiredLabels);
            $seenLabels[$family['track_label']] = true;

            $this->assertIsString($family['title'] ?? null);
            $this->assertNotSame('', $family['title']);
            $this->assertIsString($family['summary'] ?? null);
            $this->assertNotSame('', $family['summary']);

            $this->assertIsArray($family['after'] ?? null);
            foreach ($family['after'] as $prereq) {
                $this->assertIsString($prereq);
                $this->assertArrayHasKey(
                    $prereq,
                    $slugSet,
                    "after slug '{$prereq}' on family {$family['slug']} must exist in the catalog",
                );
            }

            $this->assertIsArray($family['rungs'] ?? null);
            $this->assertNotEmpty($family['rungs']);
            foreach ($family['rungs'] as $rung) {
                $this->assertIsString($rung);
            }
        }

        foreach ($requiredLabels as $label) {
            $this->assertArrayHasKey($label, $seenLabels, "At least one family must use label {$label}");
        }
    }

    public function testHubGroupingFixtureUsesFullTrackLabelsAndPlacesArrays(): void
    {
        $html = Catalog::hubGroupingHtml();

        foreach (['Data structures', 'Algorithms', 'System design', 'Design patterns'] as $label) {
            $this->assertStringContainsString($label, $html);
        }

        $this->assertStringContainsString('SAMPLE', $html);

        // arrays must appear under the Data structures section (before Algorithms heading).
        $dsPos = strpos($html, 'Data structures');
        $algPos = strpos($html, 'Algorithms');
        $arraysPos = strpos($html, 'data-slug="arrays"');
        $this->assertNotFalse($dsPos);
        $this->assertNotFalse($algPos);
        $this->assertNotFalse($arraysPos);
        $this->assertGreaterThan($dsPos, $arraysPos, 'arrays should appear after Data structures heading');
        $this->assertLessThan($algPos, $arraysPos, 'arrays should appear before Algorithms heading');
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
