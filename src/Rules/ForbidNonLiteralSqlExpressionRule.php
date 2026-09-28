<?php

declare(strict_types=1);

namespace PHPStanGlpi\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use PHPStanGlpi\Services\EarlierRulesAdoptionResolver;

/**
 * Forbids building a raw SQL fragment from anything but a literal string.
 *
 * `QueryExpression` is the only escape hatch that injects verbatim SQL into a query. It is
 * legitimate for a hardcoded fragment, but as soon as the fragment is assembled at runtime
 * the safety of the whole statement depends on the caller, and nothing can verify it.
 *
 * Identifiers belong in `QueryIdentifier`, values in `QueryValue` (or in the `values:`
 * argument, bound as statement parameters), and SQL fragments in `QueryFunction` /
 * `QuerySubQuery`.
 *
 * @implements Rule<New_>
 */
final class ForbidNonLiteralSqlExpressionRule implements Rule
{
    /**
     * Version that made the recommended alternatives available.
     */
    private const AVAILABLE_SINCE = '12.0.0-dev';

    /**
     * Version from which the rule is unconditionally applied.
     */
    private const ENFORCED_SINCE = '13.0.0-dev';

    /**
     * Classes whose first constructor argument is raw SQL, mapped to that argument name.
     *
     * @var array<string, string>
     */
    private const RAW_SQL_CLASSES = [
        'Glpi\DBAL\QueryExpression' => 'expression',
    ];

    /**
     * Interface implemented by the query elements that render their own SQL.
     */
    private const QUERY_ELEMENT_INTERFACE = 'Glpi\DBAL\QueryElementInterface';

    /**
     * A SQL fragment that is nothing but a reference to a column, optionally prefixed by its
     * table, e.g. `` `id` ``, `id`, `` `glpi_tickets`.`id` ``, `glpi_tickets.id`
     * or `` `glpi_tickets`.* ``.
     */
    private const IDENTIFIER_PATTERN
        = '/^\s*(?:`[^`]+`|[a-z_][a-z0-9_$]*)(?:\.(?:`[^`]+`|[a-z_][a-z0-9_$]*|\*))?\s*$/i';

    /**
     * SQL keywords that match the identifier pattern but are values, not identifiers.
     *
     * @var list<string>
     */
    private const RESERVED_LITERALS = [
        'CURRENT_DATE',
        'CURRENT_TIME',
        'CURRENT_TIMESTAMP',
        'CURRENT_USER',
        'DEFAULT',
        'DUAL', // the MySQL pseudo-table, that must not be quoted
        'FALSE',
        'LOCALTIME',
        'LOCALTIMESTAMP',
        'NULL',
        'TRUE',
        'UNKNOWN',
        'UTC_DATE',
        'UTC_TIME',
        'UTC_TIMESTAMP',
    ];

    private EarlierRulesAdoptionResolver $earlierRulesAdoptionResolver;

    private bool $treatPhpDocTypesAsCertain;

    public function __construct(
        EarlierRulesAdoptionResolver $earlierRulesAdoptionResolver,
        bool $treatPhpDocTypesAsCertain
    ) {
        $this->earlierRulesAdoptionResolver = $earlierRulesAdoptionResolver;
        $this->treatPhpDocTypesAsCertain = $treatPhpDocTypesAsCertain;
    }

    public function getNodeType(): string
    {
        return New_::class;
    }

