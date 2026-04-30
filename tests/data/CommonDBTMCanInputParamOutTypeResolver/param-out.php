<?php

declare(strict_types=1);

use function PHPStan\Testing\assertType;

// PHPStan will use the extension only if the signature indicates the param is passed by reference.
// It requires this stub to be declared.
class CommonDBTM {
    public function can($ID, int $right, ?array &$input = null): bool
    { }

    public function check($ID, int $right, ?array &$input = null): void
    { }
}

$object = new class extends CommonDBTM {

};

// $input not explicitely typed and therefore considered as mixed.
// We cannot guess its type, so its types should remain mixed.
$object->can(-1, CREATE, $input1);
assertType('mixed', $input1);

$object->check(-1, CREATE, $input2);
assertType('mixed', $input2);

// $input declared with a null value.
// The method will not alter it, it remains null.
$input1 = null;
$object->can(-1, CREATE, $input1);
assertType('null', $input1);

$input2 = null;
$object->check(-1, CREATE, $input2);
assertType('null', $input2);

// $input declared with an array value.
// The method may add entries to the variable, but it will always remain an array.
$input1 = $_POST;
$object->can(-1, CREATE, $input1);
assertType('array<mixed>', $input1);

$input2 = $_POST;
$object->check(-1, CREATE, $input2);
assertType('array<mixed>', $input2);
