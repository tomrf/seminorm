<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Tomrf\Seminorm\Factory\Factory;
use Tomrf\Seminorm\Pdo\PdoConnection;
use Tomrf\Seminorm\Pdo\PdoQueryExecutor;
use Tomrf\Seminorm\QueryBuilder\QueryBuilder;
use Tomrf\Seminorm\Seminorm;

/**
 * @internal
 * @coversNothing
 */
final class PdoConnectionTest extends TestCase
{
    private static Seminorm $seminorm;

    public static function setUpBeforeClass(): void
    {
        self::$seminorm = new Seminorm(
            new PdoConnection(
                PdoConnection::dsn('sqlite', ':memory:'),
                null,
                null,
                null,
            ),
            new Factory(QueryBuilder::class),
            new Factory(PdoQueryExecutor::class),
        );
        // self::$seminorm->getConnection()->connect();
    }

    public function test_not_connected_yet(): void
    {
        static::assertFalse(self::$seminorm->getConnection()->isConnected());
    }

    public function test_connect_after_create(): void
    {
        self::$seminorm->getConnection()->connect();
        static::assertTrue(self::$seminorm->getConnection()->isConnected());
    }

    public function test_get_pdo_object(): void
    {
        static::assertInstanceOf(PDO::class, self::$seminorm->getConnection()->getPdo());
    }

    public function test_disconnect(): void
    {
        self::$seminorm->getConnection()->disconnect();
        static::assertFalse(self::$seminorm->getConnection()->isConnected());
    }
}
