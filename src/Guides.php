<?php

declare(strict_types=1);

namespace TheoryCore;

use DOMDocument;
use DOMElement;
use DOMNode;
use RuntimeException;

/**
 * Guide registry and HTML renderers for /guides and /guides/{slug}.
 * Educational practice data only (no live systems).
 */
final class Guides
{
    /** SVG element allowlist for guide diagrams. */
    private const SVG_TAGS = [
        'svg' => true,
        'g' => true,
        'rect' => true,
        'text' => true,
        'line' => true,
        'path' => true,
        'circle' => true,
        'polyline' => true,
        'polygon' => true,
        'title' => true,
        'desc' => true,
    ];

    /** Safe attribute allowlist (no event handlers, no external hrefs). */
    private const SVG_ATTRS = [
        'viewbox' => true,
        'xmlns' => true,
        'width' => true,
        'height' => true,
        'role' => true,
        'aria-hidden' => true,
        'aria-label' => true,
        'class' => true,
        'id' => true,
        'fill' => true,
        'stroke' => true,
        'stroke-width' => true,
        'stroke-dasharray' => true,
        'stroke-linecap' => true,
        'stroke-linejoin' => true,
        'stroke-opacity' => true,
        'fill-opacity' => true,
        'opacity' => true,
        'x' => true,
        'y' => true,
        'x1' => true,
        'y1' => true,
        'x2' => true,
        'y2' => true,
        'cx' => true,
        'cy' => true,
        'r' => true,
        'rx' => true,
        'ry' => true,
        'dx' => true,
        'dy' => true,
        'width' => true,
        'height' => true,
        'points' => true,
        'd' => true,
        'transform' => true,
        'font-size' => true,
        'font-family' => true,
        'font-weight' => true,
        'text-anchor' => true,
        'dominant-baseline' => true,
    ];

