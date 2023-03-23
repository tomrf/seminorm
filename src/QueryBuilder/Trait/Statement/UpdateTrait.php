<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\QueryBuilder\Trait\Statement;

use InvalidArgumentException;

trait UpdateTrait
{
    /**
     * @param array<string,int|string|float|null> $values
     *
     * @throws InvalidArgumentException
     */
    public function update(string $table, array $values = []): static
    {
        $this->setTable($table);
        $this->setStatement('UPDATE');

        if (!empty($values)) {
            $this->setFromArray($values);
        }

        return $this;
    }
}
