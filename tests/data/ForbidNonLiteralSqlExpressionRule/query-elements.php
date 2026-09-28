<?php

use Glpi\DBAL\QueryElementInterface;
use Glpi\DBAL\QueryExpression;
use Glpi\DBAL\QueryFunction;

class QueryElements
{
    public function queryElements(QueryExpression $expression, QueryFunction $function, QueryElementInterface $element): void
    {
        // a query element renders its own SQL and carries its own parameters
        new QueryExpression($expression);
        new QueryExpression($element);

        // all the members of the union type are safe
        new QueryExpression(\random_int(0, 1) === 1 ? $expression : 'NOW()');
    }

    public function otherObjects(\stdClass $object): void
    {
        new QueryExpression($object);
    }
}
