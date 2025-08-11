<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

function update95xto1000()
{
    global $DB, $migration;

    assertType(\DBmysql::class, $DB);
    assertType(\Migration::class, $migration);
}
