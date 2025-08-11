<?php

declare(strict_types=1);

global $test;

/**
 * @var ?string $another
 */
global $another; // this one should not be detected

global $GLPI_CACHE, $CFG_GLPI; // these should not be detected as they are automatically resolved

echo 1;
