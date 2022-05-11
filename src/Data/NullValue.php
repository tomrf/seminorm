<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Data;

class NullValue
{
    public function isNull(): bool
    {
        return true;
    }
}
