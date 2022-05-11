<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Interface;

use Stringable;

interface QueryBuilderInterface extends Stringable
{
    /**
     * Return the current query as a string.
     */
    public function getQuery(): string;

    /**
     * Return current query parameters as array.
     *
     * @return array<int|string,mixed>
     */
    public function getQueryParameters(): array;
}
