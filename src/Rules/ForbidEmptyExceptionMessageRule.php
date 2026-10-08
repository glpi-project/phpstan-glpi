<?php

declare(strict_types=1);

namespace PHPStanGlpi\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Native PHP exceptions must have an explicit message.
 *
 * @implements Rule<New_>
 */
final class ForbidEmptyExceptionMessageRule implements Rule
{
    private const EXCEPTIONS_TO_CATCH = [
            \Exception::class,
            \RuntimeException::class,
            \LogicException::class,
        ];

    public function getNodeType(): string
    {
        return New_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof New_) {
            return [];
        }


        if (!$node->class instanceof Node\Name) {
            return [];
        }

        $className = $node->class->toString();
        // not an Exception -> return
        if (!is_a($className, \Exception::class, true)) {
            return [];
        }

        // Not an exception to catch
        if (!in_array($className, self::EXCEPTIONS_TO_CATCH, true)) {
            return [];
        }

        // Check message argument (empty or not provided)
        // exceptions without message may not be reported
        // if a single argument is provided with it's name (eg. new \Exception(code: 123))
        $no_args = count($node->args) === 0;
        // message provided but empty
        $emptyMessage = false;
        if (!$no_args) {
            $firstArg = $node->args[0]->value;
            $emptyMessage = $firstArg instanceof Node\Scalar\String_ && trim($firstArg->value) === '';
        }

        if ($no_args || $emptyMessage) {
            return [
                RuleErrorBuilder::message('Native PHP exceptions must have an explicit message. ' . $className)->identifier('glpi.forbidEmptyExceptionMessage')->build(),
            ];
        }

        return [];
    }
}
