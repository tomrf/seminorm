<?php

namespace Tomrf\Seminorm\QueryBuilder\Trait\Clause;

trait HavingTrait {
    public function havingRaw(string $expression): static
    {
        $this->having[] = [
            'condition' => trim($expression),
        ];

        return $this;
    }

    public function having(string $column, string $operator, float|int|string $value): static
    {
        $this->having[] = [
            'column' => trim($column),
            'operator' => trim($operator),
            'value' => $value,
            'condition' => sprintf('%s %s ?', $this->quoteExpression(trim($column)), trim($operator)),
        ];
        return $this;
    }
}
