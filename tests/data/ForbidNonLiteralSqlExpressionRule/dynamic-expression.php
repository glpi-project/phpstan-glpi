<?php

use Glpi\DBAL\QueryExpression;

class DynamicExpression
{
    public function dynamic(string $table, int $id): void
    {
        // a variable
        new QueryExpression($table);

        // a string interpolation
        new QueryExpression("COUNT(`$table`.`id`)");

        // a concatenation with a dynamic part
        new QueryExpression('COUNT(' . $table . ')');

        // a function call result
        new QueryExpression(\sprintf('COUNT(`%s`.`id`)', $table));

        // a method call result
        new QueryExpression($this->buildExpression($id));

        // only one of the branches is a literal string
        new QueryExpression($id > 0 ? $table : 'NOW()');
    }

    private function buildExpression(int $id): string
    {
        return 'id = ' . $id;
    }
}
