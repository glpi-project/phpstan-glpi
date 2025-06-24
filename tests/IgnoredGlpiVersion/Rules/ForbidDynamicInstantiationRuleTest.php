<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\IgnoredGlpiVersion\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStanGlpi\Rules\ForbidDynamicInstantiationRule;
use PHPStanGlpi\Services\GlpiVersionResolver;
use PHPStanGlpi\Tests\IgnoredGlpiVersion\TestIgnoredRuleTrait;

/**
 * @extends RuleTestCase<ForbidDynamicInstantiationRule>
 */
class ForbidDynamicInstantiationRuleTest extends RuleTestCase
{
    use TestIgnoredRuleTrait;

    protected function getRule(): Rule
    {
        return new ForbidDynamicInstantiationRule(
            new GlpiVersionResolver('10.0.18'), // should be ignored in GLPI < 11.0.0
            false
        );
    }
}
