<?php

declare(strict_types=1);

namespace TheoryCore;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Shared diagram rendering for guides, coach, and quiz.
 * Types: svg (default), mermaid (client-side), image (static asset).
 */
final class Diagrams
{
    private const SVG_TAGS = [
        'svg' => true, 'g' => true, 'rect' => true, 'text' => true, 'line' => true,
        'path' => true, 'circle' => true, 'polyline' => true, 'polygon' => true,
        'title' => true, 'desc' => true,
    ];

    private const SVG_ATTRS = [
        'viewbox' => true, 'xmlns' => true, 'width' => true, 'height' => true,
        'role' => true, 'aria-hidden' => true, 'aria-label' => true, 'class' => true, 'id' => true,
        'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-dasharray' => true,
        'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke-opacity' => true,
        'fill-opacity' => true, 'opacity' => true,
        'x' => true, 'y' => true, 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true,
        'cx' => true, 'cy' => true, 'r' => true, 'rx' => true, 'ry' => true, 'dx' => true, 'dy' => true,
        'points' => true, 'd' => true, 'transform' => true,
        'font-size' => true, 'font-family' => true, 'font-weight' => true,
        'text-anchor' => true, 'dominant-baseline' => true,
    ];

    /**
     * @param array{id?: mixed, title?: mixed, type?: mixed, svg?: mixed, mermaid?: mixed, src?: mixed} $diagram
     */
    public static function renderFigure(array $diagram): string
    {
        $id = isset($diagram['id']) && is_string($diagram['id']) ? $diagram['id'] : '';
        $title = isset($diagram['title']) && is_string($diagram['title']) ? $diagram['title'] : $id;
        $type = isset($diagram['type']) && is_string($diagram['type']) ? strtolower($diagram['type']) : 'svg';
        if ($type === '' || $type === 'svg') {
            if (isset($diagram['mermaid']) && is_string($diagram['mermaid']) && trim($diagram['mermaid']) !== ''
                && (!isset($diagram['svg']) || !is_string($diagram['svg']) || trim($diagram['svg']) === '')) {
                $type = 'mermaid';
            } elseif (isset($diagram['src']) && is_string($diagram['src']) && trim($diagram['src']) !== ''
                && (!isset($diagram['svg']) || !is_string($diagram['svg']) || trim($diagram['svg']) === '')) {
                $type = 'image';
            }
        }

        $inner = '';
        if ($type === 'mermaid') {
            $src = isset($diagram['mermaid']) && is_string($diagram['mermaid']) ? trim($diagram['mermaid']) : '';
            if ($src === '') {
                return '<!-- empty mermaid: ' . self::h($id) . ' -->';
            }
            $inner = '<pre class="mermaid">' . self::h($src) . '</pre>';
        } elseif ($type === 'image') {
            $src = isset($diagram['src']) && is_string($diagram['src']) ? trim($diagram['src']) : '';
            if ($src === '' || str_contains($src, '..') || !preg_match('#^diagrams/[a-z0-9_./-]+\\.(png|jpe?g|gif|webp|svg)$#i', $src)) {
                return '<!-- bad image src: ' . self::h($id) . ' -->';
            }
            $abs = dirname(__DIR__) . '/assets/' . $src;
            if (!is_file($abs)) {
                return '<!-- missing image: ' . self::h($src) . ' -->';
            }
            $inner = '<img src="' . e(asset_url($src)) . '" alt="' . self::h($title !== '' ? $title : $id) . '" loading="lazy">';
        } else {
            $svg = isset($diagram['svg']) && is_string($diagram['svg']) ? $diagram['svg'] : '';
            $safe = self::sanitizeSvg($svg);
            if ($safe === '') {
                return '<!-- empty diagram: ' . self::h($id) . ' -->';
            }
            $inner = $safe;
        }

        return '<figure class="guide-diagram" data-diagram-id="' . self::h($id) . '" data-diagram-type="' . self::h($type) . '">'
            . $inner
            . ($title !== '' ? '<figcaption>' . self::h($title) . '</figcaption>' : '')
            . '</figure>';
    }

    /**
     * @param list<array{id?: mixed, title?: mixed, svg?: mixed, type?: mixed, mermaid?: mixed, src?: mixed}> $diagrams
     * @return array<string, array{id?: mixed, title?: mixed, svg?: mixed, type?: mixed, mermaid?: mixed, src?: mixed}>
     */
    public static function indexById(array $diagrams): array
    {
        $byId = [];
        foreach ($diagrams as $d) {
            if (!is_array($d)) {
                continue;
            }
            $id = isset($d['id']) && is_string($d['id']) ? $d['id'] : '';
            if ($id === '') {
                continue;
            }
            $byId[$id] = $d;
        }

        return $byId;
    }

    /**
     * @param list<array{id?: mixed, type?: mixed, mermaid?: mixed, svg?: mixed}> $diagrams
     */
    public static function needsMermaid(array $diagrams): bool
    {
        foreach ($diagrams as $d) {
            if (!is_array($d)) {
                continue;
            }
            $type = isset($d['type']) && is_string($d['type']) ? strtolower($d['type']) : '';
            if ($type === 'mermaid') {
                return true;
            }
            if (isset($d['mermaid']) && is_string($d['mermaid']) && trim($d['mermaid']) !== ''
                && ($type === '' || $type === 'svg')
                && (!isset($d['svg']) || !is_string($d['svg']) || trim($d['svg']) === '')) {
                return true;
            }
        }

        return false;
    }

