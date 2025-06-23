<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\IgnoredGlpiVersion\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStanGlpi\Rules\ForbidHttpResponseCodeRule;
use PHPStanGlpi\Services\GlpiVersionResolver;
use PHPStanGlpi\Tests\IgnoredGlpiVersion\TestIgnoredRuleTrait;

/**
 * @extends RuleTestCase<ForbidHttpResponseCodeRule>
 */
class ForbidHttpResponseCodeRuleTest extends RuleTestCase
{
    use TestIgnoredRuleTrait;

    protected function getRule(): Rule
    {
        return new ForbidHttpResponseCodeRule(
            new GlpiVersionResolver('10.0.18') // should be ignored in GLPI < 11.0.0
        );
    }
}
