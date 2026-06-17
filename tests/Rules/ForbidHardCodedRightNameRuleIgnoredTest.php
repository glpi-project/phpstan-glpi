<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStanGlpi\Rules\ForbidHardCodedRightNameRule;
use PHPStanGlpi\Tests\TestTrait;

/**
 * @extends RuleTestCase<ForbidHardCodedRightNameRule>
 */
class ForbidHardCodedRightNameRuleIgnoredTest extends RuleTestCase
{
    use TestTrait;

    protected function getRule(): Rule
    {
        return new ForbidHardCodedRightNameRule(
            $this->getGlpiVersionResolver('11.0.0')
        );
    }

    public function testSessionMethods(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidHardCodedRightNameRule/sessionMethods.php'], []);
    }

}
