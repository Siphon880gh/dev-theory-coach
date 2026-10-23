<?php

declare(strict_types=1);

namespace TheoryCore;

use RuntimeException;

/**
 * Guide registry and HTML renderers for /guides and /guides/{slug}.
 * SAMPLE / educational data only.
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
        $parts = [];
        $parts[] = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">';
        $parts[] = '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $parts[] = '<title>Guides — Dev Theory Coach</title></head><body>';
        $parts[] = '<main class="guides-hub">';
        $parts[] = '<p><strong>SAMPLE</strong> data only.</p>';
        $parts[] = '<h1>Guides</h1>';
        $parts[] = '<p>Interview concepts by track. Open a guide, then its coach on the same slug.</p>';
        foreach (Catalog::groupedByTrackLabel() as $label => $families) {
            $parts[] = '<section class="track" data-track-label="' . self::h($label) . '">';
            $parts[] = '<h2>' . self::h($label) . '</h2><ul>';
            foreach ($families as $family) {
                $slug = (string) $family['slug'];
                $title = (string) ($family['title'] ?? $slug);
                $parts[] = '<li data-slug="' . self::h($slug) . '">'
                    . '<a href="/guides/' . rawurlencode($slug) . '">' . self::h($title) . '</a>'
                    . ' <code>' . self::h($slug) . '</code>'
                    . '</li>';
            }
            $parts[] = '</ul></section>';
        }
        $parts[] = '</main></body></html>';

        return implode("\n", $parts);
    }

    /**
     * @return array{status: int, location?: string, body?: string}
     */
    public static function pageResponse(string $slug): array
    {
        $family = Catalog::bySlug($slug);
        if ($family === null) {
            return ['status' => 302, 'location' => '/'];
        }

        $guide = self::load($slug);
        $title = (string) ($guide['title'] ?? $family['title'] ?? $slug);
        $summary = (string) ($guide['summary'] ?? $family['summary'] ?? '');
        $body = (string) ($guide['body'] ?? '');
        $coachSlug = (string) ($guide['relatedCoachSlug'] ?? $slug);

        $html = [];
        $html[] = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">';
        $html[] = '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $html[] = '<title>' . self::h($title) . ' — Guide</title></head><body>';
        $html[] = '<main class="guide-page" data-slug="' . self::h($slug) . '">';
        $html[] = '<p><strong>SAMPLE</strong> data only.</p>';
        $html[] = '<p><a href="/guides">All guides</a></p>';
        $html[] = '<h1>' . self::h($title) . '</h1>';
        $html[] = '<p class="summary">' . self::h($summary) . '</p>';
        if ($body === '') {
            $html[] = '<p class="slot">Guide body not authored yet.</p>';
        } else {
            $html[] = '<article class="guide-body">' . self::formatBody($body) . '</article>';
        }
        $html[] = '<p class="coach-cta"><a href="/coach/' . rawurlencode($coachSlug) . '">Open coach: '
            . self::h($coachSlug) . '</a></p>';
        $html[] = '</main></body></html>';

        return ['status' => 200, 'body' => implode("\n", $html)];
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
