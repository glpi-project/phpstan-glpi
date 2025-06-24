<?php

declare(strict_types=1);

namespace PHPStanGlpi\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use PHPStanGlpi\Services\GlpiVersionResolver;

/**
 * @implements Rule<New_>
 */
final class ForbidDynamicInstantiationRule implements Rule
{
    private GlpiVersionResolver $glpiVersionResolver;

    private bool $treatPhpDocTypesAsCertain;

    public function __construct(
        GlpiVersionResolver $glpiVersionResolver,
        bool $treatPhpDocTypesAsCertain
    ) {
        $this->glpiVersionResolver = $glpiVersionResolver;
        $this->treatPhpDocTypesAsCertain = $treatPhpDocTypesAsCertain;
    }

    public function getNodeType(): string
    {
        return New_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (\version_compare($this->glpiVersionResolver->getGlpiVersion(), '11.0.0-dev', '<')) {
            // Only applies for GLPI >= 11.0.0
            return [];
        }

        if ($this->isSafe($node, $scope)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Instantiating an object from an unrestricted dynamic string is forbidden (see https://github.com/glpi-project/phpstan-glpi?tab=readme-ov-file#forbiddynamicinstantiationrule).'
            )
            ->identifier('glpi.forbidDynamicInstantiation')
            ->build(),
        ];
    }

    private function isSafe(New_ $node, Scope $scope): bool
    {
        if ($node->class instanceof Name) {
            // Either a class identifier (e.g. `new User()`),
            // or a PHP keyword (e.g. `new self()` or `new static()`).
            return true;
        }

        if ($node->class instanceof Node\Stmt\Class_) {
            // Anonymous class instantiation (e.g. `$var = new class () extends CommonDBTM {}`).
            return true;
        }

        $type = $this->treatPhpDocTypesAsCertain ? $scope->getType($node->class) : $scope->getNativeType($node->class);

        if ($this->isTypeSafe($type)) {
            return true;
        }

        return false;
    }

    private function isTypeSafe(Type $type): bool
    {
        if ($type instanceof UnionType) {
            // A union type variable is safe only if all of the possible types are safe.
            foreach ($type->getTypes() as $sub_type) {
                if (!$this->isTypeSafe($sub_type)) {
                    return false;
                }
            }
            return true;
        }

        if ($type->isObject()->yes()) {
            // Either a instanciation from another object instance (e.g. `$a = new Computer(); $b = new $a();`),
            // or from a variable with an object type assigned by the PHPDoc (e.g. `/* @var $class Computer */ $c = new $class();`).
            // Creating an instance from an already instantiated object is considered safe.
            return true;
        }

        if ($type->isClassString()->yes()) {
            // A variable with a `class-string` type assigned by the PHPDoc.
            //
            // Unless the generic type is unspecified, we consider that the related code produces all the necessary
            // checks to ensure that the variable is safe before assigning this type.
            return count($type->getClassStringObjectType()->getObjectClassNames()) > 0;
        }

        $constant_strings = $type->getConstantStrings();
        if (count($constant_strings) > 0) {
            // Instantiation from a string variable with constant(s) value(s).
            // If every values matches a known class (e.g. `$class = 'Computer'; $c = new $class();`),
            // this is considered safe as the class name has been intentionally hardcoded.
            foreach ($constant_strings as $constant_string) {
                if ($constant_string->isClassString()->yes() === false) {
                    return false;
                }
            }
        }

        if ($type->isNull()->yes()) {
            // Instantiation will a `null` hardcoded class name (e.g. `$a = $condition ? Computer::class : null; $b = new $a();`),
            // or from a variable with a nullable type assigned by the PHPDoc (e.g. `/* @var $class class-string<CommonDBTM>|null */ $c = new $class();`).
            // This is safe from this rule point of view as it will not permit to instantiate an unexpected object.
            //
            // An error will be triggered by base PHPStan rules with a most relevant message.
            return true;
        }

        return false;
    }
}
