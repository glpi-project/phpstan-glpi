<?php

declare(strict_types=1);

$class = $_SESSION['foo'];
if ($class instanceof CommonDBTM) {
    $object = new $class();
}
