<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\IgnoredGlpiVersion\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStanGlpi\Rules\ForbidHttpResponseCodeRule;
use PHPStanGlpi\Tests\IgnoredGlpiVersion\TestIgnoredRuleTrait;
use PHPStanGlpi\Tests\TestTrait;

/**
 * @extends RuleTestCase<ForbidHttpResponseCodeRule>
 */
class ForbidHttpResponseCodeRuleTest extends RuleTestCase
{
    use TestIgnoredRuleTrait;
    use TestTrait;

    protected function getRule(): Rule
    {
        return new ForbidHttpResponseCodeRule(
            $this->getGlpiVersionResolver('10.0.18') // should be ignored in GLPI < 11.0.0
        );
    }
}
