<?php

declare(strict_types=1);

namespace TheoryCore;

/**
 * Home dashboard: next beginner CTA and path-complete / case-gate chips.
 */
final class Dashboard
{
    /**
     * @param array<string, mixed>|null $state
     */
    public static function nextBeginnerSlug(?array $state = null): string
    {
        $state = ProgressSchema::normalize($state ?? ProgressSchema::fresh());
        foreach (ProgressSchema::BEGINNER_LEARNING_ORDER as $slug) {
            $id = 'beginner:' . $slug;
            $row = $state['milestones'][$id] ?? [];
            if (!($row['guideRead'] ?? false)) {
                return $slug;
            }
        }

        return ProgressSchema::BEGINNER_LEARNING_ORDER[count(ProgressSchema::BEGINNER_LEARNING_ORDER) - 1];
    }

    /**
     * @param array<string, mixed> $state
     */
    public static function isPathComplete(array $state): bool
    {
        $state = ProgressSchema::normalize($state);
        foreach (ProgressSchema::BEGINNER_LEARNING_ORDER as $slug) {
            $row = $state['milestones']['beginner:' . $slug] ?? [];
            if (!($row['guideRead'] ?? false)) {
                return false;
            }
        }
        $arrays = $state['milestones']['beginner:arrays'] ?? [];
        $acc = $arrays['quizAccuracy'] ?? null;
        if (!is_numeric($acc) || (float) $acc < 0.8) {
            return false;
        }
        if (!($arrays['coachComplete'] ?? false)) {
            return false;
        }

        return true;
    }

    /**
     * @param array<string, mixed>|null $state
     */
    public static function homeHtml(?array $state = null): string
    {
        $state = ProgressSchema::refreshBeginnerLocks(ProgressSchema::normalize($state ?? ProgressSchema::fresh()));
        $next = self::nextBeginnerSlug($state);
        $complete = self::isPathComplete($state);
        $gateOpen = ProgressSchema::caseGateOpenFor($state);

        $parts = [];
        $parts[] = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">';
        $parts[] = '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $parts[] = '<title>Dev Theory Coach (THEORY_CORE)</title></head><body>';
        $parts[] = '<main class="dashboard">';
        $parts[] = '<p><strong>SAMPLE</strong> data only.</p>';
        $parts[] = '<h1>Dev Theory Coach (THEORY_CORE)</h1>';
        if ($complete) {
            $parts[] = '<p class="path-complete" data-credential="beginner-path">Beginner path complete. Credential: beginner path finished.</p>';
        } else {
            $parts[] = '<p class="next-action"><a class="next-cta" href="/guides/' . rawurlencode($next) . '">'
                . 'Next: study ' . self::h($next) . '</a></p>';
        }
        $parts[] = '<p class="case-gate-chip" data-open="' . ($gateOpen ? '1' : '0') . '">'
            . ($gateOpen ? 'Case gate open' : 'Case gate locked') . '</p>';
        $parts[] = '<p><a href="/guides">Guides hub</a></p>';
        $parts[] = '<script src="/assets/js/progress.js"></script>';
        $parts[] = '</main></body></html>';

        return implode("\n", $parts);
    }

    private static function h(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
