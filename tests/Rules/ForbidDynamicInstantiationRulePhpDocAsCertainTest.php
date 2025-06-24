<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStanGlpi\Services\GlpiVersionResolver;
use PHPStanGlpi\Rules\ForbidDynamicInstantiationRule;

/**
 * @extends RuleTestCase<ForbidDynamicInstantiationRule>
 */
class ForbidDynamicInstantiationRulePhpDocAsCertainTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ForbidDynamicInstantiationRule(
            new GlpiVersionResolver('11.0.0'),
            true
        );
    }

    public function testAnonymousClass(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidDynamicInstantiationRule/anonymous-class.php'], [
        ]);
    }

    public function testContantValues(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidDynamicInstantiationRule/constant-values.php'], [
        ]);
    }

    public function testInstanceof(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidDynamicInstantiationRule/instanceof.php'], [
        ]);
    }

    public function testIsA(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidDynamicInstantiationRule/is-a.php'], [
        ]);
    }

    public function testIsSubclassOf(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidDynamicInstantiationRule/is-subclass-of.php'], [
        ]);
    }

    public function testMixedType(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidDynamicInstantiationRule/mixed-type.php'], [
            [
                'Instantiating an object from an unrestricted dynamic string is forbidden (see https://github.com/glpi-project/phpstan-glpi?tab=readme-ov-file#forbiddynamicinstantiationrule).',
                11,
            ],
        ]);
    }


    public function testPhpDocClassString(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidDynamicInstantiationRule/phpdoc-class-string.php'], [
            [
                'Instantiating an object from an unrestricted dynamic string is forbidden (see https://github.com/glpi-project/phpstan-glpi?tab=readme-ov-file#forbiddynamicinstantiationrule).',
                7,
            ],
        ]);
    }

    public function testPhpDocSpecificClass(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidDynamicInstantiationRule/phpdoc-specific-class.php'], [
        ]);
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testUnionType(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidDynamicInstantiationRule/union-type.php'], [
            [
                'Instantiating an object from an unrestricted dynamic string is forbidden (see https://github.com/glpi-project/phpstan-glpi?tab=readme-ov-file#forbiddynamicinstantiationrule).',
                19,
            ],
        ]);
    }
}
