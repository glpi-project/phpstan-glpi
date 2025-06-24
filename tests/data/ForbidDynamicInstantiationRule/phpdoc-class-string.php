<?php

declare(strict_types=1);

/** @var class-string $class1 */
$class1 = $_GET['class1'];
$object1 = new $class1(); // unsafe, the PHPDoc does not provide an expected type for the `class-string`

/** @var class-string<\CommonDBTM> $class2 */
$class2 = $_GET['class2'];
$object2 = new $class2();
