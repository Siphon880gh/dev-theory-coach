<?php

declare(strict_types=1);

namespace TheoryCore;

/**
 * Shared HTML5 document: charset, viewport, fonts, stylesheet, site header + panel.
 */
final class Layout
{
    /**
     * @param array{
     *   mainClass?: string,
     *   mainAttrs?: array<string, string|int>,
     *   scripts?: list<string>,
     *   wide?: bool,
     *   bodyClass?: string,
     *   sampleNote?: string,
     *   navActive?: string
     * } $options
     */
    public static function document(string $title, string $mainInnerHtml, array $options = []): string
    {
        $mainClass = (string) ($options['mainClass'] ?? '');
        /** @var array<string, string|int> $attrs */
        $attrs = isset($options['mainAttrs']) && is_array($options['mainAttrs'])
            ? $options['mainAttrs']
            : [];
        /** @var list<string> $scripts */
        $scripts = isset($options['scripts']) && is_array($options['scripts'])
            ? $options['scripts']
            : [];
        $wide = !empty($options['wide']);
        $bodyClass = (string) ($options['bodyClass'] ?? '');
        $sampleNote = (string) ($options['sampleNote'] ?? '');
        $navActive = (string) ($options['navActive'] ?? '');

        $mainOpen = '<main id="main"';
        if ($mainClass !== '') {
            $mainOpen .= ' class="' . e($mainClass) . '"';
        }
        foreach ($attrs as $key => $value) {
            $mainOpen .= ' ' . e((string) $key) . '="' . e((string) $value) . '"';
        }
        $mainOpen .= '>';

        $shellClass = $wide ? 'shell shell--wide' : 'shell';
        $homeClass = $navActive === 'home' ? ' is-active' : '';
        $guidesClass = $navActive === 'guides' ? ' is-active' : '';

        $parts = [];
        $parts[] = '<!DOCTYPE html>';
        $parts[] = '<html lang="en">';
        $parts[] = '<head>';
        $parts[] = '<meta charset="UTF-8">';
        $parts[] = '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $parts[] = '<title>' . e($title) . '</title>';
        $parts[] = '<link rel="preconnect" href="https://fonts.googleapis.com">';
        $parts[] = '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
        $parts[] = '<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">';
        $parts[] = '<link rel="stylesheet" href="' . e(asset_url('css/app.css')) . '">';
        $parts[] = '</head>';
        $parts[] = '<body' . ($bodyClass !== '' ? ' class="' . e($bodyClass) . '"' : '') . '>';
        $parts[] = '<a class="skip-link" href="#main">Skip to content</a>';
        $parts[] = '<div class="' . e($shellClass) . '">';
        $parts[] = '<header class="site-header">';
        $parts[] = '<a class="brand" href="' . e(url('/')) . '">Dev Theory Coach</a>';
        $parts[] = '<nav class="site-nav" aria-label="Primary">';
        $parts[] = '<a class="nav-link' . $homeClass . '" href="' . e(url('/')) . '">Home</a>';
        $parts[] = '<a class="nav-link' . $guidesClass . '" href="' . e(url('/guides')) . '">Guides</a>';
        $parts[] = '</nav>';
        $parts[] = '</header>';
        $parts[] = '<div class="panel">';
        if ($sampleNote !== '') {
            $parts[] = '<p class="meta-note">' . e($sampleNote) . '</p>';
        }
        $parts[] = $mainOpen;
        $parts[] = $mainInnerHtml;
        $parts[] = '</main>';
        $parts[] = '</div>';
        $parts[] = '</div>';
        foreach ($scripts as $src) {
            $parts[] = '<script src="' . e((string) $src) . '"></script>';
        }
        $parts[] = '</body>';
        $parts[] = '</html>';

        return implode("\n", $parts);
    }
}
