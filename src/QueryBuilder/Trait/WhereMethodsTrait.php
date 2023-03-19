<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\QueryBuilder\Trait;

trait WhereMethodsTrait
{
    public function where(string $column, string $operator, int|float|string $value): static
    {
        $this->where[] = [
            'value' => $value,
            'column' => $column,
            'condition' => sprintf(
                '%s %s ?',
                $this->quoteExpression(trim($column)),
                trim($operator),
            ),
        ];

        return $this;
    }

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

    /**
     * @param array<int,int|float|string> $values
     */
    public function whereIn(string $column, array $values): static
    {
        $this->where[] = [
            'value' => $values,
            'column' => $column,
            'condition' => sprintf(
                '%s IN (%s)',
                $this->quoteExpression(trim($column)),
                implode(', ', array_fill(0, count($values), '?')),
            ),
        ];

        return $this;
    }

    /**
     * @param array<int,int|float|string> $values
     */
    public function whereNotIn(string $column, array $values): static
    {
        $this->where[] = [
            'value' => $values,
            'column' => $column,
            'condition' => sprintf(
                '%s NOT IN (%s)',
                $this->quoteExpression(trim($column)),
                implode(', ', array_fill(0, count($values), '?')),
            ),
        ];

        return $this;
    }

    public function whereLike(string $column, string $value): static
    {
        return $this->where($column, 'LIKE', $value);
    }

    public function whereNotLike(string $column, string $value): static
    {
        return $this->where($column, 'NOT LIKE', $value);
    }

    public function whereBetween(string $column, int|float|string $value1, int|float|string $value2): static
    {
        $this->where[] = [
            'value' => [$value1, $value2],
            'column' => $column,
            'condition' => sprintf(
                '%s BETWEEN ? AND ?',
                $this->quoteExpression(trim($column)),
            ),
        ];

        return $this;
    }

    public function whereNotBetween(string $column, int|float|string $value1, int|float|string $value2): static
    {
        $this->where[] = [
            'value' => [$value1, $value2],
            'column' => $column,
            'condition' => sprintf(
                '%s NOT BETWEEN ? AND ?',
                $this->quoteExpression(trim($column)),
            ),
        ];

        return $this;
    }
}
