<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Pdo;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;
use Tomrf\Seminorm\Data\Row;
use Tomrf\Seminorm\Interface\QueryBuilderInterface;
use Tomrf\Seminorm\Interface\QueryExecutorInterface;

class PdoQueryExecutor implements QueryExecutorInterface
{
    protected PDOStatement $pdoStatement;

    public function __construct(
        protected PdoConnection $connection,
        protected ?string $rowClass = null,
        protected ?string $valueClass = null,
    ) {
    }

    /**
     * Returns the number of rows affected by the last SQL statement.
     */
    public function getRowCount(): int
    {
        return $this->pdoStatement->rowCount();
    }

    /**
     * Returns the last inserted row ID as string.
     */
    public function getLastInsertId(): string|false
    {
        return $this->connection->getPdo()?->lastInsertId() ?? false;
    }

    /**
     * Prepare and execute PDOStatement from an instance of
     * QueryBuilderInterface.
     *
     * @throws PDOException
     */
    public function execute(
        QueryBuilderInterface|string $query,
        array $parameters = []
    ): static {
        if (true !== $this->connection->isConnected()) {
            $this->connection->connect();
        }

        if ($query instanceof QueryBuilderInterface) {
            $this->pdoStatement = $this->executeQuery(
                $query->getQuery(),
                $query->getQueryParameters()
            );

            return $this;
        }

        $this->pdoStatement = $this->executeQuery(
            $query,
            $parameters
        );

        return $this;
    }

    /**
     * Fetch next row from the result set as Row.
     *
     * @return null|array<null|object|string>|object
     */
    public function findOne(): null|array|object
    {
        $row = $this->fetchRow($this->pdoStatement);

        if (false === $row) {
            return null;
        }

        return $row;
    }

    /**
     * Fetch all rows from query result set.
     *
     * @return array<int,array<null|object|string>|object>
     */
    public function findMany(): array|object
    {
        return $this->fetchAllRows($this->pdoStatement);
    }

    /**
     * Return the named column of the first row in the result set, or the first column
     * of the first row if no column name is specified.
     *
     * @throws RuntimeException if the query does not return a column
     */
    public function getColumn(string $column = null): string|int|float
    {
        /** @var array<string|int|float>|null */
        $row = $this->findOne();

        if (null === $row) {
            throw new RuntimeException('Query did not return a column');
        }

        if (null === $column && is_array($row)) {
            return array_values($row)[0];
        }

        if ($column !== null && !array_key_exists($column, $row)) {
            throw new RuntimeException(
                sprintf(
                    'Query did not return a column named "%s"',
                    $column
                )
            );
        }

        return $row[$column];
    }

    /**
     * Return the named column of all rows in the result set, or the first column
     * of all rows if no column name is specified.
     *
     * @throws RuntimeException if the query does not return a column
     *
     * @return array<int,string|int|float>
     */
    public function getColumns(string $column = null): array
    {
        $rows = $this->findMany();

        if (null === $column && is_array($rows)) {
            return array_map(
                function (array $row): mixed {
                    return array_values($row)[0];
                },
                $rows
            );
        }

        if (!\is_array($rows)) {
            throw new RuntimeException('Query did not return any rows');
        }

        return array_map(
            fn (array $row) => $row[$column],
            $rows
        );
    }

    /**
     * Return the first row of the result set as an array.
     *
     * @throws RuntimeException if the query does not return a row
     * @return array<string|int|float>
     */

    public function getRow(): array
    {
        $row = $this->findOne();

        if (null === $row) {
            throw new RuntimeException('Query did not return a row');
        }

        return $row;
    }

    /**
     * Return all rows of the result set as an array of arrays.
     *
     * @throws RuntimeException if the query does not return at least one row
     * @return array<int,array<string|int|float>>
     */
    public function getRows(): array
    {
        $rows = $this->findMany();

        if (0 === count($rows)) {
            throw new RuntimeException('Query did not return any rows');
        }

        return $rows;
    }

    /**
     * Prepare and execute PDOStatement from query string and array of
     * parameters.
     *
     * @param array<int|string,mixed> $queryParameters
     *
     * @throws RuntimeException
     */
    protected function executeQuery(string $query, array $queryParameters): PDOStatement
    {
        if (true !== $this->connection->isConnected()) {
            throw new RuntimeException(
                'Unable to execute query - not connected to database'
            );
        }

        $statement = $this->connection->getPdo()?->prepare(
            $query
        );

        if (!$statement instanceof PDOStatement) {
            throw new RuntimeException(
                'Could not get prepared statement, connection error?'
            );
        }

        // remove named keys from parameters array
        $queryParameters = array_values($queryParameters);

        $statement->execute($queryParameters);

        return $statement;
    }

    /**
     * Fetch all rows in a result set as array of Row.
     *
     * @return array<int,array<null|object|string>|object>
     */
    protected function fetchAllRows(PDOStatement $statement): array
    {
        for ($rows = [];;) {
            $row = $this->fetchRow($statement);
            if (false === $row) {
                break;
            }
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Fetch next row from result set as array/rowClass of values/valueClass.
     *
     * @return array<null|object|string>|false|object
     */
    protected function fetchRow(PDOStatement $statement): array|object|false
    {
        /** @var array<string,null|string>|bool */
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (\is_bool($row)) {
            return false;
        }

        $values = [];

        foreach ($row as $key => $value) {
            if (null === $this->valueClass) {
                $values[(string) $key] = $value;

                continue;
            }

            $values[(string) $key] = new $this->valueClass($value);
        }

        if (null === $this->rowClass) {
            return $values;
        }

        return new $this->rowClass($values);
    }
}
