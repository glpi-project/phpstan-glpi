<?php

declare(strict_types=1);

namespace PHPStanGlpi\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStanGlpi\Services\GlpiVersionResolver;

/**
 * @implements Rule<StaticCall>
 */
final class ForbidHardCodedRightNameRule implements Rule
{
    private const SESSION_CLASS = 'Session';

    private const CHECKED_METHODS = [
        'checkRight',
        'checkRightsOr',
        'haveRight',
        'haveRightsAnd',
        'haveRightsOr',
    ];

    private GlpiVersionResolver $glpiVersionResolver;

    public function __construct(GlpiVersionResolver $glpiVersionResolver)
    {
        $this->glpiVersionResolver = $glpiVersionResolver;
    }

    public function getNodeType(): string
    {
        return StaticCall::class;
    }

    /**
     * @param StaticCall $node
     * @param Scope $scope
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // bypass rule if glpi version lower than 12
        if (\version_compare($this->glpiVersionResolver->getGlpiVersion(), '12.0.0-dev', '<=')) {
            return [];
        }

        if (!$this->isSessionRightCheckMethod($node)) {
            return [];
        }

        $first_arg = $this->getFirstArgument($node->args);
        if ($first_arg === null) {
            return [];
        }

        // `isSessionRightCheckMethod` already ensures $node->name is an Identifier, but PHPStan cannot infer it
        if (!($node->name instanceof Identifier)) {
            return [];
        }

        $method = $node->name->toString();

        $error_message = $this->buildErrorMessage($first_arg->value, $method, $scope);
        if ($error_message === null) {
            return [];
        }

        return [
            RuleErrorBuilder::message($error_message)
                ->identifier('glpi.forbidHardCodedRightName')
                ->build(),
        ];
    }

    private function buildErrorMessage(Node\Expr $expr, string $method, Scope $scope): ?string
    {
        if ($expr instanceof String_) {
            return \sprintf(
                'Hardcoded string \'%s\' used as right name in Session::%s(). Use a class static property reference such as %s::$rightname instead.',
                $expr->value,
                $method,
                \ucfirst($expr->value),
            );
        }

        if ($expr instanceof ClassConstFetch && $expr->class instanceof Name && $expr->name instanceof Identifier) {
            $class_name = $expr->class->toString();
            $const_name = $expr->name->toString();
            return \sprintf(
                'Class constant \'%s::%s\' used as right name in Session::%s(). Use %s::$rightname instead.',
                $class_name,
                $const_name,
                $method,
                $class_name,
            );
        }

        if ($expr instanceof StaticPropertyFetch && $expr->class instanceof Name && $expr->name instanceof Identifier) {
            // Correct usage: SomeClass::$rightname — not an error
            if ($expr->name->name === 'rightname') {
                return null;
            }
            $class_name = $expr->class->toString();
            $prop_name = $expr->name->name;
            return \sprintf(
                'Static property \'%s::$%s\' used as right name in Session::%s(). Use %s::$rightname instead.',
                $class_name,
                $prop_name,
                $method,
                $class_name,
            );
        }

        $type = $scope->getType($expr);
        $constantStrings = $type->getConstantStrings();
        if (!empty($constantStrings)) {
            $constantStringType = reset($constantStrings);
            return \sprintf(
                'Hardcoded string \'%s\' used as right name in Session::%s(). Use a class static property reference such as %s::$rightname instead.',
                $constantStringType->getValue(),
                $method,
                \ucfirst($constantStringType->getValue()),
            );
        }

        return null;
    }

    private function isSessionRightCheckMethod(StaticCall $node): bool
    {
        // Ignore if the class name is not a static name (e.g. dynamic class - $className::checkRight())
        if (!($node->class instanceof Name)) {
            return false;
        }

        // Only check calls on the Session class
        if ($node->class->toString() !== self::SESSION_CLASS) {
            return false;
        }

        // Ignore if the method name is dynamic (e.g. variable call - Session::$method()) - cannot be analysed by phpstan
        if (!($node->name instanceof Identifier)) {
            return false;
        }

        // Only check calls to the methods listed in CHECKED_METHODS
        if (!\in_array($node->name->toString(), self::CHECKED_METHODS, true)) {
            return false;
        }

        return true;
    }

    /**
     * @param array<Node\Arg|\PhpParser\Node\VariadicPlaceholder> $args
     * @return Node\Arg|null
     */
    private function getFirstArgument(array $args): ?Node\Arg
    {
        // Find the 'module' argument: either the first positional arg or a named 'module' arg
        // variadic expression cannot be parsed (e.g. `Session::checkRight(...$myvar)` )
        $first_arg = null;
        foreach ($args as $arg) {
            if (!($arg instanceof Node\Arg)) {
                continue;
            }
            // Positional argument (no name): this is the 'module' parameter
            if ($arg->name === null) {
                $first_arg = $arg;
                break;
            }
            // Named argument explicitly passed as 'module: ...'
            if ($arg->name->name === 'module') {
                $first_arg = $arg;
                break;
            }
        }

        return $first_arg;
    }
}
