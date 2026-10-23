<?php

declare(strict_types=1);

namespace TheoryCore;

use InvalidArgumentException;
use RuntimeException;

final class Cheatsheet
{
    /** @return array<string, mixed>|null */
    public static function load(string $slug): ?array
    {
        $path = dirname(__DIR__) . '/content/cheatsheets/' . $slug . '.json';
        if (!is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new RuntimeException('Unreadable cheatsheet');
        }
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        self::assertValid($decoded);

        return $decoded;
    }

    /** @param array<string, mixed> $sheet */
    public static function assertValid(array $sheet): void
    {
        $lines = $sheet['lines'] ?? null;
        if (!is_array($lines) || count($lines) < 1 || count($lines) > 12) {
            throw new InvalidArgumentException('cheatsheet needs 1–12 lines');
        }
        if (!isset($sheet['pickWhen'], $sheet['skipWhen']) || !is_string($sheet['pickWhen']) || !is_string($sheet['skipWhen'])) {
            throw new InvalidArgumentException('cheatsheet needs pickWhen and skipWhen');
        }
    }

    /** @return array{status: int, location?: string, body?: string} */
    public static function pageResponse(string $slug): array
    {
        if (Catalog::bySlug($slug) === null) {
            return ['status' => 302, 'location' => '/'];
        }
        $sheet = self::load($slug);
        if ($sheet === null) {
            return [
                'status' => 200,
                'body' => '<!DOCTYPE html><html lang="en"><body><main>'
                    . '<p><strong>SAMPLE</strong> data only.</p>'
                    . '<p class="slot">not authored yet</p></main></body></html>',
            ];
        }

        return ['status' => 200, 'body' => self::renderHtml($sheet)];
    }

    /** @param array<string, mixed> $sheet */
    public static function renderHtml(array $sheet): string
    {
        self::assertValid($sheet);
        $slug = (string) $sheet['slug'];
        $parts = ['<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">',
            '<title>Cheatsheet — ' . self::h($slug) . '</title></head><body>',
            '<main class="cheatsheet" data-slug="' . self::h($slug) . '">',
            '<p><strong>SAMPLE</strong> data only.</p>',
            '<h1>Cheatsheet: ' . self::h($slug) . '</h1>',
            '<ul class="lines">'];
        foreach ($sheet['lines'] as $line) {
            $parts[] = '<li>' . self::h((string) $line) . '</li>';
        }
        $parts[] = '</ul>';
        $parts[] = '<p class="pick-when">Pick when: ' . self::h((string) $sheet['pickWhen']) . '</p>';
        $parts[] = '<p class="skip-when">Skip when: ' . self::h((string) $sheet['skipWhen']) . '</p>';
        $parts[] = '</main></body></html>';

        return implode("\n", $parts);
    }

    private static function h(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
