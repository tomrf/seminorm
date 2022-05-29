<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Test;

use PDO;
use Tomrf\Seminorm\Seminorm;

/**
 * @internal
 * @covers \Tomrf\Seminorm\Factory\Factory
 * @covers \Tomrf\Seminorm\Pdo\PdoConnection
 * @covers \Tomrf\Seminorm\Seminorm
 */
final class PdoConnectionTest extends AbstractTestCase
{
    private static Seminorm $seminorm;

    public static function setUpBeforeClass(): void
    {
        //...
    }

    public function test_new_seminorm_is_instance_of_seminorm(): void
    {
        static::assertInstanceOf(Seminorm::class, $this->newSeminormInstance());
    }

    public function test_new_seminorm_not_connected(): void
    {
        $seminorm = $this->newSeminormInstance(false);
        static::assertFalse($seminorm->getConnection()->isConnected());
    }

    public function test_new_seminorm_is_connected(): void
    {
        $seminorm = $this->newSeminormInstance(true);
        static::assertTrue($seminorm->getConnection()->isConnected());
    }

    public function test_get_pdo_object(): void
    {
        static::assertInstanceOf(PDO::class, $this->newSeminormInstance()->getConnection()->getPdo());
    }

    public function test_disconnect(): void
    {
        $seminorm = $this->newSeminormInstance(true);
        static::assertTrue($seminorm->getConnection()->isConnected());

        $seminorm->getConnection()->disconnect();
        static::assertFalse($seminorm->getConnection()->isConnected());
    }

    public function test_get_options(): void
    {
        static::assertIsArray($this->newSeminormInstance()->getConnection()->getOptions());
    }

    public function test_get_dsn(): void
    {
        static::assertIsString($this->newSeminormInstance()->getConnection()->getDsn());
        static::assertStringContainsString('sqlite', $this->newSeminormInstance()->getConnection()->getDsn());
        static::assertStringContainsString(':memory:', $this->newSeminormInstance()->getConnection()->getDsn());
    }

    public function test_get_username(): void
    {
        static::assertNull($this->newSeminormInstance()->getConnection()->getUsername());
    }
}
