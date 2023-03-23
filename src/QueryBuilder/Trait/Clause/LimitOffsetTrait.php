<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\QueryBuilder\Trait\Clause;

use InvalidArgumentException;

trait LimitOffsetTrait
{

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

}
