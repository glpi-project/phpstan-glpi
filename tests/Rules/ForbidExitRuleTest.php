<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStanGlpi\Rules\ForbidExitRule;
use PHPStanGlpi\Services\GlpiVersionResolver;

/**
 * @extends RuleTestCase<ForbidExitRule>
 */
class ForbidExitRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ForbidExitRule(
            new GlpiVersionResolver('11.0.0')
        );
    }

    public function testDie(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidExitRule/die.php'], [
            [
                'You should not use the `die` function. It prevents the execution of post-request/post-command routines.',
                5,
            ],
        ]);
    }

    public function testDieWithArguments(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidExitRule/die-with-arguments.php'], [
            [
                'You should not use the `die` function. It prevents the execution of post-request/post-command routines.',
                7,
            ],
        ]);
    }

    public function testDieWithParenthesis(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidExitRule/die-with-parenthesis.php'], [
            [
                'You should not use the `die` function. It prevents the execution of post-request/post-command routines.',
                7,
            ],
        ]);
    }

    public function testExit(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidExitRule/exit.php'], [
            [
                'You should not use the `exit` function. It prevents the execution of post-request/post-command routines.',
                7,
            ],
        ]);
    }

    public function testExitWithArguments(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidExitRule/exit-with-arguments.php'], [
            [
                'You should not use the `exit` function. It prevents the execution of post-request/post-command routines.',
                6,
            ],
        ]);
    }

    public function testExitWithParenthesis(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidExitRule/exit-with-parenthesis.php'], [
            [
                'You should not use the `exit` function. It prevents the execution of post-request/post-command routines.',
                6,
            ],
        ]);
    }
}
