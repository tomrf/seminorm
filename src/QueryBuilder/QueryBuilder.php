<?php

/**
 * SQL query builder
 *
 * SQL statements:
 * - SELECT
 * - INSERT
 * - UPDATE
 * - DELETE
 * - JOIN
 * - ORDER BY
 * - GROUP BY
 * - HAVING
 * - LIMIT
 * - OFFSET
 * - ON DUPLICATE KEY
 * - WHERE
 *
 *
 */

declare(strict_types=1);

namespace Tomrf\Seminorm\QueryBuilder;

use Tomrf\Seminorm\Interface\QueryBuilderInterface;
use Tomrf\Seminorm\Sql\SqlCompiler;

class QueryBuilder extends SqlCompiler implements QueryBuilderInterface
{
    use Trait\Statement\SelectTrait;
    use Trait\Statement\InsertTrait;
    use Trait\Statement\UpdateTrait;
    use Trait\Statement\DeleteTrait;
    use Trait\Clause\OrderByTrait;
    use Trait\Clause\WhereTrait;
    use Trait\Clause\GroupByTrait;
    use Trait\Clause\HavingTrait;
    use Trait\Clause\SetTrait;
    use Trait\Clause\JoinTrait;
    use Trait\Clause\LimitOffsetTrait;
    use Trait\Clause\OnDuplicateKeyTrait;

    protected string $table = '';
    protected string $statement = '';

    public function __toString(): string
    {
        return $this->getQuery();
    }

    protected function setTable(string $table): void
    {
        $this->table = $table;
    }

    protected function setStatement(string $type): void
    {
        $this->statement = $type;
    }
}
