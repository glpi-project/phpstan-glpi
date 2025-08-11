<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

/** @var \DBmysql|null $DB */
global $DB, $ANOTHER_GLOBAL;

assertType('DBmysql|null', $DB); // overriden by PHPDoc
assertType('mixed', $ANOTHER_GLOBAL);

function test()
{
    /** @var bool $GLPI_CACHE */
    global $DB, $GLPI_CACHE;

    assertType(\DBmysql::class, $DB);
    assertType('bool', $GLPI_CACHE); // overriden by PHPDoc
};

class Test
{
    protected function something(): void
    {
        /** @var \Foo\Bar $DB */
        global $DB;
        global $GLPI_CACHE;

        assertType(\Foo\Bar::class, $DB); // overriden by PHPDoc
        assertType(\Psr\SimpleCache\CacheInterface::class, $GLPI_CACHE);
    }
}
