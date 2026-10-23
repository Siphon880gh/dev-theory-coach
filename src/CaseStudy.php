<?php

declare(strict_types=1);

namespace TheoryCore;

use InvalidArgumentException;
use RuntimeException;

/**
 * Decide-and-reveal cases (named CaseStudy to avoid PHP reserved word Case).
 */
final class CaseStudy
{
    /** @return array<string, mixed>|null */
    public static function load(string $slug): ?array
    {
        $path = dirname(__DIR__) . '/content/cases/' . $slug . '.json';
        if (!is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new RuntimeException('Unreadable case');
        }
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        self::assertValid($decoded);

        return $decoded;
    }

    /** @param array<string, mixed> $case */
    public static function assertValid(array $case): void
    {
        $choices = $case['choices'] ?? null;
        if (!is_array($choices) || count($choices) < 3 || count($choices) > 4) {
            throw new InvalidArgumentException('case needs 3 or 4 choices');
        }
        $best = $case['best'] ?? null;
        if (!is_string($best) || $best === '') {
            throw new InvalidArgumentException('case needs best id');
        }
        $ids = [];
        foreach ($choices as $c) {
            if (!is_array($c) || !isset($c['id'], $c['label'], $c['consequence'])) {
                throw new InvalidArgumentException('choice needs id, label, consequence');
            }
            $ids[] = $c['id'];
        }
        if (!in_array($best, $ids, true)) {
            throw new InvalidArgumentException('best must name a choice id');
        }
    }

    /**
     * @param array<string, mixed>|null $progress
     * @return array{status: int, location?: string, body?: string}
     */
    public static function pageResponse(string $slug, ?array $progress = null): array
    {
        if (Catalog::bySlug($slug) === null) {
            return UnknownPage::slugResponse('case', $slug);
        }
        $progress = ProgressSchema::normalize($progress ?? ProgressSchema::fresh());
        $gateOpen = ProgressSchema::caseGateOpenFor($progress);
        $case = self::load($slug);
        if ($case === null) {
            return [
                'status' => 200,
                'body' => Layout::document(
                    'Case slot',
                    '<p class="slot">not authored yet</p>',
                    ['mainClass' => 'case-slot', 'navActive' => 'home']
                ),
            ];
        }
        if (!$gateOpen) {
            return ['status' => 200, 'body' => self::gateClosedHtml($slug)];
        }

        return ['status' => 200, 'body' => self::preCommitHtml($case)];
    }

    public static function gateClosedHtml(string $slug): string
    {
        return Layout::document(
            'Case gated',
            '<p class="gate">Case gate locked. Reach quiz accuracy ≥ 0.8 on arrays, hash-maps, and queues before graded cases reveal the best choice.</p>',
            [
                'mainClass' => 'case-gated',
                'mainAttrs' => ['data-slug' => $slug],
                'navActive' => 'home',
            ]
        );
    }

    /** @param array<string, mixed> $case */
    public static function preCommitHtml(array $case): string
    {
        self::assertValid($case);
        $slug = (string) $case['slug'];
        $parts = [];
        $parts[] = '<h1>Case: ' . self::h((string) ($case['title'] ?? $slug)) . '</h1>';
        $parts[] = '<p class="prompt">' . self::h((string) $case['prompt']) . '</p>';
        $parts[] = '<ul class="choices">';
        foreach ($case['choices'] as $c) {
            $parts[] = '<li><button type="button" class="choice" data-choice-id="'
                . self::h((string) $c['id']) . '">'
                . self::h((string) $c['label']) . '</button></li>';
            // consequences intentionally omitted pre-commit
        }
        $parts[] = '</ul>';
        $html = Layout::document(
            'Case — ' . $slug,
            implode("\n", $parts),
            [
                'mainClass' => 'case-page',
                'mainAttrs' => ['data-slug' => $slug, 'data-committed' => '0'],
                'navActive' => 'home',
            ]
        );
        foreach ($case['choices'] as $c) {
            if (str_contains($html, (string) $c['consequence'])) {
                throw new RuntimeException('consequence leaked into pre-commit HTML');
            }
        }

        return $html;
    }

    /**
     * @param array<string, mixed> $state
     * @param array<string, mixed> $case
     * @return array<string, mixed>
     */
    public static function commit(array $state, array $case, string $choiceId): array
    {
        self::assertValid($case);
        $state = ProgressSchema::normalize($state);
        $slug = (string) $case['slug'];
        $id = $state['pathId'] . ':' . $slug;
        $correct = ($choiceId === $case['best']);
        if (!isset($state['milestones'][$id])) {
            // decision families may sit outside beginner path ids — still record under scores
            $state['milestones'][$id] = [
                'status' => 'unlocked',
                'guideRead' => false,
                'coachComplete' => false,
                'quizSubmitted' => false,
                'quizScore' => null,
                'quizAccuracy' => null,
                'drillAccuracy' => null,
                'drillMedianLatencyMs' => null,
                'caseChoiceId' => null,
                'caseCorrect' => null,
            ];
        }
        $state['milestones'][$id]['caseChoiceId'] = $choiceId;
        $state['milestones'][$id]['caseCorrect'] = $correct;
        $state['scores']['case:' . $slug] = ['choiceId' => $choiceId, 'correct' => $correct];
        $state['recovery'] = false;

        return $state;
    }

    private static function h(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
