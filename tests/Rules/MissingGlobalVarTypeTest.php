<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStan\Type\FileTypeMapper;
use PHPStanGlpi\Rules\MissingGlobalVarTypeRule;
use PHPStanGlpi\Services\GlpiVersionResolver;

/**
 * @extends RuleTestCase<MissingGlobalVarTypeRule>
 */
class MissingGlobalVarTypeTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new MissingGlobalVarTypeRule(
            self::getContainer()->getByType(FileTypeMapper::class),
            new GlpiVersionResolver('10.0.0')
        );
    }

    public function testInClassMethod(): void
    {
        $this->analyse([__DIR__ . '/../data/MissingGlobalVarTypeRule/in-class-method.php'], [
            [
                'Missing PHPDoc tag @var for global variable $CFG_GLPI',
                9,
            ],
            [
                'Missing PHPDoc tag @var for global variable $GLPI_CACHE',
                9,
            ],
        ]);
    }

    public function testInFunction(): void
    {
        $this->analyse([__DIR__ . '/../data/MissingGlobalVarTypeRule/in-function.php'], [
            [
                'Missing PHPDoc tag @var for global variable $DB',
                7,
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