    /**
     * @return array{
     *   slug: string,
     *   title: string,
     *   summary: string,
     *   body: string,
     *   relatedCoachSlug: string,
     *   parts?: array<string, string>,
     *   diagrams?: list<array{id: string, title: string, svg: string}>,
     * }|null
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
     * YouTube results URL for track label + guide title (override via youtubeQuery).
     */
    public static function youtubeSearchUrl(string $trackLabel, string $title, ?string $overrideQuery = null): string
    {
        $q = $overrideQuery !== null && trim($overrideQuery) !== ''
            ? trim($overrideQuery)
            : trim($trackLabel . ' ' . $title);

        return 'https://www.youtube.com/results?' . http_build_query(['search_query' => $q]);
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
        /** @var list<array{id?: mixed, title?: mixed, svg?: mixed}> $diagrams */
        $diagrams = [];
        if (is_array($guide) && isset($guide['diagrams']) && is_array($guide['diagrams'])) {
            $diagrams = $guide['diagrams'];
        }

        $parts = [];
        $parts[] = '<p><a href="' . e(url('/guides')) . '">All guides</a></p>';
        $parts[] = '<h1>' . self::h($title) . '</h1>';
        $parts[] = '<p class="summary">' . self::h($summary) . '</p>';
        if ($body === '') {
            $parts[] = '<p class="slot">Guide body not authored yet.</p>';
        } else {
            $parts[] = '<article class="guide-body">' . self::formatBody($body, $diagrams) . '</article>';
        }
        $trackLabel = (string) ($family['track_label'] ?? '');
        $ytOverride = null;
        if (is_array($guide) && isset($guide['youtubeQuery']) && is_string($guide['youtubeQuery'])) {
            $ytOverride = $guide['youtubeQuery'];
        }
        $youtubeUrl = self::youtubeSearchUrl($trackLabel, $title, $ytOverride);
        $parts[] = self::guideFooterHtml($slug, $title, $trackLabel, $coachSlug, $youtubeUrl);

        $layoutOpts = [
            'mainClass' => 'guide-page',
            'mainAttrs' => ['data-slug' => $slug],
            'navActive' => 'guides',
        ];
        if (Diagrams::needsMermaid($diagrams)) {
            $layoutOpts['scripts'] = Diagrams::mermaidScripts();
        }

        return [
            'status' => 200,
            'body' => Layout::document(
                $title . ' — Guide',
                implode("\n", $parts),
                $layoutOpts
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

    /**
     * @param list<array{id?: mixed, title?: mixed, svg?: mixed}> $diagrams
     */


    /**
     * Bottom of a guide: YouTube, end-of-lesson quiz (questions), quiet coach link, then prev/next.
     */
    private static function guideFooterHtml(
        string $slug,
        string $title,
        string $trackLabel,
        string $coachSlug,
        string $youtubeUrl
    ): string {
        $topic = trim($trackLabel . ' · ' . $title);
        if ($topic === '·' || $topic === '') {
            $topic = $title !== '' ? $title : $slug;
        }

        $parts = [];
        $parts[] = '<footer class="guide-footer">';

        $parts[] = '<section class="guide-footer__block guide-footer__questions" aria-labelledby="guide-questions-heading">';
        $parts[] = '<h2 id="guide-questions-heading" class="guide-footer__heading">'
            . '<span class="guide-footer__icon" aria-hidden="true">'
            . '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" '
            . 'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'
            . '<circle cx="12" cy="12" r="10"/>'
            . '<path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>'
            . '<line x1="12" y1="17" x2="12.01" y2="17"/>'
            . '</svg></span>'
            . '<span class="guide-footer__label">Questions</span></h2>';
        $parts[] = '<p class="guide-footer__blurb">Check yourself with a short quiz on this lesson.</p>';
        $parts[] = '<p class="guide-footer__action"><a class="guide-footer__link guide-footer__link--quiz" href="'
            . e(url('/quiz/' . rawurlencode($slug))) . '">Take the quiz</a></p>';
        $parts[] = '<p class="guide-footer__secondary"><a class="guide-footer__link guide-footer__link--coach" href="'
            . e(url('/coach/' . rawurlencode($coachSlug))) . '">Optional: step-by-step practice</a></p>';
        $parts[] = '</section>';

        $parts[] = '<section class="guide-footer__block guide-footer__youtube" aria-labelledby="guide-youtube-heading">';
        $parts[] = '<h2 id="guide-youtube-heading" class="guide-footer__heading">'
            . '<span class="guide-footer__icon" aria-hidden="true">'
            . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 3.5 24 17" fill="currentColor">'
            . '<path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2 31.5 31.5 0 0 0 0 12a31.5 31.5 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1A31.5 31.5 0 0 0 24 12a31.5 31.5 0 0 0-.5-5.8zM9.75 15.5v-7l6.5 3.5-6.5 3.5z"/>'
            . '</svg></span>'
            . '<span class="guide-footer__label">YouTube</span></h2>';
        $parts[] = '<p class="guide-footer__blurb">Short videos on this topic (opens YouTube search).</p>';
        $parts[] = '<p class="guide-footer__action"><a class="guide-footer__link guide-footer__link--youtube" href="'
            . e($youtubeUrl) . '" target="_blank" rel="noopener noreferrer">'
            . 'Watch on YouTube — ' . self::h($topic) . '</a></p>';
        $parts[] = '</section>';

        $parts[] = self::lessonNavHtml($slug);
        $parts[] = '</footer>';

        return implode("\n", $parts);
    }

    private static function lessonNavHtml(string $slug): string
    {
        $nav = Catalog::neighbors($slug);
        $parts = [];
        $parts[] = '<nav class="guide-lesson-nav" id="guide-end" aria-label="Lesson">';
        $parts[] = '<div class="guide-lesson-nav__row">';
        if ($nav['prev'] !== null) {
            $parts[] = '<a class="guide-lesson-nav__link guide-lesson-nav__link--prev" href="'
                . e(url('/guides/' . rawurlencode($nav['prev']['slug']))) . '">'
                . '<span class="guide-lesson-nav__dir">Previous</span>'
                . '<span class="guide-lesson-nav__title">' . self::h($nav['prev']['title']) . '</span>'
                . '</a>';
        } else {
            $parts[] = '<span class="guide-lesson-nav__link guide-lesson-nav__link--prev is-disabled" aria-disabled="true">'
                . '<span class="guide-lesson-nav__dir">Previous</span>'
                . '<span class="guide-lesson-nav__title">—</span>'
                . '</span>';
        }
        if ($nav['next'] !== null) {
            $parts[] = '<a class="guide-lesson-nav__link guide-lesson-nav__link--next" href="'
                . e(url('/guides/' . rawurlencode($nav['next']['slug']))) . '">'
                . '<span class="guide-lesson-nav__dir">Next</span>'
                . '<span class="guide-lesson-nav__title">' . self::h($nav['next']['title']) . '</span>'
                . '</a>';
        } else {
            $parts[] = '<span class="guide-lesson-nav__link guide-lesson-nav__link--next is-disabled" aria-disabled="true">'
                . '<span class="guide-lesson-nav__dir">Next</span>'
                . '<span class="guide-lesson-nav__title">—</span>'
                . '</span>';
        }
        $parts[] = '</div>';
        $parts[] = '</nav>';

        return implode("\n", $parts);
    }

    public static function formatBody(string $body, array $diagrams = []): string
    {
        $byId = Diagrams::indexById($diagrams);

        $paras = preg_split("/\n\s*\n/", trim($body)) ?: [];
        $out = [];
        foreach ($paras as $p) {
            $trim = trim($p);
            if ($trim === '') {
                continue;
            }
            if (preg_match('/^\[\[diagram:([a-z0-9_-]+)\]\]$/i', $trim, $m) === 1) {
                $out[] = self::renderDiagramFigure($m[1], $byId);
                continue;
            }
            $html = nl2br(self::h($trim), false);
            $html = preg_replace_callback(
                '/\[\[diagram:([a-z0-9_-]+)\]\]/i',
                static function (array $mm) use ($byId): string {
                    return '</p>' . self::renderDiagramFigure($mm[1], $byId) . '<p>';
                },
                $html
            ) ?? $html;
            $out[] = '<p>' . $html . '</p>';
        }

        return implode("\n", $out);
    }

    /**
     * @param array<string, array{id?: mixed, title?: mixed, svg?: mixed}> $byId
     */
    private static function renderDiagramFigure(string $id, array $byId): string
    {
        if (!isset($byId[$id])) {
            return '<!-- missing diagram: ' . self::h($id) . ' -->';
        }

        return Diagrams::renderFigure($byId[$id]);
    }

    public static function sanitizeSvg(string $svg): string
    {
        return Diagrams::sanitizeSvg($svg);
    }

    private static function h(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
