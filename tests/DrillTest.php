<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\Catalog;
use TheoryCore\Drill;
use TheoryCore\ProgressSchema;

final class DrillTest extends TestCase
{
    protected function setUp(): void
    {
        Catalog::reset();
    }

    public function testComplexityRendersAndArraysIsNotADrill(): void
    {
        $c = Drill::pageResponse('complexity');
        $this->assertSame(200, $c['status']);
        $this->assertStringContainsString('Timed', $c['body'] ?? '');

        $a = Drill::pageResponse('arrays');
        $this->assertSame(200, $a['status']);
        $this->assertStringContainsString('not a drill', strtolower($a['body'] ?? ''));
        $this->assertStringNotContainsString('drill-item', $a['body'] ?? '');
    }

    public function testComplexityPackShapeAndPaste(): void
    {
        $drill = Drill::load('complexity');
        $this->assertNotNull($drill);
        $this->assertGreaterThanOrEqual(8, count($drill['items']));
        foreach ($drill['items'] as $item) {
            $this->assertCount(3, $item['thoughtChain']);
            $this->assertIsInt($item['seconds']);
            $this->assertNotSame('', $item['accept']);
        }
        $skill = file_get_contents(dirname(__DIR__) . '/.agents/skills/add-drill/SKILL.md');
        $this->assertNotFalse($skill);
        $this->assertStringNotContainsString('TODO', $skill);
        $this->assertFileExists(dirname(__DIR__) . '/content/drills/complexity.json');
    }

    public function testFinishedDrillWritesAccuracyAndMedianLatency(): void
    {
        $flags = [true, true, true, true, true, true, false, true];
        $lat = [100, 200, 150, 180, 90, 220, 300, 110];
        $state = Drill::recordFinish(ProgressSchema::fresh(), 'complexity', $flags, $lat);
        // complexity may not be on beginner milestones list as drill fields — still store scores
        $this->assertEqualsWithDelta(7 / 8, $state['scores']['drill:complexity']['accuracy'], 1e-9);
        $this->assertSame($lat, $state['latencies']['drill:complexity']);
        $sorted = $lat;
        sort($sorted);
        $median = $sorted[(int) floor((count($sorted) - 1) / 2)];
        $this->assertSame($median, $state['milestones']['beginner:complexity']['drillMedianLatencyMs']);
        $this->assertEqualsWithDelta(7 / 8, $state['milestones']['beginner:complexity']['drillAccuracy'], 1e-9);
    }
}
