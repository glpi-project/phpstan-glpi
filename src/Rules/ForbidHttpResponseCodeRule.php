<?php

declare(strict_types=1);

namespace PHPStanGlpi\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStanGlpi\Services\GlpiVersionResolver;

/**
 * @implements Rule<FuncCall>
 */
class ForbidHttpResponseCodeRule implements Rule
{
    private GlpiVersionResolver $glpiVersionResolver;

    public function __construct(GlpiVersionResolver $glpiVersionResolver)
    {
        $this->glpiVersionResolver = $glpiVersionResolver;
    }

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (\version_compare($this->glpiVersionResolver->getGlpiVersion(), '11.0.0-dev', '<')) {
            // Only applies for GLPI >= 11.0.0
            return [];
        }

        if (
            $node->name instanceof Name
            && $node->name->toString() === 'http_response_code'
            && count($node->getRawArgs()) > 0 // `http_response_code()` used without args is a setter and does not cause issues
        ) {
            return [
                RuleErrorBuilder::message(
                    'You should not use the `http_response_code` function to change the response code. Due to a PHP bug, it may not provide the expected result (see https://bugs.php.net/bug.php?id=81451).',
                )
                ->identifier('glpi.forbidHttpResponseCode')
                ->build(),
            ];
        }

        return [];
    }
}
