<?php

declare(strict_types=1);

namespace TheoryCore;

use RuntimeException;

/**
 * Loads the family catalog from content/families.json.
 * Join key is slug. Educational practice data only (no live systems).
 */
final class Catalog
{
    private static ?array $data = null;

    /** Full track labels, keyed by track letter (A–D). */
    public const TRACK_LABELS = [
        'A' => 'Data structures',
        'B' => 'Algorithms',
        'C' => 'System design',
        'D' => 'Design patterns',
    ];

    /** @return list<array<string, mixed>> */
    public static function all(): array
    {
        return self::data()['families'];
    }

    /** @return list<string> */
    public static function slugs(): array
    {
        $slugs = [];
        foreach (self::all() as $family) {
            if (!isset($family['slug']) || !is_string($family['slug'])) {
                throw new RuntimeException('Family row missing string slug');
            }
            $slugs[] = $family['slug'];
        }

        return $slugs;
    }

    /**
     * @return array{slug: string, track: string, track_id: string, track_label: string, title: string, summary: string, after: list<string>, rungs: list<string>}&array<string, mixed>
     */
    public static function bySlug(string $slug): ?array
    {
        foreach (self::all() as $family) {
            if (($family['slug'] ?? null) === $slug) {
                return $family;
            }
        }

        return null;
    }

    /** @return array<string, string> track letter => full label */
    public static function trackLabels(): array
    {
        $fromFile = self::data()['tracks'] ?? null;
        if (is_array($fromFile) && $fromFile !== []) {
            $out = [];
            foreach ($fromFile as $id => $label) {
                if (is_string($id) && is_string($label)) {
                    $out[$id] = $label;
                }
            }
            if ($out !== []) {
                return $out;
            }
        }

        return self::TRACK_LABELS;
    }

    /**
     * Families grouped by full track label, preserving catalog order within each track.
     *
     * @return array<string, list<array<string, mixed>>>
     */
    public static function groupedByTrackLabel(): array
    {
        $labels = self::trackLabels();
        $groups = [];
        foreach ($labels as $label) {
            $groups[$label] = [];
        }
        foreach (self::all() as $family) {
            $label = $family['track_label'] ?? ($labels[$family['track'] ?? ''] ?? null);
            if (!is_string($label) || $label === '') {
                throw new RuntimeException('Family missing track_label: ' . (string) ($family['slug'] ?? '?'));
            }
            if (!isset($groups[$label])) {
                $groups[$label] = [];
            }
            $groups[$label][] = $family;
        }

        return $groups;
    }

    /**
     * Hub grouping fixture: HTML string with full track labels and family titles.
     */
    public static function hubGroupingHtml(): string
    {
        $parts = ['<div class="guides-hub">'];
        foreach (self::groupedByTrackLabel() as $label => $families) {
            $parts[] = '<section class="track" data-track-label="' . htmlspecialchars($label, ENT_QUOTES) . '">';
            $parts[] = '<h2>' . htmlspecialchars($label, ENT_QUOTES) . '</h2>';
            $parts[] = '<ul>';
            foreach ($families as $family) {
                $slug = (string) $family['slug'];
                $title = (string) ($family['title'] ?? $slug);
                $parts[] = '<li data-slug="' . htmlspecialchars($slug, ENT_QUOTES) . '">'
                    . htmlspecialchars($title, ENT_QUOTES)
                    . '</li>';
            }
            $parts[] = '</ul></section>';
        }
        $parts[] = '</div>';

        return implode("\n", $parts);
    }

    /** Clear cached JSON (tests). */
    public static function reset(): void
    {
        self::$data = null;
    }

    /** @return array{families: list<array<string, mixed>>, tracks?: array<string, string>} */
    private static function data(): array
    {
        if (self::$data !== null) {
            return self::$data;
        }

        $path = dirname(__DIR__) . '/content/families.json';
        $raw = @file_get_contents($path);
        if ($raw === false) {
            throw new RuntimeException('Missing content/families.json');
        }

        /** @var array{families?: list<array<string, mixed>>, tracks?: array<string, string>} $decoded */
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (!isset($decoded['families']) || !is_array($decoded['families'])) {
            throw new RuntimeException('content/families.json must contain a families array');
        }

        self::$data = $decoded;

        return self::$data;
    }

    /**
     * Previous/next family in catalog order (hub order).
     *
     * @return array{prev: ?array{slug: string, title: string}, next: ?array{slug: string, title: string}}
     */
    public static function neighbors(string $slug): array
    {
        $all = self::all();
        $index = null;
        foreach ($all as $i => $family) {
            if (($family['slug'] ?? null) === $slug) {
                $index = $i;
                break;
            }
        }
        if ($index === null) {
            return ['prev' => null, 'next' => null];
        }
        $prev = null;
        $next = null;
        if ($index > 0) {
            $f = $all[$index - 1];
            $prev = [
                'slug' => (string) $f['slug'],
                'title' => (string) ($f['title'] ?? $f['slug']),
            ];
        }
        if ($index < count($all) - 1) {
            $f = $all[$index + 1];
            $next = [
                'slug' => (string) $f['slug'],
                'title' => (string) ($f['title'] ?? $f['slug']),
            ];
        }

        return ['prev' => $prev, 'next' => $next];
    }

}