    /**
     * @param New_ $node
     * @param Scope $scope
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$this->earlierRulesAdoptionResolver->isRuleEnabled(self::ENFORCED_SINCE, self::AVAILABLE_SINCE)) {
            return [];
        }

        if (!($node->class instanceof Name)) {
            // Only direct instanciations (e.g. `new QueryExpression()`) are handled here.
            return [];
        }

        $class_name = $scope->resolveName($node->class);
        $arg_name = self::RAW_SQL_CLASSES[$class_name] ?? null;
        if ($arg_name === null) {
            return [];
        }

        $arg = $this->getArgument($node->args, $arg_name);
        if ($arg === null) {
            if (!$this->hasUnpackedArguments($node->args)) {
                // The argument is simply not provided: PHPStan reports the missing argument itself.
                return [];
            }

            // The argument is provided, but cannot be located, so its safety cannot be verified.
            // Reporting it is preferred over staying silent: an explicit ignore is better than an
            // unreported potential issue.
            return [
                RuleErrorBuilder::message(
                    \sprintf(
                        'Building a %s from unpacked arguments is forbidden, as the SQL expression'
                        . ' cannot be verified. Pass the `%s` argument explicitly.',
                        $class_name,
                        $arg_name
                    )
                )
                    ->identifier('glpi.forbidNonLiteralSqlExpression')
                    ->line($node->getStartLine())
                    ->build(),
            ];
        }

        $type = $this->treatPhpDocTypesAsCertain
            ? $scope->getType($arg->value)
            : $scope->getNativeType($arg->value);

        if (!$this->isTypeSafe($type)) {
            return [
                RuleErrorBuilder::message(
                    \sprintf(
                        'Building a %s from a non-literal SQL string is forbidden.'
                        . ' Use `QueryIdentifier` for an identifier, `QueryValue` for a value,'
                        . ' `QueryFunction` or `QuerySubQuery` for a SQL fragment,'
                        . ' or pass the dynamic parts through the `values:` argument to have them bound'
                        . ' as statement parameters.',
                        $class_name
                    )
                )
                    ->identifier('glpi.forbidNonLiteralSqlExpression')
                    ->line($arg->getStartLine())
                    ->build(),
            ];
        }

        foreach ($type->getConstantStrings() as $constant_string) {
            if ($this->isBareIdentifier($constant_string->getValue())) {
                return [
                    RuleErrorBuilder::message(
                        \sprintf(
                            'Building a %s from a bare SQL identifier is forbidden.'
                            . ' Use `QueryIdentifier` instead.',
                            $class_name
                        )
                    )
                        ->identifier('glpi.forbidSqlExpressionIdentifier')
                        ->line($arg->getStartLine())
                        ->build(),
                ];
            }
        }

        return [];
    }

    private function isTypeSafe(Type $type): bool
    {
        if ($type instanceof UnionType) {
            // A union type is safe only if all of the possible types are safe.
            foreach ($type->getTypes() as $sub_type) {
                if (!$this->isTypeSafe($sub_type)) {
                    return false;
                }
            }
            return true;
        }

        $query_element_type = new ObjectType(self::QUERY_ELEMENT_INTERFACE);
        if ($query_element_type->isSuperTypeOf($type)->yes()) {
            // A query element renders its own SQL and carries its own parameters.
            return true;
        }

        // A literal SQL string. Concatenation and interpolation of constants are folded into a
        // constant string by PHPStan, so `'FOO(' . self::SEP . ')'` is accepted too.
        return \count($type->getConstantStrings()) === 1;
    }

    /**
     * Indicates whether the given SQL fragment is nothing but an identifier reference, that
     * should be built with a `QueryIdentifier`.
     */
    private function isBareIdentifier(string $expression): bool
    {
        if (\preg_match(self::IDENTIFIER_PATTERN, $expression) !== 1) {
            return false;
        }

        // A single unquoted token may be a reserved literal (e.g. `NULL`), not an identifier.
        return !\in_array(\strtoupper(\trim($expression)), self::RESERVED_LITERALS, true);
    }

    /**
     * @param array<Arg|Node\VariadicPlaceholder> $args
     */
    private function getArgument(array $args, string $name): ?Arg
    {
        $unresolved_positions = false;

        foreach ($args as $arg) {
            if (!($arg instanceof Arg) || $arg->unpack) {
                // Argument unpacking (`...$args`): the positions that follow cannot be resolved
                // statically. A named argument can still be matched though.
                $unresolved_positions = true;
                continue;
            }
            if ($arg->name === null) {
                // A positional argument can only be located while no unpacking occurred before it.
                if (!$unresolved_positions) {
                    return $arg;
                }
                continue;
            }
            if ($arg->name->name === $name) {
                return $arg;
            }
        }

        return null;
    }

    /**
     * @param array<Arg|Node\VariadicPlaceholder> $args
     */
    private function hasUnpackedArguments(array $args): bool
    {
        foreach ($args as $arg) {
            if (!($arg instanceof Arg) || $arg->unpack) {
                return true;
            }
        }

        return false;
    }
}
