<?php

declare(strict_types=1);

function test()
{
    global $DB;

    /**
     * @var ?string $another
     */
    global $another; // this one should not be detected

    echo 1;
}
