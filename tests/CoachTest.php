<?php

declare(strict_types=1);

namespace TheoryCore\Tests;

use PHPUnit\Framework\TestCase;
use TheoryCore\Catalog;
use TheoryCore\Coach;
use TheoryCore\CoachValidator;

final class CoachTest extends TestCase
{
    protected function setUp(): void
    {
        Catalog::reset();
    }

    public function testValidatorRejectsMissingSuccessRewindAndSuccessChoices(): void
    {
        $base = [
            'meta' => ['slug' => 'demo'],
            'start' => 'a',
            'nodes' => [
                'a' => [
                    'type' => 'continue',
                    'prompt' => 'x',
                    'choices' => [['label' => 'go', 'to' => 'b']],
                ],
                'b' => [
                    'type' => 'wrong',
                    'prompt' => 'no',
                    'explanation' => 'nope',
                    'rewind_to' => 'missing',
                    'choices' => [],
                ],
            ],
        ];
        $errors = CoachValidator::errors($base);
        $this->assertNotEmpty($errors);
        $joined = implode(' ', $errors);
        $this->assertStringContainsString('success', $joined);
        $this->assertStringContainsString('missing', $joined);

        $withSuccessChoices = [
            'meta' => ['slug' => 'demo'],
            'start' => 's',
            'nodes' => [
                's' => [
                    'type' => 'success',
                    'prompt' => 'done',
                    'choices' => [['label' => 'extra', 'to' => 's']],
                ],
            ],
        ];
        $errors2 = CoachValidator::errors($withSuccessChoices);
        $this->assertTrue(
            (bool) array_filter($errors2, fn ($e) => str_contains($e, 'empty choices')),
        );
    }

    public function testNavEngineWrongRewindThenSuccess(): void
    {
        $session = Coach::load('arrays');
        $this->assertNotNull($session);
        $state = Coach::start($session);
        $this->assertSame('q1', $state['current']);

        // choose linked list (wrong) -> rewind to q1
        $state = Coach::choose($session, $state, 1);
        $this->assertSame('q1', $state['current']);
        $this->assertNotEmpty($state['explanations']);
        $this->assertContains('wrong_list_for_index', $state['history']);

        // choose array -> q2
        $state = Coach::choose($session, $state, 0);
        $this->assertSame('q2', $state['current']);

        // wrong splice choice -> rewind q2
        $state = Coach::choose($session, $state, 1);
        $this->assertSame('q2', $state['current']);

        // correct list splice -> success
        $state = Coach::choose($session, $state, 0);
        $this->assertSame('success', $state['current']);
        $this->assertTrue($state['done']);
    }

    public function testCoachStringsIsSlotOrAuthoredNot404(): void
    {
        $response = Coach::pageResponse('strings');
        $this->assertSame(200, $response['status']);
        $body = $response['body'] ?? '';
        // Slot when unauthored; real coach page once content/coaching/strings.json exists.
        $this->assertTrue(
            str_contains($body, 'not authored yet') || str_contains($body, 'data-slug="strings"'),
            'Expected slot or authored coach page for strings'
        );
    }

    public function testArraysCoachValidatesAndMatchesGuideCta(): void
    {
        $session = Coach::load('arrays');
        $this->assertNotNull($session);
        $this->assertSame([], CoachValidator::errors($session));
        $this->assertSame('arrays', $session['meta']['slug']);

        $wrong = 0;
        $success = 0;
        foreach ($session['nodes'] as $node) {
            if (($node['type'] ?? '') === 'wrong') {
                $wrong++;
                $this->assertNotSame('', $node['rewind_to'] ?? '');
            }
            if (($node['type'] ?? '') === 'success') {
                $success++;
                $this->assertSame([], $node['choices']);
            }
        }
        $this->assertGreaterThanOrEqual(2, $wrong);
        $this->assertSame(1, $success);

        $guide = \TheoryCore\Guides::pageResponse('arrays');
        $this->assertStringContainsString('/coach/arrays', $guide['body'] ?? '');

        $page = Coach::pageResponse('arrays');
        $this->assertSame(200, $page['status']);
        $this->assertStringContainsString('index', strtolower($page['body'] ?? ''));
    }

    public function testAddCoachingSessionSkillHasRealPaths(): void
    {
        $skill = dirname(__DIR__) . '/.agents/skills/add-coaching-session/SKILL.md';
        $this->assertFileExists($skill);
        $raw = file_get_contents($skill);
        $this->assertNotFalse($raw);
        $this->assertStringNotContainsString('TODO', $raw);
        $this->assertStringContainsString('content/coaching/', $raw);
        $this->assertStringContainsString('CoachValidator', $raw);
        $this->assertFileExists(dirname(__DIR__) . '/content/coaching/arrays.json');
        $this->assertFileExists(dirname(__DIR__) . '/src/CoachValidator.php');
    }

    public function testCoachPageEmbedsSessionJsonAndScripts(): void
    {
        $page = Coach::pageResponse('arrays');
        $this->assertSame(200, $page['status']);
        $body = $page['body'] ?? '';
        $this->assertStringContainsString('id="coach-session-data"', $body);
        $this->assertStringContainsString('type="application/json"', $body);
        $this->assertStringContainsString('js/coach.js', $body);
        $this->assertStringContainsString('js/progress.js', $body);
        $this->assertStringContainsString('data-choice-index=', $body);
        $this->assertStringContainsString('coach-live', $body);
    }
}
