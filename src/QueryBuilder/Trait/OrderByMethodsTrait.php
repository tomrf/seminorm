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

    public function orderByRaw(string $expression): static
    {
        $this->order[] = [
            'expression' => trim($expression),
        ];

        return $this;
    }

    public function orderByRandom(): static
    {
        return $this->orderByRaw('RANDOM()');
    }

    public function orderByRandomSeed(int $seed): static
    {
        return $this->orderByRaw(sprintf('RANDOM(%d)', $seed));
    }
}
