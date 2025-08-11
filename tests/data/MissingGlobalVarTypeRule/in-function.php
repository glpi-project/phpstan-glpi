<?php

declare(strict_types=1);

function test()
{
    global $DB; // this one should not be detected as it is automatically resolved

    global $CFGGLPI; // typo

    /**
     * @var ?string $another
     */
    global $another; // this one should not be detected

    echo 1;
}
