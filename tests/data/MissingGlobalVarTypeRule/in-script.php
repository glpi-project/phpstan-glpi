<?php

declare(strict_types=1);

global $test;

/**
 * @var ?string $another
 */
global $another; // this one should not be detected

echo 1;
