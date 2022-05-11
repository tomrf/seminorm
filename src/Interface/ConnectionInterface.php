<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Interface;

interface ConnectionInterface
{
    public function isConnected(): bool;
}
