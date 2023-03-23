<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\QueryBuilder\Trait\Clause;

trait JoinTrait
{
    public function join(string $table, string $joinCondition, string $joinType = null): static
    {
        $this->join[] = [
            'table' => trim($table),
            'condition' => trim($joinCondition),
            'type' => $joinType,
        ];

        return $this;
    }
}
