<?php

// Minimal stubs of the GLPI query elements, to be able to analyse the rule data files
// without having a GLPI instance available.

namespace Glpi\DBAL;

interface QueryElementInterface
{
    public function getValue(): string;
}

class QueryExpression implements QueryElementInterface
{
    /**
     * @param array<int, mixed> $values
     */
    public function __construct(string|QueryExpression $expression, ?string $alias = null, array $values = [])
    {
    }

    public function getValue(): string
    {
        return '';
    }
}

class QueryFunction implements QueryElementInterface
{
    /**
     * @param array<int, mixed> $params
     */
    public function __construct(string $name, array $params = [], ?string $alias = null)
    {
    }

    public function getValue(): string
    {
        return '';
    }
}
