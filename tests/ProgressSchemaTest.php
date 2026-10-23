<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\ProgressSchema;

final class ProgressSchemaTest extends TestCase
{
    public function testRoundTripFixturePreservesPathMilestonesScoresLatencies(): void
    {
        $fixture = ProgressSchema::fresh();
        $fixture['pathId'] = 'beginner';
        $fixture['milestones']['beginner:arrays']['status'] = 'complete';
        $fixture['milestones']['beginner:arrays']['guideRead'] = true;
        $fixture['scores']['beginner:arrays'] = ['correct' => 5, 'total' => 6];
        $fixture['latencies']['drill:complexity'] = [1200, 900, 1100];
        $fixture['caseGateOpen'] = false;

        $back = ProgressSchema::roundTrip($fixture);

        $this->assertSame('beginner', $back['pathId']);
        $this->assertSame(ProgressSchema::VERSION, $back['version']);
        $this->assertSame('complete', $back['milestones']['beginner:arrays']['status']);
        $this->assertTrue($back['milestones']['beginner:arrays']['guideRead']);
        $this->assertSame(['correct' => 5, 'total' => 6], $back['scores']['beginner:arrays']);
        $this->assertSame([1200, 900, 1100], $back['latencies']['drill:complexity']);
        $this->assertFalse($back['recovery']);
    }

    public function testProgressJsSourceContainsStorageKey(): void
    {
        $path = dirname(__DIR__) . '/assets/js/progress.js';
        $this->assertFileExists($path);
        $src = file_get_contents($path);
        $this->assertNotFalse($src);
        $this->assertStringContainsString('theory_core_progress_v1', $src);
        $this->assertStringContainsString('ProgressSchema.php', $src);
    }

    public function testTruncatedJsonDecodesToResetWithRecovery(): void
    {
        $truncated = '{"version":1,"pathId":"beginner","milestones":';
        $state = ProgressSchema::decode($truncated);

        $this->assertTrue($state['recovery']);
        $this->assertSame(ProgressSchema::VERSION, $state['version']);
        $this->assertSame(ProgressSchema::PATH_BEGINNER, $state['pathId']);
        $this->assertSame(ProgressSchema::beginnerMilestoneIds(), array_keys($state['milestones']));
        $this->assertSame('unlocked', $state['milestones']['beginner:arrays']['status']);
        $this->assertSame('locked', $state['milestones']['beginner:strings']['status']);
        $this->assertFalse($state['caseGateOpen']);
    }

    public function testBeginnerMilestoneIdsMatchLearningOrder(): void
    {
        $expected = [];
        foreach (ProgressSchema::BEGINNER_LEARNING_ORDER as $slug) {
            $expected[] = 'beginner:' . $slug;
        }

        $this->assertSame($expected, ProgressSchema::beginnerMilestoneIds());
        $this->assertCount(12, $expected);

        $fresh = ProgressSchema::fresh();
        $this->assertSame('beginner', $fresh['pathId']);
        $this->assertSame($expected, array_keys($fresh['milestones']));
    }
}
