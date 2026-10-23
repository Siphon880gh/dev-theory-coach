<?php

declare(strict_types=1);

namespace TheoryCore;

/**
 * Canonical progress schema for theory_core_progress_v1.
 * assets/js/progress.js must mirror these fields and the storage key.
 */
final class ProgressSchema
{
    public const KEY = 'theory_core_progress_v1';
    public const VERSION = 1;
    public const PATH_BEGINNER = 'beginner';

    /** @var list<string> */
    public const BEGINNER_LEARNING_ORDER = [
        'arrays',
        'strings',
        'hash-maps',
        'linked-lists',
        'stacks',
        'queues',
        'complexity',
        'recursion',
        'two-pointers',
        'binary-search',
        'trees',
        'sliding-window',
    ];

    /**
     * @return list<string> e.g. beginner:arrays … beginner:sliding-window
     */
    public static function beginnerMilestoneIds(): array
    {
        $ids = [];
        foreach (self::BEGINNER_LEARNING_ORDER as $slug) {
            $ids[] = self::PATH_BEGINNER . ':' . $slug;
        }

        return $ids;
    }

    /**
     * First-run / reset state. recovery is false unless decode recovered from corruption.
     *
     * @return array{
     *   version: int,
     *   pathId: string,
     *   milestones: array<string, array<string, mixed>>,
     *   scores: array<string, mixed>,
     *   latencies: array<string, mixed>,
     *   caseGateOpen: bool,
     *   recovery: bool
     * }
     */
    public static function fresh(bool $recovery = false): array
    {
        $milestones = [];
        foreach (self::beginnerMilestoneIds() as $i => $id) {
            $milestones[$id] = [
                'status' => $i === 0 ? 'unlocked' : 'locked',
                'guideRead' => false,
                'coachComplete' => false,
                'quizSubmitted' => false,
                'quizScore' => null,
                'quizAccuracy' => null,
                'drillAccuracy' => null,
                'drillMedianLatencyMs' => null,
                'caseChoiceId' => null,
                'caseCorrect' => null,
            ];
        }

        return [
            'version' => self::VERSION,
            'pathId' => self::PATH_BEGINNER,
            'milestones' => $milestones,
            'scores' => [],
            'latencies' => [],
            'caseGateOpen' => false,
            'recovery' => $recovery,
        ];
    }

    /**
     * Decode a JSON string from storage. Truncated / invalid JSON → fresh + recovery.
     *
     * @return array<string, mixed>
     */
    public static function decode(?string $raw): array
    {
        if ($raw === null || $raw === '') {
            return self::fresh(false);
        }

        try {
            /** @var mixed $decoded */
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return self::fresh(true);
        }

        if (!is_array($decoded)) {
            return self::fresh(true);
        }

        return self::normalize($decoded);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function normalize(array $data): array
    {
        $base = self::fresh(false);
        $version = $data['version'] ?? null;
        if ($version !== self::VERSION) {
            // Unknown / missing version → treat as corrupt recovery to known schema.
            $recovered = self::fresh(true);
            // Keep pathId if it looks sane.
            if (isset($data['pathId']) && is_string($data['pathId']) && $data['pathId'] !== '') {
                $recovered['pathId'] = $data['pathId'];
            }

            return $recovered;
        }

        $out = $base;
        if (isset($data['pathId']) && is_string($data['pathId'])) {
            $out['pathId'] = $data['pathId'];
        }
        if (isset($data['caseGateOpen'])) {
            $out['caseGateOpen'] = (bool) $data['caseGateOpen'];
        }
        if (isset($data['recovery'])) {
            $out['recovery'] = (bool) $data['recovery'];
        }
        if (isset($data['scores']) && is_array($data['scores'])) {
            $out['scores'] = $data['scores'];
        }
        if (isset($data['latencies']) && is_array($data['latencies'])) {
            $out['latencies'] = $data['latencies'];
        }
        if (isset($data['milestones']) && is_array($data['milestones'])) {
            foreach ($out['milestones'] as $id => $row) {
                if (!isset($data['milestones'][$id]) || !is_array($data['milestones'][$id])) {
                    continue;
                }
                $src = $data['milestones'][$id];
                foreach ($row as $k => $default) {
                    if (array_key_exists($k, $src)) {
                        $out['milestones'][$id][$k] = $src[$k];
                    }
                }
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function encode(array $data): string
    {
        $normalized = self::normalize($data);
        // Persisted copy should not force recovery true unless caller set it on corrupt path.
        return json_encode($normalized, JSON_THROW_ON_ERROR);
    }

    /**
     * Round-trip helper for tests: encode then decode.
     *
     * @param array<string, mixed> $fixture
     * @return array<string, mixed>
     */
    public static function roundTrip(array $fixture): array
    {
        return self::decode(self::encode($fixture));
    }
}
