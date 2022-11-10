<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\QueryBuilder;

use InvalidArgumentException;
use Tomrf\Seminorm\Interface\QueryBuilderInterface;
use Tomrf\Seminorm\Sql\SqlCompiler;

class QueryBuilder extends SqlCompiler implements QueryBuilderInterface
{
    use Trait\OrderByMethodsTrait;
    use Trait\SelectMethodsTrait;
    use Trait\WhereMethodsTrait;

    protected string $table = '';
    protected string $statement = '';

    public function __toString(): string
    {
        return $this->getQuery();
    }

    public function selectFrom(string $table, string ...$columns): static
    {
        $this->setTable($table);
        $this->setStatement('SELECT');

        foreach ($columns as $column) {
            $this->select($column);
        }

        return $this;
    }

    /**
     * @param array<string,int|string|float|null $values
     *
     * @throws InvalidArgumentException
     */
    public function insertInto(string $table, array $values = []): static
    {
        $this->setTable($table);
        $this->setStatement('INSERT INTO');

        if (!empty($values)) {
            $this->setFromArray($values);
        }

        return $this;
    }

    /**
     * @param array<string,int|string|float|null $values
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

    public function deleteFrom(string $table): static
    {
        $this->setTable($table);
        $this->setStatement('DELETE FROM');

        return $this;
    }

    public function set(string $column, float|int|string $value): static
    {
        $key = trim($column);

        $this->set[$key] = [
            'value' => $value,
            'raw' => false,
        ];

        return $this;
    }

    public function setRaw(string $column, string $expression): static
    {
        $key = trim($column);

        $this->set[$key] = [
            'value' => $expression,
            'raw' => true,
        ];

        return $this;
    }

    /**
     * @param array<string, null|float|int|string> $values
     *
     * @throws InvalidArgumentException
     */
    public function setFromArray(array $values): static
    {
        foreach ($values as $column => $value) {
            if (!\is_string($column)) {
                throw new InvalidArgumentException(
                    'Column name (array key) must be string, got '.\gettype($column)
                );
            }

            if (null === $value) {
                $this->setRaw($column, 'NULL');

                continue;
            }

            $this->set($column, $value);
        }

        return $this;
    }

    public function alias(string $expression, string $alias): static
    {
        foreach ($this->select as $i => $select) {
            if ($select['expression'] === $expression) {
                $this->select[$i]['alias'] = $this->quoteString(trim($alias));
            }
        }

        return $this;
    }

    public function join(string $table, string $joinCondition, string $joinType = null): static
    {
        $this->join[] = [
            'table' => trim($table),
            'condition' => trim($joinCondition),
            'type' => $joinType,
        ];

        return $this;
    }

    public function limit(int $limit, ?int $offset = null): static
    {
        if ($limit < 0) {
            throw new InvalidArgumentException('Negative limit not allowed');
        }

        $this->limit = $limit;

        if (null !== $offset) {
            return $this->offset($offset);
        }

        return $this;
    }

    public function offset(int $offset): static
    {
        if ($offset < 0) {
            throw new InvalidArgumentException('Negative offset not allowed');
        }

        $this->offset = $offset;

        return $this;
    }

    public function onDuplicateKey(string $expression): static
    {
        $this->onDuplicateKey = trim($expression);

        return $this;
    }

    protected function setTable(string $table): void
    {
        $this->table = $table;
    }

    protected function setStatement(string $type): void
    {
        $this->statement = $type;
    }
}
