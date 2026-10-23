<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\CaseStudy;
use TheoryCore\Catalog;
use TheoryCore\ProgressSchema;

final class CaseTest extends TestCase
{
    protected function setUp(): void
    {
        Catalog::reset();
    }

    public function testGateClosedAndOpen(): void
    {
        $closed = CaseStudy::pageResponse('requirements', ProgressSchema::fresh());
        $this->assertSame(200, $closed['status']);
        $this->assertStringContainsString('Case gate locked', $closed['body'] ?? '');
        $this->assertStringNotContainsString('data-choice-id', $closed['body'] ?? '');

        $state = ProgressSchema::fresh();
        foreach (['arrays', 'hash-maps', 'queues'] as $slug) {
            $state['milestones']['beginner:' . $slug]['quizAccuracy'] = 0.8;
            $state['milestones']['beginner:' . $slug]['quizSubmitted'] = true;
        }
        $open = CaseStudy::pageResponse('requirements', $state);
        $this->assertStringContainsString('SAMPLE', $open['body'] ?? '');
        $this->assertStringContainsString('data-choice-id', $open['body'] ?? '');
        $case = CaseStudy::load('requirements');
        $this->assertNotNull($case);
        foreach ($case['choices'] as $c) {
            $this->assertStringNotContainsString($c['consequence'], $open['body'] ?? '');
        }
    }

    public function testRequirementsShapePasteAndCommit(): void
    {
        $case = CaseStudy::load('requirements');
        $this->assertNotNull($case);
        CaseStudy::assertValid($case);
        $this->assertGreaterThanOrEqual(3, count($case['choices']));
        $this->assertLessThanOrEqual(4, count($case['choices']));

        $state = CaseStudy::commit(ProgressSchema::fresh(), $case, 'reject');
        $this->assertSame('reject', $state['milestones']['beginner:requirements']['caseChoiceId']
            ?? $state['scores']['case:requirements']['choiceId']);
        $this->assertTrue($state['scores']['case:requirements']['correct']);

        $skill = file_get_contents(dirname(__DIR__) . '/.agents/skills/add-case/SKILL.md');
        $this->assertNotFalse($skill);
        $this->assertStringNotContainsString('TODO', $skill);
    }
}
