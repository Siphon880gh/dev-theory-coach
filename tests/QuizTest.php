<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\Catalog;
use TheoryCore\ProgressSchema;
use TheoryCore\Quiz;

final class QuizTest extends TestCase
{
    protected function setUp(): void
    {
        Catalog::reset();
    }

    public function testRendererRequiresWhyWrongAndIncludesSample(): void
    {
        $quiz = Quiz::load('arrays');
        $this->assertNotNull($quiz);
        $html = Quiz::renderHtml($quiz);
        $this->assertStringContainsString('Untimed', $html);
        $this->assertStringContainsString('No code runner', $html);

        $bad = $quiz;
        $bad['items'][0]['whyWrong'][1] = '';
        $this->expectException(\InvalidArgumentException::class);
        Quiz::renderHtml($bad);
    }

    public function testArraysQuizCountTopicsAndPaste(): void
    {
        $quiz = Quiz::load('arrays');
        $this->assertNotNull($quiz);
        $this->assertGreaterThanOrEqual(6, count($quiz['items']));

        $blob = strtolower(json_encode($quiz) ?: '');
        foreach (['index', 'insert', 'append', 'list'] as $topic) {
            $this->assertStringContainsString($topic, $blob);
        }

        $ngrams = $this->curriculaNgrams();
        $tokens = [];
        preg_match_all("/[a-z0-9']+/", $blob, $m);
        $tokens = $m[0];
        for ($i = 0; $i <= count($tokens) - 12; $i++) {
            $span = implode(' ', array_slice($tokens, $i, 12));
            $this->assertArrayNotHasKey($span, $ngrams, "paste: {$span}");
        }
    }

    public function testFinishingQuizWritesScoreAndAccuracy(): void
    {
        $quiz = Quiz::load('arrays');
        $this->assertNotNull($quiz);
        $answers = [];
        foreach ($quiz['items'] as $i => $item) {
            // 5/6 correct: miss the last item
            $answers[] = $i === 5 ? ($item['answerIndex'] + 1) % 4 : $item['answerIndex'];
        }
        $score = Quiz::score($quiz, $answers);
        $this->assertSame(5, $score['correct']);
        $this->assertSame(6, $score['total']);
        $this->assertEqualsWithDelta(5 / 6, $score['accuracy'], 1e-9);

        $state = ProgressSchema::recordQuizResult(ProgressSchema::fresh(), 'arrays', 5, 6);
        $row = $state['milestones']['beginner:arrays'];
        $this->assertTrue($row['quizSubmitted']);
        $this->assertSame(5, $row['quizScore']);
        $this->assertEqualsWithDelta(5 / 6, $row['quizAccuracy'], 1e-9);
        $this->assertSame(5, $state['scores']['beginner:arrays']['correct']);

        $js = file_get_contents(dirname(__DIR__) . '/assets/js/progress.js');
        $this->assertNotFalse($js);
        $this->assertStringContainsString('recordQuizResult', $js);
        $this->assertStringContainsString('theory_core_progress_v1', $js);
    }

    /** @return array<string, true> */
    private function curriculaNgrams(): array
    {
        $out = [];
        foreach (glob(dirname(__DIR__) . '/context/curriculums/*.md') ?: [] as $file) {
            $raw = file_get_contents($file);
            if ($raw === false) {
                continue;
            }
            preg_match_all("/[a-z0-9']+/", strtolower($raw), $m);
            $tokens = $m[0];
            for ($i = 0; $i <= count($tokens) - 12; $i++) {
                $out[implode(' ', array_slice($tokens, $i, 12))] = true;
            }
        }

        return $out;
    }

    public function testQuizPageEmbedsDataAndScripts(): void
    {
        $quiz = Quiz::load('arrays');
        $this->assertNotNull($quiz);
        $html = Quiz::renderHtml($quiz);
        $this->assertStringContainsString('id="quiz-data"', $html);
        $this->assertStringContainsString('js/quiz.js', $html);
        $this->assertStringContainsString('js/progress.js', $html);
        $this->assertStringContainsString('data-choice=', $html);
        $this->assertStringContainsString('quiz-live', $html);
        $this->assertStringContainsString('quiz-feedback', $html);

        $js = file_get_contents(dirname(__DIR__) . '/assets/js/progress.js');
        $this->assertNotFalse($js);
        $this->assertStringContainsString('recordCoachComplete', $js);
    }
}
