<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStan\Type\FileTypeMapper;
use PHPStanGlpi\Rules\MissingGlobalVarTypeRule;
use PHPStanGlpi\Tests\TestTrait;

/**
 * @extends RuleTestCase<MissingGlobalVarTypeRule>
 */
class MissingGlobalVarTypeRuleTest extends RuleTestCase
{
    use TestTrait;

    protected function getRule(): Rule
    {
        return new MissingGlobalVarTypeRule(
            self::getContainer()->getByType(FileTypeMapper::class),
            $this->getGlobalTypeResolver('11.0.0', __DIR__ . '/../data'),
            $this->getGlpiVersionResolver('11.0.0')
        );
    }

    public function testInClassMethod(): void
    {
        $this->analyse([__DIR__ . '/../data/MissingGlobalVarTypeRule/in-class-method.php'], [
            [
                'Missing PHPDoc tag @var for global variable $undocumented',
                16,
            ],
        ]);
    }

    public function testInFunction(): void
    {
        $this->analyse([__DIR__ . '/../data/MissingGlobalVarTypeRule/in-function.php'], [
            [
                'Missing PHPDoc tag @var for global variable $CFGGLPI',
                9,
            ],
        ]);
    }

    public function testInScript(): void
    {
        $this->analyse([__DIR__ . '/../data/MissingGlobalVarTypeRule/in-script.php'], [
            [
                'Missing PHPDoc tag @var for global variable $test',
                5,
            ],
        ]);
    }
}
