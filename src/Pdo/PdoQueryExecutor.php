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
     * @return null|array<null|string>|object
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
     * @return array<int,array<null|string>|object>
     */
    public function findMany(): array|object
    {
        return $this->fetchAllRows($this->pdoStatement);
    }

    /**
     * Prepare and execute PDOStatement from query string and array of
     * parameters.
     *
     * @param array<int|string,mixed> $queryParameters
     *
     * @throws PDOException
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

        $statement->execute($queryParameters);

        return $statement;
    }

    /**
     * Fetch all rows in a result set as array of Row.
     *
     * @return array<int,array<null|string>|object>
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
     * Fetch next row from result set as Row.
     *
     * @return array<null|string>|false|object
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
            $values[(string) $key] = $value;
        }

        if (null === $this->rowClass) {
            return $values;
        }

        return new $this->rowClass($values);
    }
}
