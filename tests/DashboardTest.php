<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\Dashboard;
use TheoryCore\ProgressSchema;

final class DashboardTest extends TestCase
{
    public function testFirstRunCtaNamesArrays(): void
    {
        $html = Dashboard::homeHtml(ProgressSchema::fresh());
        $this->assertStringContainsString('css/app.css', $html);
        $this->assertStringContainsString('rel="stylesheet"', $html);
        $this->assertStringContainsString('/guides/arrays', $html);
        $this->assertStringContainsString('arrays', $html);
        $this->assertStringNotContainsString('%', $html);
        $this->assertStringContainsString('Case gate locked', $html);
        $this->assertStringContainsString('Dev Theory Coach', $html);
        $this->assertStringNotContainsString('THEORY_CORE', $html);
        $this->assertStringContainsString('site-header', $html);
        $this->assertStringContainsString('site-nav', $html);
        $this->assertStringContainsString('nav-link is-active', $html);
        $this->assertStringContainsString('Next: study arrays', $html);
        $this->assertStringContainsString('theme-switch', $html);
        $this->assertStringContainsString('weng-theme', $html);
        $this->assertStringContainsString('data-theme', $html);
        $this->assertStringContainsString('js/theme.js', $html);
    }

    public function testAfterArraysGuideReadCtaMovesForward(): void
    {
        $state = ProgressSchema::recordGuideRead(ProgressSchema::fresh(), 'arrays');
        $html = Dashboard::homeHtml($state);
        $this->assertStringContainsString('/guides/strings', $html);
        $this->assertStringNotContainsString('/guides/arrays', $html);
    }

    public function testPathCompleteAndGateOpenCopy(): void
    {
        $state = ProgressSchema::fresh();
        foreach (ProgressSchema::BEGINNER_LEARNING_ORDER as $slug) {
            $state = ProgressSchema::recordGuideRead($state, $slug);
            $state = ProgressSchema::markMilestoneComplete($state, 'beginner:' . $slug);
        }
        $state = ProgressSchema::recordQuizResult($state, 'arrays', 5, 6);
        // force >= 0.8
        $state['milestones']['beginner:arrays']['quizAccuracy'] = 0.8;
        $state = ProgressSchema::recordCoachComplete($state, 'arrays');
        foreach (['arrays', 'hash-maps', 'queues'] as $slug) {
            $state['milestones']['beginner:' . $slug]['quizAccuracy'] = 0.8;
            $state['milestones']['beginner:' . $slug]['quizSubmitted'] = true;
        }
        $state = ProgressSchema::refreshBeginnerLocks($state);

        $this->assertTrue(Dashboard::isPathComplete($state));
        $html = Dashboard::homeHtml($state);
        $this->assertStringContainsString('Beginner path complete', $html);
        $this->assertStringContainsString('Case gate open', $html);
        $this->assertStringNotContainsString('next-cta', $html);

        $incomplete = ProgressSchema::fresh();
        $this->assertFalse(Dashboard::isPathComplete($incomplete));
        $html2 = Dashboard::homeHtml($incomplete);
        $this->assertStringContainsString('Case gate locked', $html2);
    }
}
