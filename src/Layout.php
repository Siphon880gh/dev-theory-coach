<?php

declare(strict_types=1);

namespace TheoryCore;

/**
 * Shared HTML5 document: charset, viewport, one stylesheet, shell/card.
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
     *   sampleNote?: string
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

        $mainOpen = '<main';
        if ($mainClass !== '') {
            $mainOpen .= ' class="' . e($mainClass) . '"';
        }
        foreach ($attrs as $key => $value) {
            $mainOpen .= ' ' . e((string) $key) . '="' . e((string) $value) . '"';
        }
        $mainOpen .= '>';

        $shellClass = $wide ? 'shell shell--wide' : 'shell';
        $parts = [];
        $parts[] = '<!DOCTYPE html>';
        $parts[] = '<html lang="en">';
        $parts[] = '<head>';
        $parts[] = '<meta charset="UTF-8">';
        $parts[] = '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $parts[] = '<title>' . e($title) . '</title>';
        $parts[] = '<link rel="stylesheet" href="' . e(asset_url('css/app.css')) . '">';
        $parts[] = '</head>';
        $parts[] = '<body' . ($bodyClass !== '' ? ' class="' . e($bodyClass) . '"' : '') . '>';
        $parts[] = '<div class="' . e($shellClass) . '">';
        $parts[] = '<div class="card">';
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
