<?php

declare(strict_types=1);

class Test
{
    public function test()
    {
        global $CFG_GLPI, $GLPI_CACHE; // these should not be detected as they are automatically resolved

        /**
         * @var array $another
         */
        global $another; // this one should not be detected

        global $undocumented;

        echo 1;
    }
}
