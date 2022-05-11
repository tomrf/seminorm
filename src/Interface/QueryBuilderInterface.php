<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Interface;

use Stringable;

interface QueryBuilderInterface extends Stringable
{
    public function getQuery(): string;

    /**
     * @return array<int|string,mixed>
     */
    public function getQueryParameters(): array;
}
