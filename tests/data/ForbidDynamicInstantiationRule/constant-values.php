<?php

declare(strict_types=1);

$class = null;
if (rand(0, 100) > 50) {
    $class = Computer::class;
} else {
    $class = Monitor::class;
}
$object = new $class();
