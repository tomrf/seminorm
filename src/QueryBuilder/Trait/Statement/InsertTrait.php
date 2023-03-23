<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\QueryBuilder\Trait\Statement;

use InvalidArgumentException;

trait InsertTrait
{
    /**
     * Insert a row into a table
     *
     * @param string $table
     * @param array<string,int|string|float|null> $values
     *
     * @throws InvalidArgumentException
     */
    public function insertInto(string $table, array $values = []): static
    {
        $this->setTable($table);
        $this->setStatement('INSERT INTO');

        if (!empty($values)) {
            $this->setFromArray($values);
        }

        return $this;
    }
}
