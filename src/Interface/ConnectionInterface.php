<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Interface;

interface ConnectionInterface
{
    /**
     * Connect to the database.
     */
    public function connect(): void;

    /**
     * Disconnect from the database.
     */
    public function disconnect(): void;

    /**
     * Returns true if currently connected to database, false if not.
     */
    public function isConnected(): bool;
}
