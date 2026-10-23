<?php

declare(strict_types=1);

namespace TheoryCore;

use RuntimeException;

/**
 * Loads the family catalog from content/families.json.
 * Join key is slug. SAMPLE / educational data only.
 */
final class Catalog
{
    private static ?array $data = null;

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

    /** @return array{families: list<array<string, mixed>>} */
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

        /** @var array{families?: list<array<string, mixed>>} $decoded */
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (!isset($decoded['families']) || !is_array($decoded['families'])) {
            throw new RuntimeException('content/families.json must contain a families array');
        }

        self::$data = $decoded;

        return self::$data;
    }
}
