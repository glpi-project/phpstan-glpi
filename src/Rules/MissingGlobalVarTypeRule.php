<?php

declare(strict_types=1);

namespace PHPStanGlpi\Rules;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Global_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\FileTypeMapper;
use PHPStan\Type\Type;
use PHPStanGlpi\Services\GlpiVersionResolver;
use PHPStanGlpi\Analyser\GlobalTypeResolver;

/**
 * @implements Rule<Stmt>
 */
class MissingGlobalVarTypeRule implements Rule
{
    private FileTypeMapper $fileTypeMapper;

    private GlobalTypeResolver $globalTypeResolver;

    private GlpiVersionResolver $glpiVersionResolver;

    public function __construct(
        FileTypeMapper $fileTypeMapper,
        GlobalTypeResolver $globalTypeResolver,
        GlpiVersionResolver $glpiVersionResolver
    ) {
        $this->fileTypeMapper = $fileTypeMapper;
        $this->globalTypeResolver = $globalTypeResolver;
        $this->glpiVersionResolver = $glpiVersionResolver;
    }

    public function getNodeType(): string
    {
        return Stmt::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (\version_compare($this->glpiVersionResolver->getGlpiVersion(), '10.0.0-dev', '<')) {
            // Only applies for GLPI >= 10.0.0
            return [];
        }

        if (!($node instanceof Global_)) {
            return [];
        }

        $variablesTypes = [];
        foreach ($node->vars as $var) {
            if (!$var instanceof Variable || !is_string($var->name)) {
                // Unexpected case that should not happen
                continue;
            }

            if ($this->globalTypeResolver->getType($var, $scope) instanceof Type) {
                // The global variable type can be resolved automatically, no need to force its definition
                continue;
            }

            $variablesTypes[$var->name] = null;
        }

        $function = $scope->getFunction();
        foreach ($node->getComments() as $comment) {
            if (!$comment instanceof Doc) {
                continue;
            }
            $resolvedPhpDoc = $this->fileTypeMapper->getResolvedPhpDoc(
                $scope->getFile(),
                $scope->isInClass() ? $scope->getClassReflection()->getName() : null,
                $scope->isInTrait() ? $scope->getTraitReflection()->getName() : null,
                $function !== null ? $function->getName() : null,
                $comment->getText(),
            );
            foreach ($resolvedPhpDoc->getVarTags() as $key => $varTag) {
                if (array_key_exists($key, $variablesTypes)) {
                    $variablesTypes[$key] = $varTag->getType()->toPhpDocNode();
                }
            }
        }

        $errors = [];

        foreach ($variablesTypes as $variableName => $variableType) {
            if ($variableType === null) {
                $errors[] = RuleErrorBuilder::message(
                    sprintf(
                        'Missing PHPDoc tag @var for global variable $%s',
                        $variableName
                    )
                )->identifier('glpi.missingGlobalVarType')->build();
            }
        }

        return $errors;
    }
}
