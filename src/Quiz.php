<?php

declare(strict_types=1);

namespace TheoryCore;

use InvalidArgumentException;
use RuntimeException;

/**
 * Untimed MCQ quiz loader and SAMPLE HTML renderer. No code runner, no timer.
 */
final class Quiz
{
    /**
     * @return array{slug: string, title?: string, items: list<array<string, mixed>>}|null
     */
    public static function load(string $slug): ?array
    {
        $path = dirname(__DIR__) . '/content/quizzes/' . $slug . '.json';
        if (!is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new RuntimeException('Unreadable quiz ' . $slug);
        }
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        self::assertValid($decoded);
        if (($decoded['slug'] ?? null) !== $slug) {
            throw new RuntimeException('Quiz slug mismatch');
        }

        return $decoded;
    }

    /** @param array<string, mixed> $quiz */
    public static function assertValid(array $quiz): void
    {
        $items = $quiz['items'] ?? null;
        if (!is_array($items) || $items === []) {
            throw new InvalidArgumentException('quiz needs items');
        }
        foreach ($items as $i => $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException("item {$i} invalid");
            }
            $choices = $item['choices'] ?? null;
            if (!is_array($choices) || count($choices) !== 4) {
                throw new InvalidArgumentException("item {$i} needs exactly 4 choices");
            }
            $answer = $item['answerIndex'] ?? null;
            if (!is_int($answer) || $answer < 0 || $answer > 3) {
                throw new InvalidArgumentException("item {$i} bad answerIndex");
            }
            if (!isset($item['whyRight']) || !is_string($item['whyRight']) || $item['whyRight'] === '') {
                throw new InvalidArgumentException("item {$i} needs whyRight");
            }
            $whyWrong = $item['whyWrong'] ?? null;
            if (!is_array($whyWrong) || count($whyWrong) !== 4) {
                throw new InvalidArgumentException("item {$i} needs whyWrong for every choice index");
            }
            for ($c = 0; $c < 4; $c++) {
                if ($c === $answer) {
                    continue;
                }
                if (!isset($whyWrong[$c]) || !is_string($whyWrong[$c]) || $whyWrong[$c] === '') {
                    throw new InvalidArgumentException("item {$i} missing whyWrong[{$c}]");
                }
            }
        }
    }

    /**
     * @return array{status: int, location?: string, body?: string}
     */
    public static function pageResponse(string $slug): array
    {
        if (Catalog::bySlug($slug) === null) {
            return UnknownPage::slugResponse('quiz', $slug);
        }
        $quiz = self::load($slug);
        if ($quiz === null) {
            return [
                'status' => 200,
                'body' => Layout::document(
                    'Quiz slot',
                    '<p class="slot">not authored yet</p>',
                    ['mainClass' => 'quiz-slot']
                ),
            ];
        }

        return ['status' => 200, 'body' => self::renderHtml($quiz)];
    }

    /** @param array<string, mixed> $quiz */
    public static function renderHtml(array $quiz): string
    {
        self::assertValid($quiz);
        $slug = (string) $quiz['slug'];
        $title = (string) ($quiz['title'] ?? $slug);
        $parts = [];
        $parts[] = '<h1>Quiz: ' . self::h($title) . '</h1>';
        foreach ($quiz['items'] as $i => $item) {
            $parts[] = '<section class="quiz-item" data-index="' . (int) $i . '">';
            $parts[] = '<p class="prompt">' . self::h((string) $item['prompt']) . '</p>';
            $parts[] = '<ol class="choices">';
            foreach ($item['choices'] as $ci => $choice) {
                $parts[] = '<li data-choice="' . (int) $ci . '">' . self::h((string) $choice) . '</li>';
            }
            $parts[] = '</ol></section>';
        }
        $parts[] = '<p class="quiz-meta">Items: ' . count($quiz['items']) . '</p>';

        return Layout::document(
            'Quiz — ' . $title,
            implode("\n", $parts),
            [
                'mainClass' => 'quiz-page',
                'mainAttrs' => ['data-slug' => $slug, 'data-untimed' => '1'],
                'sampleNote' => 'Untimed. No code runner.',
            ]
        );
    }

    /**
     * Score a list of selected choice indices.
     *
     * @param array<string, mixed> $quiz
     * @param list<int> $answers
     * @return array{correct: int, total: int, accuracy: float}
     */
    public static function score(array $quiz, array $answers): array
    {
        self::assertValid($quiz);
        $items = $quiz['items'];
        $total = count($items);
        $correct = 0;
        foreach ($items as $i => $item) {
            if (($answers[$i] ?? -1) === $item['answerIndex']) {
                $correct++;
            }
        }

        return [
            'correct' => $correct,
            'total' => $total,
            'accuracy' => $total > 0 ? $correct / $total : 0.0,
        ];
    }

    private static function h(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
