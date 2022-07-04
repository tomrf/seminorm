<?php

declare(strict_types=1);

namespace Tomrf\Seminorm;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Tomrf\Seminorm\Factory\Factory;
use Tomrf\Seminorm\Interface\QueryBuilderInterface;
use Tomrf\Seminorm\Pdo\PdoConnection;
use Tomrf\Seminorm\Pdo\PdoQueryExecutor;
use Tomrf\Seminorm\QueryBuilder\QueryBuilder;

class Seminorm implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        protected PdoConnection $connection,
        protected Factory $queryBuilderFactory,
        protected Factory $queryExecutorFactory,
        protected ?string $rowClass = null,
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
        if (null !== $this->logger) {
            $this->logger->info(sprintf('Seminorm SQL execute: "%s"', $query));
        }

        return $this->queryExecutorFactory->make( // @phpstan-ignore-line
            $this->connection,
            $this->rowClass
        )->execute(
            $query,
            $parameters,
        );
    }
}
