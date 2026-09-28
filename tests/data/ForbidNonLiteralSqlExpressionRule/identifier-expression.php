<?php

use Glpi\DBAL\QueryExpression;

class IdentifierExpression
{
    public function identifiers(): void
    {
        // a bare column name, quoted or not
        new QueryExpression('`id`');
        new QueryExpression('id');

        // a column name prefixed by its table, quoted or not
        new QueryExpression('`glpi_tickets`.`id`');
        new QueryExpression('glpi_tickets.id');

        // all the columns of a table
        new QueryExpression('`glpi_tickets`.*');

        // both branches are identifiers
        new QueryExpression(\random_int(0, 1) === 1 ? '`id`' : '`name`');
    }

    public function notIdentifiers(): void
    {
        // a function call
        new QueryExpression('NOW()');
        new QueryExpression('COUNT(`id`)');

        // an operation on an identifier
        new QueryExpression('`a` + 1');

        // reserved literals are values, not identifiers
        new QueryExpression('NULL');
        new QueryExpression('TRUE');
        new QueryExpression('CURRENT_TIMESTAMP');

        // the MySQL pseudo-table, that must not be quoted
        new QueryExpression('DUAL');

        // a quoted value
        new QueryExpression("'some value'");
    }
}
