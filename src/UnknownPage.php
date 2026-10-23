<?php

declare(strict_types=1);

namespace TheoryCore;

/**
 * 200 "unknown" pages. Never 404, never redirect home.
 */
final class UnknownPage
{
    public static function slugResponse(string $kind, string $slug): array
    {
        return ['status' => 200, 'body' => self::slugHtml($kind, $slug)];
    }

    public static function slugHtml(string $kind, string $slug): string
    {
        $inner = '<p class="slot unknown">Unknown ' . e($kind) . ': <code>' . e($slug) . '</code> is not in the catalog.</p>'
            . "\n" . '<p><a href="' . e(url('/')) . '">Home</a> · <a href="' . e(url('/guides')) . '">Guides</a></p>';

        return Layout::document(
            'Unknown ' . $kind,
            $inner,
            ['mainClass' => 'unknown-page']
        );
    }

    public static function pathHtml(string $path): string
    {
        $inner = '<p class="slot unknown">Unknown page: <code>' . e($path) . '</code></p>'
            . "\n" . '<p><a href="' . e(url('/')) . '">Home</a></p>';

        return Layout::document(
            'Unknown page',
            $inner,
            ['mainClass' => 'unknown-page']
        );
    }
}
