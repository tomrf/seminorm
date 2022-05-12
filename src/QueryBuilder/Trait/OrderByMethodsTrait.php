<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\QueryBuilder\Trait;

trait OrderByMethodsTrait
{
    public function orderByAsc(string $column): static
    {
        $this->order[] = [
            'column' => trim($column),
            'direction' => 'ASC',
        ];

        return $this;
    }

    public function orderByDesc(string $column): static
    {
        $this->order[] = [
            'column' => trim($column),
            'direction' => 'DESC',
        ];

        return $this;
    }
}
