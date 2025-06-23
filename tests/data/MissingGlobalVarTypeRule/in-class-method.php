<?php

declare(strict_types=1);

class Test
{
    public function test()
    {
        global $CFG_GLPI, $GLPI_CACHE;

        /**
         * @var array $another
         */
        global $another; // this one should not be detected

        echo 1;
    }
}
