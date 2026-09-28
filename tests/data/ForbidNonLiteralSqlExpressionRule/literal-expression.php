<?php

use Glpi\DBAL\QueryExpression;

class LiteralExpression
{
    private const SEPARATOR = ', ';

    public function literals(): void
    {
        // a plain literal string
        new QueryExpression('NOW()');

        // concatenation of literals, folded into a constant string by PHPStan
        new QueryExpression('CONCAT(`a`' . self::SEPARATOR . '`b`)');

        // interpolation of a constant, folded into a constant string too
        new QueryExpression("COUNT(DISTINCT `id`)" . PHP_EOL);

        // a class constant
        new QueryExpression(self::SEPARATOR);

        // an heredoc without any dynamic part
        new QueryExpression(<<<SQL
            CASE WHEN `is_deleted` = 1 THEN 1 ELSE 0 END
            SQL);

        // both branches are literal strings
        new QueryExpression(\random_int(0, 1) === 1 ? 'MIN(`date`)' : 'MAX(`date`)');

        // the dynamic parts are bound as statement parameters
        new QueryExpression('DATE_ADD(`date`, INTERVAL ? DAY)', null, [$this->getDelay()]);
    }

    private function getDelay(): int
    {
        return 1;
    }
}