    /** CDN + dark-theme boot for Mermaid (no bundler). */
    public static function mermaidScripts(): array
    {
        return [
            'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js',
            asset_url('js/mermaid-boot.js'),
        ];
    }

    public static function sanitizeSvg(string $svg): string
    {
        $svg = trim($svg);
        if ($svg === '' || stripos($svg, '<svg') === false) {
            return '';
        }

        $prev = libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $wrapped = '<?xml encoding="UTF-8">' . $svg;
        $ok = $dom->loadHTML($wrapped, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        if ($ok === false) {
            return '';
        }

        $svgs = $dom->getElementsByTagName('svg');
        if ($svgs->length === 0) {
            return '';
        }
        /** @var DOMElement $root */
        $root = $svgs->item(0);
        $clean = new DOMDocument('1.0', 'UTF-8');
        $imported = self::cloneAllowed($root, $clean);
        if ($imported === null) {
            return '';
        }
        $clean->appendChild($imported);
        $out = $clean->saveHTML($imported);

        return is_string($out) ? $out : '';
    }

    private static function cloneAllowed(DOMNode $node, DOMDocument $target): ?DOMNode
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            return $target->createTextNode($node->textContent ?? '');
        }
        if ($node->nodeType !== XML_ELEMENT_NODE || !$node instanceof DOMElement) {
            return null;
        }
        $tag = strtolower($node->tagName);
        if (!isset(self::SVG_TAGS[$tag])) {
            return null;
        }
        $el = $target->createElement($tag);
        if ($node->hasAttributes()) {
            foreach ($node->attributes ?? [] as $attr) {
                $name = strtolower($attr->name);
                if (str_starts_with($name, 'on')) {
                    continue;
                }
                if ($name === 'href' || $name === 'xlink:href') {
                    continue;
                }
                if (!isset(self::SVG_ATTRS[$name])) {
                    continue;
                }
                $val = $attr->value;
                if ($name === 'xmlns' && $val !== 'http://www.w3.org/2000/svg') {
                    continue;
                }
                if (preg_match('/javascript:/i', $val) === 1) {
                    continue;
                }
                $el->setAttribute($attr->name, $val);
            }
        }
        foreach ($node->childNodes as $child) {
            $c = self::cloneAllowed($child, $target);
            if ($c !== null) {
                $el->appendChild($c);
            }
        }

        return $el;
    }

    private static function h(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Diagrams declared on the guide for a slug, indexed by id.
     *
     * @return array<string, array{id?: mixed, title?: mixed, svg?: mixed, type?: mixed, mermaid?: mixed, src?: mixed}>
     */
    public static function forSlug(string $slug): array
    {
        $guide = Guides::load($slug);
        if ($guide === null || !isset($guide['diagrams']) || !is_array($guide['diagrams'])) {
            return [];
        }

        return self::indexById($guide['diagrams']);
    }

    /**
     * Resolve diagramId / diagramIds / inline diagram on a content node or quiz item.
     *
     * @param array<string, mixed> $node
     * @param array<string, array{id?: mixed, title?: mixed, svg?: mixed, type?: mixed, mermaid?: mixed, src?: mixed}> $byId
     */
    public static function figuresForNode(array $node, array $byId): string
    {
        $out = [];
        if (isset($node['diagram']) && is_array($node['diagram'])) {
            $out[] = self::renderFigure($node['diagram']);
        }
        $ids = [];
        if (isset($node['diagramId']) && is_string($node['diagramId']) && $node['diagramId'] !== '') {
            $ids[] = $node['diagramId'];
        }
        if (isset($node['diagramIds']) && is_array($node['diagramIds'])) {
            foreach ($node['diagramIds'] as $id) {
                if (is_string($id) && $id !== '') {
                    $ids[] = $id;
                }
            }
        }
        foreach (array_unique($ids) as $id) {
            if (!isset($byId[$id])) {
                continue;
            }
            $out[] = self::renderFigure($byId[$id]);
        }

        return implode("\n", $out);
    }

    /**
     * @param array<string, mixed> $node
     * @param array<string, array{id?: mixed, title?: mixed, svg?: mixed, type?: mixed, mermaid?: mixed, src?: mixed}> $byId
     * @return list<array{id?: mixed, title?: mixed, svg?: mixed, type?: mixed, mermaid?: mixed, src?: mixed}>
     */
    public static function diagramsReferencedByNode(array $node, array $byId): array
    {
        $list = [];
        if (isset($node['diagram']) && is_array($node['diagram'])) {
            $list[] = $node['diagram'];
        }
        $ids = [];
        if (isset($node['diagramId']) && is_string($node['diagramId']) && $node['diagramId'] !== '') {
            $ids[] = $node['diagramId'];
        }
        if (isset($node['diagramIds']) && is_array($node['diagramIds'])) {
            foreach ($node['diagramIds'] as $id) {
                if (is_string($id) && $id !== '') {
                    $ids[] = $id;
                }
            }
        }
        foreach (array_unique($ids) as $id) {
            if (isset($byId[$id])) {
                $list[] = $byId[$id];
            }
        }

        return $list;
    }
}
