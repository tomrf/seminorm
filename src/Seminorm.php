<?php

declare(strict_types=1);

namespace Tomrf\Seminorm;

use Tomrf\Seminorm\Factory\Factory;
use Tomrf\Seminorm\Interface\QueryBuilderInterface;
use Tomrf\Seminorm\Pdo\PdoConnection;
use Tomrf\Seminorm\Pdo\PdoQueryExecutor;

class Seminorm
{
    public function __construct(
        protected PdoConnection $connection,
        protected Factory $queryBuilderFactory,
        protected Factory $queryExecutorFactory,
    ) {
    }

    /**
     * Return the active connection.
     */
    public function getConnection(): PdoConnection
    {
        return $this->connection;
    }

    public function query(): QueryBuilder
    {
        return $this->queryBuilderFactory->make();
    }

    /**
     * @param array<int|string,mixed> $parameters
     */
    public function execute(
        QueryBuilderInterface|string $query,
        array $parameters = []
    ): PdoQueryExecutor {
        return $this->queryExecutorFactory->make(
            $this->connection
        )->execute(
            $query,
            $parameters,
        );
    }
}
