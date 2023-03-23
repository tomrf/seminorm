<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\QueryBuilder\Trait\Statement;

trait SelectTrait
{
    public function selectFrom(string $table, string ...$columns): static
    {
        $this->setTable($table);
        $this->setStatement('SELECT');

        foreach ($columns as $column) {
            $this->select($column);
        }

        return $this;
    }


    public function select(string ...$columns): static
    {
        $this->setStatement('SELECT');

        foreach ($columns as $column) {
            $this->select[] = [
                'expression' => $this->quoteExpression(trim($column)),
            ];
        }

        return $this;
    }

    public function selectAs(string $column, string $alias): static
    {
        $this->setStatement('SELECT');

        $this->select[] = [
            'expression' => $this->quoteExpression(trim($column)),
            'alias' => $this->quoteString(trim($alias)),
        ];

        return $this;
    }

    public function selectRaw(string ...$expressions): static
    {
        $this->setStatement('SELECT');

        foreach ($expressions as $expression) {
            $this->select[] = [
                'expression' => trim($expression),
            ];
        }

        return $this;
    }

    public function selectRawAs(string $expression, string $alias): static
    {
        $this->setStatement('SELECT');

        $this->select[] = [
            'expression' => trim($expression),
            'alias' => $this->quoteString(trim($alias)),
        ];

        return $this;
    }

    public function selectDistinct(string ...$columns): static
    {
        $this->setStatement('SELECT DISTINCT');

        foreach ($columns as $column) {
            $this->select[] = [
                'expression' => $this->quoteExpression(trim($column)),
            ];
        }

        return $this;
    }

    public function selectDistinctAs(string $expression, string $alias): static
    {
        $this->setStatement('SELECT DISTINCT');

        $this->select[] = [
            'expression' => $this->quoteExpression(trim($expression)),
            'alias' => $this->quoteString(trim($alias)),
        ];

        return $this;
    }

    public function selectDistinctRaw(string ...$expressions): static
    {
        $this->setStatement('SELECT DISTINCT');

        foreach ($expressions as $expression) {
            $this->select[] = [
                'expression' => trim($expression),
            ];
        }

        return $this;
    }

    public function selectDistinctRawAs(string $expression, string $alias): static
    {
        $this->setStatement('SELECT DISTINCT');

        $this->select[] = [
            'expression' => trim($expression),
            'alias' => $this->quoteString(trim($alias)),
        ];

        return $this;
    }

    public function selectCount(string $column, string $alias = 'count'): static
    {
        return $this->selectRawAs(
            sprintf('COUNT(%s)', $this->quoteExpression(trim($column))),
            $alias
        );
    }

    public function selectSum(string $column, string $alias = 'sum'): static
    {
        return $this->selectRawAs(
            sprintf('SUM(%s)', $this->quoteExpression(trim($column))),
            $alias
        );
    }

    public function selectAvg(string $column, string $alias = 'avg'): static
    {
        return $this->selectRawAs(
            sprintf('AVG(%s)', $this->quoteExpression(trim($column))),
            $alias
        );
    }

    public function selectMin(string $column, string $alias = 'min'): static
    {
        return $this->selectRawAs(
            sprintf('MIN(%s)', $this->quoteExpression(trim($column))),
            $alias
        );
    }

    public function selectMax(string $column, string $alias = 'max'): static
    {
        return $this->selectRawAs(
            sprintf('MAX(%s)', $this->quoteExpression(trim($column))),
            $alias
        );
    }



}
