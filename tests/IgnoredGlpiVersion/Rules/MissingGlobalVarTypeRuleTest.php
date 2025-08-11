<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\IgnoredGlpiVersion\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStan\Type\FileTypeMapper;
use PHPStanGlpi\Rules\MissingGlobalVarTypeRule;
use PHPStanGlpi\Tests\IgnoredGlpiVersion\TestIgnoredRuleTrait;
use PHPStanGlpi\Tests\TestTrait;

/**
 * @extends RuleTestCase<MissingGlobalVarTypeRule>
 */
class MissingGlobalVarTypeRuleTest extends RuleTestCase
{
    use TestIgnoredRuleTrait;
    use TestTrait;

    protected function getRule(): Rule
    {
        return new MissingGlobalVarTypeRule(
            self::getContainer()->getByType(FileTypeMapper::class),
            $this->getGlobalTypeResolver('9.5.13', __DIR__ . '/../../data'),
            $this->getGlpiVersionResolver('9.5.13') // should be ignored in GLPI < 10.0.0
        );
    }
}
