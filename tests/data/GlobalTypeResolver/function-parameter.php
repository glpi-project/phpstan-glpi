<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/**
 * @param string $FOOTER_LOADED
 */
function whatever(array $HEADER_LOADED, $FOOTER_LOADED, $foo, $DB): void
{
    assertType('array', $HEADER_LOADED); // defined by the function signature
    assertType('string', $FOOTER_LOADED); // defined by the PHPDoc
    assertType('mixed', $foo); // default type

    // `$DB` is unexpectedly resolved because we did not found how to differenciate
    // global variables declarations from function parameter declarations in a `ExpressionTypeResolverExtension` class.
    // The type should be `mixed`.
    assertType(\DBmysql::class, $DB);
}
