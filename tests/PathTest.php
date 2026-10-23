<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\Path;
use TheoryCore\ProgressSchema;

final class PathTest extends TestCase
{
    public function testBeginnerPathOrderAndRefs(): void
    {
        $path = Path::load('beginner');
        $this->assertSame('beginner', $path['id']);
        $slugs = [];
        foreach ($path['milestones'] as $m) {
            $slugs[] = $m['slug'];
            $this->assertSame('beginner:' . $m['slug'], $m['id']);
            $this->assertContains('guide:' . $m['slug'], $m['contentRefs']);
            foreach ($m['contentRefs'] as $ref) {
                $resolved = Path::resolveRef($ref);
                $this->assertNotSame('missing', $resolved['kind'], $ref);
                if (str_starts_with($ref, 'coach:') && $m['slug'] !== 'arrays') {
                    $this->assertSame('slot', $resolved['kind']);
                }
            }
            if ($m['slug'] === 'arrays') {
                $this->assertContains('coach:arrays', $m['contentRefs']);
                $this->assertContains('quiz:arrays', $m['contentRefs']);
            } else {
                $this->assertNotContains('quiz:' . $m['slug'], $m['contentRefs']);
                $this->assertNotContains('coach:' . $m['slug'], $m['contentRefs']);
            }
        }
        $this->assertSame(ProgressSchema::BEGINNER_LEARNING_ORDER, $slugs);
    }

    public function testCaseGateThreshold079And080(): void
    {
        $path = Path::load('beginner');
        $this->assertSame(['arrays', 'hash-maps', 'queues'], $path['caseGate']['quizzes']);
        $this->assertSame(0.8, $path['caseGate']['threshold']);

        $state = ProgressSchema::fresh();
        foreach (['arrays', 'hash-maps', 'queues'] as $slug) {
            $state = ProgressSchema::recordQuizResult($state, $slug, 79, 100);
            $state['milestones']['beginner:' . $slug]['quizAccuracy'] = 0.79;
        }
        $this->assertFalse(ProgressSchema::caseGateOpenFor($state));

        foreach (['arrays', 'hash-maps', 'queues'] as $slug) {
            $state['milestones']['beginner:' . $slug]['quizAccuracy'] = 0.80;
            $state['milestones']['beginner:' . $slug]['quizSubmitted'] = true;
        }
        $this->assertTrue(ProgressSchema::caseGateOpenFor($state));
    }
}
