<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Sql;

use RuntimeException;

class SqlCompiler
{
    protected string $table;
    protected string $statement;

    protected int $limit = -1;
    protected int $offset = -1;

    protected ?string $onDuplicateKey = null;

    /**
     * @var array<array<null|bool|float|int|string>>
     */
    protected array $select = [];

    /**
     * @var array<array<null|bool|float|int|string>>
     */
    protected array $join = [];

    /**
     * @var array<array<null|bool|float|int|string>>
     */
    protected array $where = [];

    /**
     * @var array<array<null|bool|float|int|string>>
     */
    protected array $order = [];

    /**
     * @var array<array<null|bool|float|int|string>>
     */
    protected array $set = [];

    /**
     * @var array<int|string,null|bool|float|int|string>
     */
    protected array $queryParameters = [];

    public function getQuery(): string
    {
        $this->queryParameters = [];

        return $this->compileQuery();
    }

    /**
     * @return array<null|bool|float|int|int|string>
     */
    public function getQueryParameters(): array
    {
        $this->queryParameters = [];

        $this->compileQuery();

        return $this->queryParameters;
    }

    protected function compileClauseSelect(): string
    {
        if (0 === \count($this->select)) {
            return sprintf('%s.*', $this->quoteExpression($this->table));
        }

        foreach ($this->select as $key => $select) {
            $selectExpression = sprintf(
                '%s%s%s%s',
                $selectExpression ?? '',
                $select['expression'],
                isset($select['alias']) ? (' AS '.$select['alias']) : '',
                ($key !== array_key_last($this->select)) ? ', ' : '',
            );
        }

        return $selectExpression;
    }

    protected function compileClauseJoin(): string
    {
        foreach ($this->join as $join) {
            $joinClause = sprintf(
                '%s JOIN %s ON %s',
                $joinClause ?? '',
                $this->quoteExpression((string) $join['table']),
                $this->quoteExpression((string) $join['condition'])
            );
        }

        return $joinClause ?? '';
    }

    protected function compileClauseInsert(): string
    {
        $columns = '';
        $values = '';

        if (0 === \count($this->set)) {
            return '';
        }

        foreach ($this->set as $column => $valueData) {
            $isRaw = $valueData['raw'];
            $value = $valueData['value'];

            $column = (string) $column;
            $columns .= sprintf('%s, ', $this->quoteExpression($column));

            if (true === $isRaw) {
                $values .= sprintf('%s, ', $value);

                continue;
            }

            $values .= '?, ';
            $this->queryParameters[] = $value;
        }

        return sprintf(
            ' (%s) VALUES (%s)',
            trim($columns, ', '),
            trim($values, ', ')
        );
    }

    protected function compileClauseSet(): string
    {
        if (0 === \count($this->set)) {
            return '';
        }

        $statement = '';

        foreach ($this->set as $column => $assignment) {
            $isRaw = $assignment['raw'];
            $value = $assignment['value'];

            if (true === $isRaw) {
                $statement .= sprintf(
                    '%s = %s, ',
                    $this->quoteExpression((string) $column),
                    $value
                );

                continue;
            }

            $statement .= sprintf(
                '%s = ?, ',
                $this->quoteExpression((string) $column),
            );
            $this->queryParameters[] = $value;
        }

        return trim($statement, ', ');
    }

    protected function compileClauseWhere(): string
    {
        if (0 === \count($this->where)) {
            return '';
        }

        $whereCondition = ' WHERE ';

        foreach ($this->where as $key => $where) {
            $whereCondition .= $where['condition'];

            if (array_key_last($this->where) !== $key) {
                $whereCondition .= ' AND ';
            }

            if (isset($where['value'])) {
                $this->queryParameters[] = $where['value'];
            }
        }

        return $whereCondition;
    }

    protected function compileClauseOrderBy(): string
    {
        if (0 === \count($this->order)) {
            return '';
        }

        $orderByClause = ' ORDER BY ';
        foreach ($this->order as $key => $orderBy) {
            $orderByClause .= sprintf(
                '%s %s%s',
                $this->quoteExpression((string) $orderBy['column']),
                $orderBy['direction'],
                ($key !== array_key_last($this->order)) ? ', ' : ''
            );
        }

        return $orderByClause;
    }

    protected function compileClauseLimit(): string
    {
        return sprintf(
            '%s%s',
            (-1 !== $this->limit) ? sprintf(' LIMIT %d', $this->limit) : '',
            (-1 !== $this->limit && -1 !== $this->offset) ? sprintf(' OFFSET %d', $this->offset) : ''
        );
    }

    protected function compileClauseTable(): string
    {
        return $this->quoteExpression($this->table);
    }

    protected function compileClauseOnDuplicateKey(): string
    {
        return $this->onDuplicateKey
            ? sprintf(' ON DUPLICATE KEY %s', $this->onDuplicateKey)
            : '';
    }

    protected function compileQuery(): string
    {
        if (!$this->statement) {
            throw new RuntimeException('No SQL statement specified for query');
        }

        $template = match ($this->statement) {
            'INSERT INTO' => 'INSERT INTO {table}{insert}{onDuplicateKey}',
            'UPDATE' => 'UPDATE {table} SET {set}{where}{orderBy}{limit}',
            'DELETE FROM' => 'DELETE FROM {table}{where}{orderBy}{limit}',
            'SELECT' => 'SELECT {select} FROM {table}{join}{where}{orderBy}{limit}',
            default => null
        };

        if (null === $template) {
            throw new RuntimeException(sprintf(
                'Unknown SQL statement: "%s"',
                $this->statement
            ));
        }

        preg_match_all('/\{(\w+)\}/', $template, $matches);

        foreach (array_keys($matches[0]) as $key) {
            $func = sprintf('compileClause%s', ucfirst($matches[1][$key]));
            $matches[1][$key] = $this->{$func}();
        }

        return trim(str_replace($matches[0], $matches[1], $template));
    }

    protected function quoteString(string $string): string
    {
        return sprintf('"%s"', $string);
    }

    protected function quoteExpression(string $expression): string
    {
        $quotedExpression = '';

        if (mb_strstr($expression, ' ')) {
            $parts = explode(' ', $expression);

            foreach ($parts as $part) {
                $quotedExpression .= $this->quoteExpression($part);
            }

            return $quotedExpression;
        }

        if (mb_strstr($expression, '.')) {
            $parts = explode('.', $expression);

            foreach ($parts as $key => $part) {
                $quotedExpression .= $this->quoteExpression($part);
                if ($key !== array_key_last($parts)) {
                    $quotedExpression .= '.';
                }
            }

            return $quotedExpression;
        }

        if (!$this->isValidColumnName($expression)) {
            return $expression;
        }

        return sprintf('`%s`', $expression);
    }

    protected function isQuotedExpression(string $expression): bool
    {
        $offsetEnd = -1 + mb_strlen($expression);
        if ('`' === $expression[0] && '`' === $expression[$offsetEnd]) {
            return true;
        }

        return false;
    }

    protected function isValidColumnName(string $name): bool
    {
        return (bool) (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name));
    }
}
