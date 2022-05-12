<?php

declare(strict_types=1);

namespace Tomrf\Seminorm;

use Tomrf\Seminorm\Factory\Factory;
use Tomrf\Seminorm\Interface\QueryBuilderInterface;
use Tomrf\Seminorm\Pdo\PdoConnection;
use Tomrf\Seminorm\Pdo\PdoQueryExecutor;
use Tomrf\Seminorm\QueryBuilder\QueryBuilder;

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
        return $this->queryBuilderFactory->make(); // @phpstan-ignore-line
    }

    /**
     * @param array<int|string,mixed> $parameters
     */
    public function execute(
        QueryBuilderInterface|string $query,
        array $parameters = []
    ): PdoQueryExecutor {
        return $this->queryExecutorFactory->make( // @phpstan-ignore-line
            $this->connection
        )->execute(
            $query,
            $parameters,
        );
    }
}
