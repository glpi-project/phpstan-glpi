<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\IgnoredGlpiVersion\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStanGlpi\Rules\ForbidDynamicInstantiationRule;
use PHPStanGlpi\Tests\IgnoredGlpiVersion\TestIgnoredRuleTrait;
use PHPStanGlpi\Tests\TestTrait;

/**
 * @extends RuleTestCase<ForbidDynamicInstantiationRule>
 */
class ForbidDynamicInstantiationRuleTest extends RuleTestCase
{
    use TestIgnoredRuleTrait;
    use TestTrait;

    protected function getRule(): Rule
    {
        return new ForbidDynamicInstantiationRule(
            $this->getGlpiVersionResolver('10.0.18'), // should be ignored in GLPI < 11.0.0
            false
        );
    }
}
