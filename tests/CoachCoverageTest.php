<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\Catalog;
use TheoryCore\Coach;
use TheoryCore\CoachValidator;

final class CoachCoverageTest extends TestCase
{
    protected function setUp(): void
    {
        Catalog::reset();
    }

    public function testEveryFamilyHasValidCoachSession(): void
    {
        foreach (Catalog::slugs() as $slug) {
            $session = Coach::load($slug);
            $this->assertNotNull($session, $slug);
            $this->assertSame([], CoachValidator::errors($session), $slug);
            $this->assertSame($slug, $session['meta']['slug']);
            $state = Coach::start($session);
            // find a path: prefer first choice repeatedly until done or cap
            $guard = 0;
            while (!$state['done'] && $guard < 20) {
                $node = $session['nodes'][$state['current']];
                if (($node['choices'] ?? []) === []) {
                    break;
                }
                $state = Coach::choose($session, $state, 0);
                $guard++;
            }
            $this->assertTrue($state['done'], "could not reach success for {$slug}");
        }
    }

    public function testNoStage0ForceTemplateJunkInCoachingFiles(): void
    {
        $dir = dirname(__DIR__) . '/content/coaching';
        $banned = [
            'prerequisite slug',
            'That skips the force.',
            'when the force is absent',
            'covers the force or a prerequisite',
            'When should you refuse',
            'microservice theater',
            'sharding theater',
            'trading cards',
            'fancy hat',
        ];
        $files = glob($dir . '/*.json');
        $this->assertNotEmpty($files);
        $explanations = [];
        foreach ($files as $path) {
            $raw = file_get_contents($path);
            $this->assertNotFalse($raw, $path);
            foreach ($banned as $needle) {
                $this->assertStringNotContainsString(
                    $needle,
                    $raw,
                    basename($path) . ' still contains Stage-0 template junk'
                );
            }
            $session = json_decode($raw, true);
            $this->assertIsArray($session, basename($path));
            foreach ($session['nodes'] ?? [] as $node) {
                foreach (['prompt', 'explanation'] as $field) {
                    $copy = $node[$field] ?? '';
                    if (is_string($copy)) {
                        $this->assertDoesNotMatchRegularExpression(
                            '/\\brefuse(?:d)?\\b/i',
                            $copy,
                            basename($path) . " uses formulaic {$field} language",
                        );
                    }
                }
                foreach ($node['choices'] ?? [] as $choice) {
                    $label = is_array($choice) ? ($choice['label'] ?? '') : '';
                    if (is_string($label)) {
                        $this->assertDoesNotMatchRegularExpression(
                            '/\\brefuse(?:d)?\\b/i',
                            $label,
                            basename($path) . ' uses formulaic choice language',
                        );
                    }
                }
                if (($node['type'] ?? '') !== 'wrong') {
                    continue;
                }
                $expl = $node['explanation'] ?? '';
                $this->assertIsString($expl);
                $this->assertDoesNotMatchRegularExpression(
                    '/^Refuse .+ when the force is absent/i',
                    $expl,
                    basename($path)
                );
                if ($expl !== '') {
                    $explanations[$expl][] = basename($path);
                }
            }
        }
        foreach ($explanations as $expl => $filesFor) {
            $this->assertCount(
                1,
                $filesFor,
                'Identical wrong explanation shared across coaching files: ' . $expl
            );
        }
    }

    public function testAppliedCoachesUseThreeDecisionSteps(): void
    {
        $applied = [
            'adapter',
            'api-and-boundaries',
            'builder',
            'caching',
            'capstone-feed',
            'capstone-url-shortener',
            'command',
            'composite',
            'composition',
            'data-model',
            'decorator',
            'estimates',
            'facade',
            'factory',
            'failure',
            'interview-frame',
            'observer',
            'proxy',
            'rate-limiting',
            'read-and-write-paths',
            'replication-and-consistency',
            'requirements',
            'sharding',
            'singleton',
            'state',
            'storage-and-indexes',
            'strategy',
            'when-to-name-a-pattern',
            'work-queues',
        ];

        foreach ($applied as $slug) {
            $session = Coach::load($slug);
            $this->assertNotNull($session, $slug);
            $continue = array_filter(
                $session['nodes'],
                fn ($node) => is_array($node) && ($node['type'] ?? '') === 'continue',
            );
            $wrong = array_filter(
                $session['nodes'],
                fn ($node) => is_array($node) && ($node['type'] ?? '') === 'wrong',
            );
            $this->assertGreaterThanOrEqual(3, count($continue), "{$slug} needs three applied decisions");
            $this->assertGreaterThanOrEqual(3, count($wrong), "{$slug} needs feedback at every decision");
        }
    }
}
