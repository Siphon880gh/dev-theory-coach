<?php

declare(strict_types=1);

namespace TheoryCore;

use RuntimeException;

/**
 * Guide registry and HTML renderers for /guides and /guides/{slug}.
 * Educational practice data only (no live systems).
 */
final class Guides
{
    /**
     * @return array{slug: string, title: string, summary: string, body: string, relatedCoachSlug: string, parts?: array<string, string>}|null
     */
    public static function load(string $slug): ?array
    {
        $path = dirname(__DIR__) . '/content/guides/' . $slug . '.json';
        if (!is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new RuntimeException('Unreadable guide: ' . $slug);
        }
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (($decoded['slug'] ?? null) !== $slug) {
            throw new RuntimeException('Guide slug mismatch for ' . $slug);
        }

        return $decoded;
    }

    public static function hubHtml(): string
    {
        return self::hubHtmlWithLocks(null);
    }

    /**
     * @return array{status: int, location?: string, body?: string}
     */
    public static function pageResponse(string $slug): array
    {
        $family = Catalog::bySlug($slug);
        if ($family === null) {
            return UnknownPage::slugResponse('guide', $slug);
        }

        $guide = self::load($slug);
        $title = (string) ($guide['title'] ?? $family['title'] ?? $slug);
        $summary = (string) ($guide['summary'] ?? $family['summary'] ?? '');
        $body = (string) ($guide['body'] ?? '');
        $coachSlug = (string) ($guide['relatedCoachSlug'] ?? $slug);

        $parts = [];
        $parts[] = '<p><a href="' . e(url('/guides')) . '">All guides</a></p>';
        $parts[] = '<h1>' . self::h($title) . '</h1>';
        $parts[] = '<p class="summary">' . self::h($summary) . '</p>';
        if ($body === '') {
            $parts[] = '<p class="slot">Guide body not authored yet.</p>';
        } else {
            $parts[] = '<article class="guide-body">' . self::formatBody($body) . '</article>';
        }
        $parts[] = '<p class="coach-cta"><a href="' . e(url('/coach/' . rawurlencode($coachSlug))) . '">Open coach: '
            . self::h($coachSlug) . '</a></p>';

        return [
            'status' => 200,
            'body' => Layout::document(
                $title . ' — Guide',
                implode("\n", $parts),
                [
                    'mainClass' => 'guide-page',
                    'mainAttrs' => ['data-slug' => $slug],
                    'navActive' => 'guides',
                ]
            ),
        ];
    }


    /**
     * Hub row locked-reason helper for E11.
     *
     * @param array<string, mixed> $state
     */
    public static function lockedReasonFor(string $slug, array $state): string
    {
        $family = Catalog::bySlug($slug);
        if ($family === null) {
            return '';
        }
        /** @var list<string> $after */
        $after = $family['after'] ?? [];
        if ($after === []) {
            return '';
        }
        if (ProgressSchema::isFamilyUnlockedByPrereqs($state, $after)) {
            return '';
        }
        $missing = ProgressSchema::missingPrereqSlugs($state, $after);

        return 'Locked — missing: ' . implode(', ', $missing);
    }

    /**
     * @param array<string, mixed>|null $state
     */
    public static function hubHtmlWithLocks(?array $state = null): string
    {
        $state = ProgressSchema::normalize($state ?? ProgressSchema::fresh());
        $parts = [];
        $parts[] = '<h1>Guides</h1>';
        foreach (Catalog::groupedByTrackLabel() as $label => $families) {
            $parts[] = '<section class="track" data-track-label="' . self::h($label) . '">';
            $parts[] = '<h2>' . self::h($label) . '</h2><ul>';
            foreach ($families as $family) {
                $slug = (string) $family['slug'];
                $title = (string) ($family['title'] ?? $slug);
                $reason = self::lockedReasonFor($slug, $state);
                $parts[] = '<li data-slug="' . self::h($slug) . '">'
                    . '<a href="' . e(url('/guides/' . rawurlencode($slug))) . '">' . self::h($title) . '</a>'
                    . ($reason !== '' ? ' <span class="locked-reason">' . self::h($reason) . '</span>' : '')
                    . '</li>';
            }
            $parts[] = '</ul></section>';
        }

        return Layout::document(
            'Guides — Dev Theory Coach',
            implode("\n", $parts),
            [
                'mainClass' => 'guides-hub',
                'wide' => true,
                'navActive' => 'guides',
            ]
        );
    }

    private static function formatBody(string $body): string
    {
        $paras = preg_split("/\n\s*\n/", trim($body)) ?: [];
        $out = [];
        foreach ($paras as $p) {
            $out[] = '<p>' . nl2br(self::h(trim($p)), false) . '</p>';
        }

        return implode("\n", $out);
    }

    private static function h(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
