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
}
