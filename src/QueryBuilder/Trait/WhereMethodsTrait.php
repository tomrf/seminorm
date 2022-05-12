<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\QueryBuilder\Trait;

trait WhereMethodsTrait
{
    public function whereRaw(string $expression): static
    {
        $key = (string) crc32($expression);
        $this->where[$key] = [
            'condition' => $expression,
        ];

        return $this;
    }

    public function whereColumnRaw(string $column, string $expression): static
    {
        return $this->whereRaw(
            sprintf(
                '%s %s',
                $this->quoteExpression(trim($column)),
                $expression
            )
        );
    }

    public function where(string $column, string $operator, int|float|string $value): static
    {
        $this->where[] = [
            'value' => $value,
            'condition' => sprintf(
                '%s %s ?',
                $this->quoteExpression(trim($column)),
                trim($operator),
            ),
        ];

        return $this;
    }

    public function whereEqual(string $column, int|float|string $value): static
    {
        return $this->where($column, '=', $value);
    }

    public function whereNotEqual(string $column, int|float|string $value): static
    {
        return $this->where($column, '!=', $value);
    }

    public function whereNull(string $column): static
    {
        return $this->whereColumnRaw($column, 'IS NULL');
    }

    public function whereNotNull(string $column): static
    {
        return $this->whereColumnRaw($column, 'IS NOT NULL');
    }
}
