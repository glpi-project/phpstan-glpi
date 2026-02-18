<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStanGlpi\Rules\ForbidDangerousSystemFunctionRule;
use PHPStanGlpi\Tests\TestTrait;

/**
 * @extends RuleTestCase<ForbidDangerousSystemFunctionRule>
 */
class ForbidDangerousSystemFunctionRuleTest extends RuleTestCase
{
    use TestTrait;

    protected function getRule(): Rule
    {
        return new ForbidDangerousSystemFunctionRule(
            $this->getGlpiVersionResolver('11.0.0')
        );
    }

    public function testRule(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidDangerousSystemFunctionRule/test.php'], [
            [
                'You should not use the `exec` function to avoid any dangerous system function call.',
                6,
            ],
            [
                'You should not use the `shell_exec` function to avoid any dangerous system function call.',
                7,
            ],
            [
                'You should not use the `system` function to avoid any dangerous system function call.',
                8,
            ],
            [
                'You should not use the `passthru` function to avoid any dangerous system function call.',
                9,
            ],
            [
                'You should not use the `popen` function to avoid any dangerous system function call.',
                10,
            ],
            [
                'You should not use the `proc_open` function to avoid any dangerous system function call.',
                11,
            ],
            [
                'You should not use the `pcntl_fork` function to avoid any dangerous system function call.',
                12,
            ],
            [
                'You should not use the `posix_kill` function to avoid any dangerous system function call.',
                13,
            ],
            [
                'You should not use the `dl` function to avoid any dangerous system function call.',
                14,
            ],
            [
                'You should not use the `link` function to avoid any dangerous system function call.',
                15,
            ],
            [
                'You should not use the `symlink` function to avoid any dangerous system function call.',
                16,
            ],
        ]);
    }
}
