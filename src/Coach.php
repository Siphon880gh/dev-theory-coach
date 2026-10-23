<?php

declare(strict_types=1);

namespace TheoryCore;

use InvalidArgumentException;
use RuntimeException;

/**
 * Coaching session loader, nav engine, and HTML renderer.
 */
final class Coach
{
    /**
     * @return array<string, mixed>|null
     */
    public static function load(string $slug): ?array
    {
        $path = dirname(__DIR__) . '/content/coaching/' . $slug . '.json';
        if (!is_file($path)) {
            return null;
        }
        $session = CoachValidator::loadFile($path);
        if (($session['meta']['slug'] ?? null) !== $slug) {
            throw new RuntimeException('Coach meta.slug mismatch for ' . $slug);
        }

        return $session;
    }

    /**
     * Nav state: history of node ids visited, current id, explanations from wrong nodes.
     *
     * @param array<string, mixed> $session
     * @return array{current: string, history: list<string>, explanations: list<string>, done: bool}
     */
    public static function start(array $session): array
    {
        CoachValidator::assertValid($session);
        $start = (string) $session['start'];

        return [
            'current' => $start,
            'history' => [$start],
            'explanations' => [],
            'done' => (($session['nodes'][$start]['type'] ?? null) === 'success'),
        ];
    }

    /**
     * @param array<string, mixed> $session
     * @param array{current: string, history: list<string>, explanations: list<string>, done: bool} $state
     * @return array{current: string, history: list<string>, explanations: list<string>, done: bool}
     */
    public static function choose(array $session, array $state, int $choiceIndex): array
    {
        if ($state['done']) {
            return $state;
        }
        $node = $session['nodes'][$state['current']] ?? null;
        if (!is_array($node)) {
            throw new InvalidArgumentException('Unknown current node');
        }
        $choices = $node['choices'] ?? [];
        if (!isset($choices[$choiceIndex]) || !is_array($choices[$choiceIndex])) {
            throw new InvalidArgumentException('Bad choice index');
        }
        $to = (string) $choices[$choiceIndex]['to'];
        $next = $session['nodes'][$to] ?? null;
        if (!is_array($next)) {
            throw new InvalidArgumentException('Choice targets missing node');
        }

        $history = $state['history'];
        $history[] = $to;
        $explanations = $state['explanations'];
        $current = $to;

        if (($next['type'] ?? null) === 'wrong') {
            $explain = (string) ($next['explanation'] ?? 'That choice is not the best fit.');
            $explanations[] = $explain;
            $rewind = (string) $next['rewind_to'];
            $history[] = $rewind;
            $current = $rewind;
        }

        $done = (($session['nodes'][$current]['type'] ?? null) === 'success');

        return [
            'current' => $current,
            'history' => $history,
            'explanations' => $explanations,
            'done' => $done,
        ];
    }

    /**
     * @return array{status: int, location?: string, body?: string}
     */
    public static function pageResponse(string $slug): array
    {
        if (Catalog::bySlug($slug) === null) {
            return UnknownPage::slugResponse('coach', $slug);
        }

        $session = self::load($slug);
        if ($session === null) {
            $html = self::slotHtml($slug);

            return ['status' => 200, 'body' => $html];
        }

        return ['status' => 200, 'body' => self::sessionHtml($session)];
    }

    /** @param array<string, mixed> $session */
    public static function sessionHtml(array $session): string
    {
        $slug = (string) $session['meta']['slug'];
        $title = (string) ($session['meta']['title'] ?? $slug);
        $state = self::start($session);
        $node = $session['nodes'][$state['current']];
        $parts = [];
        $parts[] = '<p><a href="' . e(url('/guides/' . rawurlencode($slug))) . '">Back to guide</a></p>';
        $parts[] = '<h1>Coach: ' . self::h($title) . '</h1>';
        $parts[] = '<div class="node" data-node-id="' . self::h($state['current']) . '" data-type="'
            . self::h((string) $node['type']) . '">';
        $parts[] = '<p class="prompt">' . self::h((string) ($node['prompt'] ?? '')) . '</p>';
        if (($node['type'] ?? '') === 'success') {
            $parts[] = '<p class="success">Success. You named the right idea.</p>';
        } else {
            $parts[] = '<ol class="choices">';
            foreach ($node['choices'] as $i => $choice) {
                $parts[] = '<li><button type="button" class="choice" data-choice-index="' . (int) $i . '">'
                    . self::h((string) ($choice['label'] ?? 'Choice ' . $i))
                    . '</button></li>';
            }
            $parts[] = '</ol>';
        }
        $parts[] = '</div>';

        return Layout::document(
            $title . ' — Coach',
            implode("\n", $parts),
            [
                'mainClass' => 'coach-page',
                'mainAttrs' => ['data-slug' => $slug],
                'navActive' => 'guides',
            ]
        );
    }

    public static function slotHtml(string $slug): string
    {
        $inner = '<h1>Coach: ' . self::h($slug) . '</h1>'
            . "\n" . '<p class="slot">not authored yet</p>'
            . "\n" . '<p><a href="' . e(url('/guides/' . rawurlencode($slug))) . '">Back to guide</a></p>';

        return Layout::document(
            'Coach slot — ' . $slug,
            $inner,
            [
                'mainClass' => 'coach-slot',
                'mainAttrs' => ['data-slug' => $slug],
                'navActive' => 'guides',
            ]
        );
    }

    private static function h(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
