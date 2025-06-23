<?php

declare(strict_types=1);

namespace PHPStanGlpi\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Exit_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStanGlpi\Services\GlpiVersionResolver;

/**
 * @implements Rule<Exit_>
 */
final class ForbidExitRule implements Rule
{
    private GlpiVersionResolver $glpiVersionResolver;

    public function __construct(GlpiVersionResolver $glpiVersionResolver)
    {
        $this->glpiVersionResolver = $glpiVersionResolver;
    }

    public function getNodeType(): string
    {
        return Exit_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (\version_compare($this->glpiVersionResolver->getGlpiVersion(), '11.0.0-dev', '<')) {
            // Only applies for GLPI >= 11.0.0
            return [];
        }

        $name = $node->getAttribute('kind') === Exit_::KIND_DIE ? 'die' : 'exit';

        return [
            RuleErrorBuilder::message(
                \sprintf(
                    'You should not use the `%s` function. It prevents the execution of post-request/post-command routines.',
                    $name
                )
            )
            ->identifier('glpi.forbidExit')
            ->build(),
        ];
    }
}
