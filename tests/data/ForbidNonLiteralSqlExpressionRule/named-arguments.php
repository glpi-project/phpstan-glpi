<?php

use Glpi\DBAL\QueryExpression;

class NamedArguments
{
    public function namedArguments(string $table): void
    {
        // the `expression` argument is the analysed one, whatever its position
        new QueryExpression(expression: 'NOW()', alias: $table);
        new QueryExpression(alias: $table, expression: 'NOW()');

        new QueryExpression(expression: $table, alias: 'foo');
        new QueryExpression(alias: 'foo', expression: $table);
    }

    /**
     * @param array<int, mixed> $args
     */
    public function spreadArguments(array $args): void
    {
        // the `expression` argument cannot be located, it is reported
        new QueryExpression(...$args);
    }

    /**
     * @param array<int, mixed> $args
     */
    public function resolvableSpreadArguments(array $args): void
    {
        // the `expression` argument is explicit, the unpacking only affects the other ones
        new QueryExpression('NOW()', ...$args);
        new QueryExpression(...$args, expression: 'NOW()');

        new QueryExpression($this->getExpression(), ...$args);
    }

    private function getExpression(): string
    {
        return 'NOW()';
    }
}
