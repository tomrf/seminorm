<?php

namespace Tomrf\Seminorm\QueryBuilder\Trait\Clause;

trait GroupByTrait {
    public function groupBy(string $column): static
    {
        $this->group[] = trim($column);

        return $this;
    }
}
