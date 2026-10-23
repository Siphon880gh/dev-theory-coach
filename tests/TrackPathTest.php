<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\Catalog;
use TheoryCore\Path;
use TheoryCore\ProgressSchema;

final class TrackPathTest extends TestCase
{
    protected function setUp(): void
    {
        Catalog::reset();
    }

    public function testTrackPathsCoverCatalogAndAfterEdges(): void
    {
        $all = [];
        foreach (['structures', 'algorithms', 'systems', 'patterns'] as $id) {
            $path = Path::load($id);
            foreach ($path['milestones'] as $m) {
                $all[] = $m['slug'];
                $family = Catalog::bySlug($m['slug']);
                $this->assertNotNull($family);
                $this->assertSame($family['after'], $m['after']);
            }
        }
        sort($all);
        $catalog = Catalog::slugs();
        $sorted = $catalog;
        sort($sorted);
        $this->assertSame($sorted, $all);
        $this->assertCount(53, $all);
    }

    public function testPrerequisiteUnlocksAndCaseGateSeparate(): void
    {
        $state = ProgressSchema::fresh();
        $this->assertFalse(ProgressSchema::isFamilyUnlockedByPrereqs($state, ['arrays']));
        $this->assertTrue(ProgressSchema::isFamilyUnlockedByPrereqs($state, [])); // when-to-name-a-pattern

        $state = ProgressSchema::recordGuideRead($state, 'arrays');
        // arrays quiz exists → still locked until quiz submitted
        $this->assertFalse(ProgressSchema::isFamilyUnlockedByPrereqs($state, ['arrays']));
        $state = ProgressSchema::recordQuizResult($state, 'arrays', 6, 6);
        $this->assertTrue(ProgressSchema::isFamilyUnlockedByPrereqs($state, ['arrays']));

        // caching needs three prereqs
        $this->assertFalse(ProgressSchema::isFamilyUnlockedByPrereqs(
            $state,
            ['read-and-write-paths', 'hash-maps', 'linked-lists'],
        ));
        foreach (['read-and-write-paths', 'hash-maps', 'linked-lists'] as $slug) {
            // these may not be beginner milestones — set under beginner: keys via normalize miss
            $id = 'beginner:' . $slug;
            if (!isset($state['milestones'][$id])) {
                $state['milestones'][$id] = [
                    'status' => 'unlocked',
                    'guideRead' => true,
                    'coachComplete' => false,
                    'quizSubmitted' => true,
                    'quizScore' => null,
                    'quizAccuracy' => null,
                    'drillAccuracy' => null,
                    'drillMedianLatencyMs' => null,
                    'caseChoiceId' => null,
                    'caseCorrect' => null,
                ];
            } else {
                $state['milestones'][$id]['guideRead'] = true;
                $state['milestones'][$id]['quizSubmitted'] = true;
            }
        }
        $this->assertTrue(ProgressSchema::isFamilyUnlockedByPrereqs(
            $state,
            ['read-and-write-paths', 'hash-maps', 'linked-lists'],
        ));

        // requirements guide readable idea vs case gated
        $state['milestones']['beginner:arrays']['quizAccuracy'] = 0.5;
        $state['milestones']['beginner:hash-maps']['quizAccuracy'] = 0.5;
        $state['milestones']['beginner:queues']['quizAccuracy'] = 0.5;
        $this->assertFalse(ProgressSchema::caseGateOpenFor($state));
        $req = \TheoryCore\CaseStudy::pageResponse('requirements', $state);
        $this->assertStringContainsString('Case gate locked', $req['body'] ?? '');
    }
}
