<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStanGlpi\Rules\ForbidNonLiteralSqlExpressionRule;
use PHPStanGlpi\Tests\TestTrait;

/**
 * Validates the activation of the rule, that is enforced since GLPI 13.0, but can be adopted
 * earlier thanks to the `glpi.enableEarlierRulesAdoption` parameter.
 *
 * @extends RuleTestCase<ForbidNonLiteralSqlExpressionRule>
 */
class ForbidNonLiteralSqlExpressionRuleAdoptionTest extends RuleTestCase
{
    use TestTrait;

    private const ERROR_MESSAGE = 'Building a Glpi\DBAL\QueryExpression from a non-literal SQL string is forbidden.'
        . ' Use `QueryIdentifier` for an identifier, `QueryValue` for a value,'
        . ' `QueryFunction` or `QuerySubQuery` for a SQL fragment,'
        . ' or pass the dynamic parts through the `values:` argument to have them bound'
        . ' as statement parameters.';

    private string $glpiVersion = '13.0.0';

    private bool $enableEarlierRulesAdoption = false;

    protected function getRule(): Rule
    {
        return new ForbidNonLiteralSqlExpressionRule(
            $this->getEarlierRulesAdoptionResolver($this->glpiVersion, $this->enableEarlierRulesAdoption),
            true
        );
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../data/query-element-stubs.neon',
        ];
    }

    public function testRuleIsNotAppliedOnGlpi12WithoutEarlierAdoption(): void
    {
        $this->glpiVersion = '12.0.0';
        $this->enableEarlierRulesAdoption = false;

        $this->analyse([__DIR__ . '/../data/ForbidNonLiteralSqlExpressionRule/dynamic-expression.php'], [
        ]);
    }

    public function testRuleIsAppliedOnGlpi12WithEarlierAdoption(): void
    {
        $this->glpiVersion = '12.0.0';
        $this->enableEarlierRulesAdoption = true;

        $this->analyse([__DIR__ . '/../data/ForbidNonLiteralSqlExpressionRule/dynamic-expression.php'], [
            [self::ERROR_MESSAGE, 10],
            [self::ERROR_MESSAGE, 13],
            [self::ERROR_MESSAGE, 16],
            [self::ERROR_MESSAGE, 19],
            [self::ERROR_MESSAGE, 22],
            [self::ERROR_MESSAGE, 25],
        ]);
    }

    public function testRuleIsAppliedOnGlpi13WithoutEarlierAdoption(): void
    {
        $this->glpiVersion = '13.0.0';
        $this->enableEarlierRulesAdoption = false;

        $this->analyse([__DIR__ . '/../data/ForbidNonLiteralSqlExpressionRule/dynamic-expression.php'], [
            [self::ERROR_MESSAGE, 10],
            [self::ERROR_MESSAGE, 13],
            [self::ERROR_MESSAGE, 16],
            [self::ERROR_MESSAGE, 19],
            [self::ERROR_MESSAGE, 22],
            [self::ERROR_MESSAGE, 25],
        ]);
    }
}
