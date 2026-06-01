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
                'For security reason, GLPI recommends to disable the `exec`  function. Therefore, its usage may be blocked in most GLPI instances and you should not use it.',
                6,
            ],
            [
                'For security reason, GLPI recommends to disable the `shell_exec`  function. Therefore, its usage may be blocked in most GLPI instances and you should not use it.',
                7,
            ],
            [
                'For security reason, GLPI recommends to disable the `system`  function. Therefore, its usage may be blocked in most GLPI instances and you should not use it.',
                8,
            ],
            [
                'For security reason, GLPI recommends to disable the `passthru`  function. Therefore, its usage may be blocked in most GLPI instances and you should not use it.',
                9,
            ],
            [
                'For security reason, GLPI recommends to disable the `popen`  function. Therefore, its usage may be blocked in most GLPI instances and you should not use it.',
                10,
            ],
            [
                'For security reason, GLPI recommends to disable the `proc_open`  function. Therefore, its usage may be blocked in most GLPI instances and you should not use it.',
                11,
            ],
            [
                'For security reason, GLPI recommends to disable the `pcntl_fork`  function. Therefore, its usage may be blocked in most GLPI instances and you should not use it.',
                12,
            ],
            [
                'For security reason, GLPI recommends to disable the `posix_kill`  function. Therefore, its usage may be blocked in most GLPI instances and you should not use it.',
                13,
            ],
            [
                'For security reason, GLPI recommends to disable the `dl`  function. Therefore, its usage may be blocked in most GLPI instances and you should not use it.',
                14,
            ],
            [
                'For security reason, GLPI recommends to disable the `link`  function. Therefore, its usage may be blocked in most GLPI instances and you should not use it.',
                15,
            ],
            [
                'For security reason, GLPI recommends to disable the `symlink`  function. Therefore, its usage may be blocked in most GLPI instances and you should not use it.',
                16,
            ],
        ]);
    }
}
