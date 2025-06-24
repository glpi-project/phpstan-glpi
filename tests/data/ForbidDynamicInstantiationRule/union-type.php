<?php

declare(strict_types=1);

class MyClass
{
    public function doSomething(Computer|Monitor $asset): void
    {
        $copy = new $asset();

        // ...
    }

    /**
     * @param class-string|CommonDBTM $class
     */
    public function instantiateFoo(string|CommonDBTM $class): CommonDBTM
    {
        return new $class(); // unsafe, the PHPDoc does not provide an expected type for the `class-string` case
    }

    /**
     * @param class-string<CommonDBTM>|CommonDBTM $class
     */
    public function instantiateBar(string|CommonDBTM $class): CommonDBTM
    {
        return new $class();
    }
}
