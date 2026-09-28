<?php

use Glpi\DBAL\QueryFunction;

class OtherClasses
{
    /**
     * @param array<int, mixed> $params
     */
    public function otherClasses(string $name, array $params): void
    {
        // only the classes that take raw SQL are analysed
        new QueryFunction($name, $params);
        new \stdClass();
    }
}
