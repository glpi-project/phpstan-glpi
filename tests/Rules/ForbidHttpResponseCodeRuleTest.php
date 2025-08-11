<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStanGlpi\Rules\ForbidHttpResponseCodeRule;
use PHPStanGlpi\Tests\TestTrait;

/**
 * @extends RuleTestCase<ForbidHttpResponseCodeRule>
 */
class ForbidHttpResponseCodeRuleTest extends RuleTestCase
{
    use TestTrait;

    protected function getRule(): Rule
    {
        return new ForbidHttpResponseCodeRule(
            $this->getGlpiVersionResolver('11.0.0')
        );
    }

    public function testGetter(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidHttpResponseCodeRule/getter.php'], [
            // getter should not result in an issue
        ]);
    }

    public function testSetter(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidHttpResponseCodeRule/setter.php'], [
            [
                'You should not use the `http_response_code` function to change the response code. Due to a PHP bug, it may not provide the expected result (see https://bugs.php.net/bug.php?id=81451).',
                5,
            ],
        ]);
    }
}
