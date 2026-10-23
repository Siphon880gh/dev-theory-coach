<?php

declare(strict_types=1);

namespace TheoryCore;

use InvalidArgumentException;
use RuntimeException;

/**
 * Timed drill packs for decisions.drill_families only.
 */
final class Drill
{
    /** @return list<string> */
    public static function drillFamilies(): array
    {
        return [
            'complexity',
            'binary-search',
            'two-pointers',
            'sliding-window',
            'sorting',
            'estimates',
            'rate-limiting',
        ];
    }

    public static function isDrillFamily(string $slug): bool
    {
        return in_array($slug, self::drillFamilies(), true);
    }

    /** @return array<string, mixed>|null */
    public static function load(string $slug): ?array
    {
        $path = dirname(__DIR__) . '/content/drills/' . $slug . '.json';
        if (!is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new RuntimeException('Unreadable drill');
        }
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        self::assertValid($decoded);

        return $decoded;
    }

    /** @param array<string, mixed> $drill */
    public static function assertValid(array $drill): void
    {
        $items = $drill['items'] ?? null;
        if (!is_array($items) || $items === []) {
            throw new InvalidArgumentException('drill needs items');
        }
        foreach ($items as $i => $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException("bad item {$i}");
            }
            if (!isset($item['accept']) || !is_string($item['accept'])) {
                throw new InvalidArgumentException("item {$i} needs accept");
            }
            $chain = $item['thoughtChain'] ?? null;
            if (!is_array($chain) || count($chain) !== 3) {
                throw new InvalidArgumentException("item {$i} thoughtChain must length 3");
            }
            if (!isset($item['seconds']) || !is_int($item['seconds'])) {
                throw new InvalidArgumentException("item {$i} needs seconds");
            }
        }
    }

    /**
     * @return array{status: int, location?: string, body?: string}
     */
    public static function pageResponse(string $slug): array
    {
        if (Catalog::bySlug($slug) === null) {
            return ['status' => 302, 'location' => '/'];
        }
        if (!self::isDrillFamily($slug)) {
            return ['status' => 200, 'body' => self::notADrillHtml($slug)];
        }
        $drill = self::load($slug);
        if ($drill === null) {
            return [
                'status' => 200,
                'body' => '<!DOCTYPE html><html lang="en"><body><main>'
                    . '<p><strong>SAMPLE</strong> data only.</p>'
                    . '<p class="slot">not authored yet</p></main></body></html>',
            ];
        }

        return ['status' => 200, 'body' => self::renderHtml($drill)];
    }

    public static function notADrillHtml(string $slug): string
    {
        return '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Not a drill</title></head><body>'
            . '<main class="drill-slot" data-slug="' . self::h($slug) . '">'
            . '<p><strong>SAMPLE</strong> data only.</p>'
            . '<p class="slot">This family is not a drill.</p>'
            . '</main></body></html>';
    }

    /** @param array<string, mixed> $drill */
    public static function renderHtml(array $drill): string
    {
        self::assertValid($drill);
        $slug = (string) $drill['slug'];
        $parts = ['<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">',
            '<title>Drill — ' . self::h($slug) . '</title></head><body>',
            '<main class="drill-page" data-slug="' . self::h($slug) . '" data-timed="1">',
            '<p><strong>SAMPLE</strong> data only. Timed drill.</p>',
            '<h1>Drill: ' . self::h($slug) . '</h1>'];
        foreach ($drill['items'] as $i => $item) {
            $parts[] = '<section class="drill-item" data-index="' . (int) $i . '" data-seconds="'
                . (int) $item['seconds'] . '">';
            $parts[] = '<p class="prompt">' . self::h((string) ($item['prompt'] ?? '')) . '</p>';
            $parts[] = '<p class="thought-chain" hidden>' . self::h(implode(' | ', $item['thoughtChain'])) . '</p>';
            $parts[] = '</section>';
        }
        $parts[] = '</main></body></html>';

        return implode("\n", $parts);
    }

    /**
     * @param array<string, mixed> $state
     * @param list<bool> $correctFlags
     * @param list<int> $latenciesMs
     * @return array<string, mixed>
     */
    public static function recordFinish(array $state, string $slug, array $correctFlags, array $latenciesMs): array
    {
        $state = ProgressSchema::normalize($state);
        $total = count($correctFlags);
        $correct = count(array_filter($correctFlags));
        $accuracy = $total > 0 ? $correct / $total : 0.0;
        $sorted = $latenciesMs;
        sort($sorted);
        $median = $sorted === [] ? null : $sorted[(int) floor((count($sorted) - 1) / 2)];
        $id = $state['pathId'] . ':' . $slug;
        if (isset($state['milestones'][$id])) {
            $state['milestones'][$id]['drillAccuracy'] = $accuracy;
            $state['milestones'][$id]['drillMedianLatencyMs'] = $median;
        }
        $state['scores']['drill:' . $slug] = ['correct' => $correct, 'total' => $total, 'accuracy' => $accuracy];
        $state['latencies']['drill:' . $slug] = $latenciesMs;
        $state['recovery'] = false;

        return $state;
    }

    private static function h(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
