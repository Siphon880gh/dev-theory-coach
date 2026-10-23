<?php

declare(strict_types=1);

namespace TheoryCore;

use InvalidArgumentException;

/**
 * Validates deterministic coaching session graphs.
 */
final class CoachValidator
{
    /**
     * @param array<string, mixed> $session
     * @return list<string> empty when valid
     */
    public static function errors(array $session): array
    {
        $errors = [];
        $meta = $session['meta'] ?? null;
        if (!is_array($meta) || !isset($meta['slug']) || !is_string($meta['slug']) || $meta['slug'] === '') {
            $errors[] = 'meta.slug is required';
        }
        if (!isset($session['start']) || !is_string($session['start']) || $session['start'] === '') {
            $errors[] = 'start node id is required';
        }
        $nodes = $session['nodes'] ?? null;
        if (!is_array($nodes) || $nodes === []) {
            $errors[] = 'nodes map is required';

            return $errors;
        }

        $ids = [];
        foreach ($nodes as $id => $node) {
            if (!is_string($id) || $id === '') {
                $errors[] = 'node id must be a non-empty string';
                continue;
            }
            $ids[$id] = true;
            if (!is_array($node)) {
                $errors[] = "node {$id} must be an object";
                continue;
            }
            $type = $node['type'] ?? null;
            if (!in_array($type, ['continue', 'wrong', 'success'], true)) {
                $errors[] = "node {$id} has invalid type";
            }
            $choices = $node['choices'] ?? [];
            if (!is_array($choices)) {
                $errors[] = "node {$id} choices must be an array";
                $choices = [];
            }
            if ($type === 'success' && $choices !== []) {
                $errors[] = "success node {$id} must have empty choices";
            }
            if ($type === 'wrong') {
                $rewind = $node['rewind_to'] ?? null;
                if (!is_string($rewind) || $rewind === '') {
                    $errors[] = "wrong node {$id} needs rewind_to";
                }
            }
            foreach ($choices as $i => $choice) {
                if (!is_array($choice) || !isset($choice['to']) || !is_string($choice['to'])) {
                    $errors[] = "node {$id} choice {$i} needs string to";
                }
            }
        }

        $start = $session['start'] ?? null;
        if (is_string($start) && $start !== '' && !isset($ids[$start])) {
            $errors[] = 'start references a missing node';
        }

        $hasSuccess = false;
        foreach ($nodes as $id => $node) {
            if (!is_array($node)) {
                continue;
            }
            if (($node['type'] ?? null) === 'success') {
                $hasSuccess = true;
            }
            if (($node['type'] ?? null) === 'wrong') {
                $rewind = $node['rewind_to'] ?? null;
                if (is_string($rewind) && $rewind !== '' && !isset($ids[$rewind])) {
                    $errors[] = "rewind_to on {$id} names missing node {$rewind}";
                }
            }
            foreach ($node['choices'] ?? [] as $choice) {
                if (is_array($choice) && isset($choice['to']) && is_string($choice['to']) && !isset($ids[$choice['to']])) {
                    $errors[] = "choice on {$id} targets missing node {$choice['to']}";
                }
            }
        }
        if (!$hasSuccess) {
            $errors[] = 'graph needs at least one success node';
        }

        return $errors;
    }

    /** @param array<string, mixed> $session */
    public static function assertValid(array $session): void
    {
        $errors = self::errors($session);
        if ($errors !== []) {
            throw new InvalidArgumentException(implode('; ', $errors));
        }
    }

    public static function loadFile(string $path): array
    {
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new InvalidArgumentException('Cannot read ' . $path);
        }
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        self::assertValid($decoded);

        return $decoded;
    }
}
