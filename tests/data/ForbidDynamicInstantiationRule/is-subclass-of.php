<?php

declare(strict_types=1);

$class = $_GET['itemtype'];
if (is_subclass_of($class, Item_Devices::class, true)) {
    $object = new $class();
}
