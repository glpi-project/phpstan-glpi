<?php

declare(strict_types=1);

namespace PHPStanGlpi\Tests\Rules;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPStanGlpi\Rules\ForbidNonLiteralSqlExpressionRule;
use PHPStanGlpi\Tests\TestTrait;

/**
 * @extends RuleTestCase<ForbidNonLiteralSqlExpressionRule>
 */
class ForbidNonLiteralSqlExpressionRulePhpDocAsCertainTest extends RuleTestCase
{
    use TestTrait;

    private const ERROR_MESSAGE = 'Building a Glpi\DBAL\QueryExpression from a non-literal SQL string is forbidden.'
        . ' Use `QueryIdentifier` for an identifier, `QueryValue` for a value,'
        . ' `QueryFunction` or `QuerySubQuery` for a SQL fragment,'
        . ' or pass the dynamic parts through the `values:` argument to have them bound'
        . ' as statement parameters.';

    private const IDENTIFIER_ERROR_MESSAGE = 'Building a Glpi\DBAL\QueryExpression from a bare SQL identifier is forbidden.'
        . ' Use `QueryIdentifier` instead.';

    private const UNPACKED_ERROR_MESSAGE = 'Building a Glpi\DBAL\QueryExpression from unpacked arguments is forbidden,'
        . ' as the SQL expression cannot be verified. Pass the `expression` argument explicitly.';

    protected function getRule(): Rule
    {
        return new ForbidNonLiteralSqlExpressionRule(
            $this->getEarlierRulesAdoptionResolver('13.0.0'),
            true
        );
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../data/query-element-stubs.neon',
        ];
    }

    public function testLiteralExpression(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidNonLiteralSqlExpressionRule/literal-expression.php'], [
        ]);
    }

    public function testDynamicExpression(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidNonLiteralSqlExpressionRule/dynamic-expression.php'], [
            [self::ERROR_MESSAGE, 10],
            [self::ERROR_MESSAGE, 13],
            [self::ERROR_MESSAGE, 16],
            [self::ERROR_MESSAGE, 19],
            [self::ERROR_MESSAGE, 22],
            [self::ERROR_MESSAGE, 25],
        ]);
    }

    public function testNamedArguments(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidNonLiteralSqlExpressionRule/named-arguments.php'], [
            [self::ERROR_MESSAGE, 13],
            [self::ERROR_MESSAGE, 14],
            [self::UNPACKED_ERROR_MESSAGE, 23],
            [self::ERROR_MESSAGE, 35],
        ]);
    }

    public function testQueryElements(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidNonLiteralSqlExpressionRule/query-elements.php'], [
            [self::ERROR_MESSAGE, 21],
        ]);
    }

    public function testIdentifierExpression(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidNonLiteralSqlExpressionRule/identifier-expression.php'], [
            [self::IDENTIFIER_ERROR_MESSAGE, 10],
            [self::IDENTIFIER_ERROR_MESSAGE, 11],
            [self::IDENTIFIER_ERROR_MESSAGE, 14],
            [self::IDENTIFIER_ERROR_MESSAGE, 15],
            [self::IDENTIFIER_ERROR_MESSAGE, 18],
            [self::IDENTIFIER_ERROR_MESSAGE, 21],
        ]);
    }

    public function testOtherClasses(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidNonLiteralSqlExpressionRule/other-classes.php'], [
        ]);
    }

    public function testPhpDocTypes(): void
    {
        $this->analyse([__DIR__ . '/../data/ForbidNonLiteralSqlExpressionRule/phpdoc-types.php'], [
            // a `string` type carries no guarantee, whether the PHPDoc is trusted or not
            [self::ERROR_MESSAGE, 19],
        ]);
    }
}
