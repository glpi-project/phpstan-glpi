<?php

declare(strict_types=1);

namespace PHPStanGlpi\Analyser;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Type\MethodParameterOutTypeExtension;
use PHPStan\Type\Type;

class CommonDBTMCanInputParamOutTypeResolver implements MethodParameterOutTypeExtension
{
    public function isMethodSupported(
        MethodReflection $methodReflection,
        ParameterReflection $parameter
    ): bool {
        return $methodReflection->getDeclaringClass()->getName() === \CommonDBTM::class // @phpstan-ignore class.notFound
            && \in_array($methodReflection->getName(), ['can', 'check'], true)
            && $parameter->getName() === 'input';
    }

    public function getParameterOutTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        ParameterReflection $parameter,
        Scope $scope
    ): ?Type {
        $inputArg = $methodCall->getArg('input', 2);

        if ($inputArg === null) {
            return null;
        }

        // The `CommonDBTM::can()` method always preserve the `$input` parameter type.
        // Returning its know type prevents PHPStan to consider it can be changed from `array` to `null`
        // inside the method.
        //
        // The `CommonDBTM::check()` is a proxy to `CommonDBTM::can()` and therefore should act the same way.
        return $scope->getType($inputArg->value);
    }
}
