<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Test;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Tomrf\Seminorm\Data\Row;
use Tomrf\Seminorm\Data\Value;
use Tomrf\Seminorm\Factory\Factory;
use Tomrf\Seminorm\Pdo\PdoConnection;
use Tomrf\Seminorm\Pdo\PdoQueryExecutor;
use Tomrf\Seminorm\QueryBuilder\QueryBuilder;
use Tomrf\Seminorm\Seminorm;

/**
 * @internal
 * @covers \Tomrf\Seminorm\Data\ImmutableArrayObject
 * @covers \Tomrf\Seminorm\Data\NullValue
 * @covers \Tomrf\Seminorm\Data\Row
 * @covers \Tomrf\Seminorm\Data\Value
 * @covers \Tomrf\Seminorm\Factory\Factory
 * @covers \Tomrf\Seminorm\Pdo\PdoConnection
 * @covers \Tomrf\Seminorm\Pdo\PdoQueryExecutor
 * @covers \Tomrf\Seminorm\QueryBuilder\QueryBuilder
 * @covers \Tomrf\Seminorm\Seminorm
 * @covers \Tomrf\Seminorm\Sql\SqlCompiler
 */
final class SeminormTest extends TestCase
{
    private static Seminorm $seminorm;

    public static function setUpBeforeClass(): void
    {
        self::$seminorm = new Seminorm(
            new PdoConnection(
                PdoConnection::dsn('sqlite', ':memory:')
            ),
            new Factory(QueryBuilder::class),
            new Factory(PdoQueryExecutor::class),
            Row::class,
            Value::class,
        );

        self::$seminorm->getConnection()->connect();

        $sql = file_get_contents('tests/sql/countries_schema.sql');
        self::$seminorm->execute($sql)->getRowCount();

        $sql = file_get_contents('tests/sql/countries_data.sql');
        self::$seminorm->execute($sql)->getRowCount();
    }

    public function test_constructor(): void
    {
        static::assertInstanceOf(Seminorm::class, new Seminorm(
            new PdoConnection(PdoConnection::dsn('sqlite', ':memory:')),
            new Factory(QueryBuilder::class),
            new Factory(PdoQueryExecutor::class),
        ));
    }

    public function test_connection_is_connected(): void
    {
        static::assertTrue(
            self::$seminorm->getConnection()->isConnected()
        );
    }

    public function test_select_all_find_one_returns_instance_of_row(): void
    {
        $row = self::$seminorm->execute(
            self::$seminorm->query()->selectFrom('countries')
        )->findOne();

        static::assertInstanceOf(Row::class, $row);
    }

    public function test_select_all_find_many_returns_array_of_row(): void
    {
        $rows = self::$seminorm->execute(
            self::$seminorm->query()->selectFrom('countries')
        )->findMany();

        static::assertIsArray($rows);
        static::assertContainsOnlyInstancesOf(Row::class, $rows);
    }

    public function test_select_find_many_limit1_returns_array_of_one_row(): void
    {
        $rows = self::$seminorm->execute(
            self::$seminorm->query()
                ->selectFrom('countries')
                ->limit(1)
        )->findMany();

        static::assertIsArray($rows);
        static::assertCount(1, $rows);
        static::assertContainsOnlyInstancesOf(Row::class, $rows);
    }

    public function test_unspecified_select_returns_all_columns(): void
    {
        $columns = ['id', 'phone', 'code', 'name', 'symbol', 'currency', 'continent', 'continent_code'];

        $row = self::$seminorm->execute(
            self::$seminorm->query()
                ->selectFrom('countries')
        )->findOne();

        foreach ($columns as $column) {
            static::assertArrayHasKey($column, $row);
        }
    }

    public function test_select_as(): void
    {
        $row = self::$seminorm->execute(
            self::$seminorm->query()
                ->selectFrom('countries')
                ->selectAs('symbol', 'currency_symbol')
        )->findOne();

        static::assertArrayHasKey('currency_symbol', $row);
    }

    public function test_select_raw(): void
    {
        $row = self::$seminorm->execute(
            self::$seminorm->query()
                ->selectFrom('countries')
                ->selectRaw('COUNT()', 'RANDOM()', '"string"')
        )->findOne();

        if ($row['COUNT()'] instanceof Value) {
            static::assertSame($row['COUNT()']->asInt(), 252);
            static::assertSame($row['"string"']->asString(), 'string');
        } else {
            static::assertSame($row['COUNT()'], '252');
            static::assertSame($row['"string"'], 'string');
        }

        static::assertArrayHasKey('RANDOM()', $row);
    }

    public function test_select_raw_as(): void
    {
        $row = self::$seminorm->execute(
            self::$seminorm->query()
                ->selectFrom('countries')
                ->selectRawAs('COUNT()', 'number_of_rows')
        )->findOne();

        static::assertArrayHasKey('number_of_rows', $row);
        if ($row['number_of_rows'] instanceof Value) {
            static::assertSame($row['number_of_rows']->asInt(), 252);
        } else {
            static::assertSame($row['number_of_rows'], '252');
        }
    }

    public function test_logger(): void
    {
        self::$seminorm->setLogger(new NullLogger());
        self::$seminorm->execute('SELECT 1');
        static::assertTrue(true);
    }
}
