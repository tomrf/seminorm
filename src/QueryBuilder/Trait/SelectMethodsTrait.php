<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\QueryBuilder\Trait;

trait SelectMethodsTrait
{
    public function select(string ...$columns): static
    {
        foreach ($columns as $column) {
            $this->select[] = [
                'expression' => $this->quoteExpression(trim($column)),
            ];
        }

        return $this;
    }

    public function selectAs(string $expression, string $alias): static
    {
        $this->select[] = [
            'expression' => $this->quoteExpression(trim($expression)),
            'alias' => $this->quoteString(trim($alias)),
        ];

        return $this;
    }

    public function selectRaw(string ...$expressions): static
    {
        foreach ($expressions as $expression) {
            $this->select[] = [
                'expression' => trim($expression),
            ];
        }

        return $this;
    }

    public function selectRawAs(string $expression, string $alias): static
    {
        $this->select[] = [
            'expression' => trim($expression),
            'alias' => $this->quoteString(trim($alias)),
        ];

        return $this;
    }
}
