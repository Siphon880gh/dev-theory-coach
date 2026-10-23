<?php

declare(strict_types=1);

namespace TheoryCore;

use RuntimeException;

/**
 * Learning path loader (beginner first).
 */
final class Path
{
    /**
     * @return array{id: string, milestones: list<array<string, mixed>>, caseGate: array<string, mixed>}
     */
    public static function load(string $pathId): array
    {
        $file = dirname(__DIR__) . '/content/paths/' . $pathId . '.json';
        if (!is_file($file)) {
            throw new RuntimeException('Missing path ' . $pathId);
        }
        $raw = file_get_contents($file);
        if ($raw === false) {
            throw new RuntimeException('Unreadable path ' . $pathId);
        }
        /** @var array{id: string, milestones: list<array<string, mixed>>, caseGate: array<string, mixed>} $decoded */
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (($decoded['id'] ?? null) !== $pathId) {
            throw new RuntimeException('Path id mismatch');
        }

        return $decoded;
    }

    /**
     * Resolve a contentRef to a file path or a slot route marker.
     * Returns ['kind'=>'file','path'=>...] or ['kind'=>'slot','route'=>...] or ['kind'=>'missing'].
     *
     * @return array{kind: string, path?: string, route?: string}
     */
    public static function resolveRef(string $ref): array
    {
        if (str_starts_with($ref, 'guide:')) {
            $slug = substr($ref, 6);
            $p = dirname(__DIR__) . '/content/guides/' . $slug . '.json';

            return is_file($p)
                ? ['kind' => 'file', 'path' => $p]
                : ['kind' => 'missing'];
        }
        if (str_starts_with($ref, 'coach:')) {
            $slug = substr($ref, 6);
            $p = dirname(__DIR__) . '/content/coaching/' . $slug . '.json';
            if (is_file($p)) {
                return ['kind' => 'file', 'path' => $p];
            }

            return ['kind' => 'slot', 'route' => '/coach/' . $slug];
        }
        if (str_starts_with($ref, 'quiz:')) {
            $slug = substr($ref, 5);
            $p = dirname(__DIR__) . '/content/quizzes/' . $slug . '.json';
            if (is_file($p)) {
                return ['kind' => 'file', 'path' => $p];
            }

            return ['kind' => 'slot', 'route' => '/quiz/' . $slug];
        }

        return ['kind' => 'missing'];
    }
}
