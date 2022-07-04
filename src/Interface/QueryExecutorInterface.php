<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Interface;

interface QueryExecutorInterface
{
    /**
     * Execute query from a QueryBuilderInterface or literal query string.
     *
     * @param array<int|string,mixed> $parameters
     */
    public function execute(
        QueryBuilderInterface|string $query,
        array $parameters = []
    ): static;
}
