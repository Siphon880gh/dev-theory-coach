<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\ProgressSchema;

final class UnlockTest extends TestCase
{
    public function testBeginnerLinearLocksAndCaseGateSides(): void
    {
        $state = ProgressSchema::fresh();
        $this->assertTrue(ProgressSchema::isBeginnerMilestoneUnlocked($state, 'beginner:arrays'));
        $this->assertFalse(ProgressSchema::isBeginnerMilestoneUnlocked($state, 'beginner:strings'));

        $state = ProgressSchema::markMilestoneComplete($state, 'beginner:arrays');
        $this->assertTrue(ProgressSchema::isBeginnerMilestoneUnlocked($state, 'beginner:strings'));
        $this->assertFalse(ProgressSchema::isBeginnerMilestoneUnlocked($state, 'beginner:hash-maps'));

        // mid-path: complete through queues (index 5), next complexity unlocked, recursion locked
        foreach (['arrays', 'strings', 'hash-maps', 'linked-lists', 'stacks', 'queues'] as $slug) {
            $state = ProgressSchema::markMilestoneComplete($state, 'beginner:' . $slug);
        }
        $this->assertTrue(ProgressSchema::isBeginnerMilestoneUnlocked($state, 'beginner:complexity'));
        $this->assertFalse(ProgressSchema::isBeginnerMilestoneUnlocked($state, 'beginner:recursion'));

        $this->assertFalse(ProgressSchema::caseGateOpenFor($state));
        foreach (['arrays', 'hash-maps', 'queues'] as $slug) {
            $state['milestones']['beginner:' . $slug]['quizAccuracy'] = 0.79;
        }
        $this->assertFalse(ProgressSchema::caseGateOpenFor($state));
        foreach (['arrays', 'hash-maps', 'queues'] as $slug) {
            $state['milestones']['beginner:' . $slug]['quizAccuracy'] = 0.8;
        }
        $this->assertTrue(ProgressSchema::caseGateOpenFor($state));
        $state = ProgressSchema::refreshBeginnerLocks($state);
        $this->assertTrue($state['caseGateOpen']);
    }

    public function testQuizAndCoachWriteback(): void
    {
        $state = ProgressSchema::fresh();
        $state = ProgressSchema::recordQuizResult($state, 'arrays', 5, 6);
        $this->assertTrue($state['milestones']['beginner:arrays']['quizSubmitted']);
        $this->assertEqualsWithDelta(5 / 6, $state['milestones']['beginner:arrays']['quizAccuracy'], 1e-9);

        $state = ProgressSchema::recordCoachComplete($state, 'arrays');
        $this->assertTrue($state['milestones']['beginner:arrays']['coachComplete']);
    }

    public function testResetRequiresConfirm(): void
    {
        $state = ProgressSchema::fresh();
        $state = ProgressSchema::markMilestoneComplete($state, 'beginner:arrays');
        $state = ProgressSchema::recordQuizResult($state, 'arrays', 6, 6);
        $noop = ProgressSchema::reset($state, false);
        $this->assertSame('complete', $noop['milestones']['beginner:arrays']['status']);

        $cleared = ProgressSchema::reset($state, true);
        $this->assertSame('unlocked', $cleared['milestones']['beginner:arrays']['status']);
        $this->assertFalse($cleared['milestones']['beginner:arrays']['quizSubmitted']);
        $this->assertFalse($cleared['recovery']);
        $this->assertSame([], $cleared['scores']);
    }
}
