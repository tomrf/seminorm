<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Test;

use Tomrf\Seminorm\Factory\Factory;
use Tomrf\Seminorm\Pdo\PdoConnection;
use Tomrf\Seminorm\Pdo\PdoQueryExecutor;
use Tomrf\Seminorm\QueryBuilder\QueryBuilder;
use Tomrf\Seminorm\Seminorm;

/**
 * Gives 100% coverage of \Tomrf\Seminorm\QueryBuilder\Trait\SelectMethodsTrait
 *
 * All tests are run against in-memory sqlite3.
 *
 * @internal
 * @covers \Tomrf\Seminorm\QueryBuilder\QueryBuilder
 * @covers \Tomrf\Seminorm\Sql\SqlCompiler
 * @covers \Tomrf\Seminorm\QueryBuilder\Trait\SelectMethodsTrait
 * @covers \Tomrf\Seminorm\Pdo\PdoConnection
 * @covers \Tomrf\Seminorm\Pdo\PdoQueryExecutor
 * @covers \Tomrf\Seminorm\Factory\Factory
 * @covers \Tomrf\Seminorm\Seminorm
 */
final class QueryBuilderSelectTest extends AbstractTestCase
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
        );

        self::$seminorm->getConnection()->connect();

        $sql = file_get_contents('tests/sql/countries_schema.sql');
        self::$seminorm->execute($sql)->getRowCount();

        $sql = file_get_contents('tests/sql/countries_data.sql');
        self::$seminorm->execute($sql)->getRowCount();
    }

    public function test_constructor(): void
    {
        static::assertInstanceOf(QueryBuilder::class, new QueryBuilder());
    }

    // simple select of column(s)
    public function test_select(): void
    {
        // select id, name from countries, assert more than 100 rows, assert id and name columns exist
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries', 'id', 'name'))->getRows();
        static::assertGreaterThan(100, count($rows));
        static::assertArrayHasKey('id', $rows[0]);
        static::assertArrayHasKey('name', $rows[0]);

        // select id, name from countries limit 1, assert 1 row, assert id and name columns exist
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries', 'id', 'name')->limit(1))->getRows();
        static::assertCount(1, $rows);
        static::assertArrayHasKey('id', $rows[0]);
        static::assertArrayHasKey('name', $rows[0]);
    }

    public function test_select_raw(): void
    {
        // simple select of static value
        $staticValues = [ 0, 1, '"test"', '"testing \'strings\'"', 123.456, 0.00001 ];
        foreach ($staticValues as $staticValue) {
            try {
                static::assertSame(
                    is_string($staticValue) ? trim(sprintf('%s', $staticValue), '"') : $staticValue,
                    self::$seminorm->execute(self::$seminorm->query()->selectRaw(sprintf('%s', $staticValue)))->getColumn()
                );
            } catch (\Throwable $e) {
                throw new \Exception(sprintf('Failed to select static value "%s"', $staticValue), 0, $e);
            }
        }
    }

    public function test_select_as(): void
    {
        // simple SELECT AS of column(s)
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectAs('id', 'country_id')->selectAs('name', 'country_name'))->getRows();
        static::assertGreaterThan(100, count($rows));
        static::assertArrayHasKey('country_id', $rows[0]);
        static::assertArrayHasKey('country_name', $rows[0]);

        // simple SELECT AS of static value
        $staticValues = [ 0, 1, '"test"', '"testing \'strings\'"', 123.456, 0.00001 ];
        foreach ($staticValues as $staticValue) {
            try {
                static::assertSame(
                    is_string($staticValue) ? trim(sprintf('%s', $staticValue), '"') : $staticValue,
                    self::$seminorm->execute(self::$seminorm->query()->selectRaw(sprintf('%s', $staticValue))->selectAs(sprintf('%s', $staticValue), 'static_value'))->getColumn()
                );
            } catch (\Throwable $e) {
                throw new \Exception(sprintf('Failed to select static value "%s"', $staticValue), 0, $e);
            }
        }

        // simple SELECT AS of static value with alias
        $staticValues = [ 0, 1, '"test"', '"testing \'strings\'"', 123.456, 0.00001 ];
        foreach ($staticValues as $staticValue) {
            try {
                static::assertSame(
                    is_string($staticValue) ? trim(sprintf('%s', $staticValue), '"') : $staticValue,
                    self::$seminorm->execute(self::$seminorm->query()->selectRaw(sprintf('%s', $staticValue))->selectAs(sprintf('%s', $staticValue), 'static_value'))->getColumn()
                );
            } catch (\Throwable $e) {
                throw new \Exception(sprintf('Failed to select static value "%s"', $staticValue), 0, $e);
            }
        }
    }

    public function test_select_raw_as(): void
    {
        // simple SELECT AS of static value
        $staticValues = [ 0, 1, '"test"', '"testing \'strings\'"', 123.456, 0.00001 ];
        foreach ($staticValues as $staticValue) {
            try {
                static::assertSame(
                    is_string($staticValue) ? trim(sprintf('%s', $staticValue), '"') : $staticValue,
                    self::$seminorm->execute(self::$seminorm->query()->selectRaw(sprintf('%s', $staticValue))->selectRawAs(sprintf('%s', $staticValue), 'static_value'))->getColumn()
                );
            } catch (\Throwable $e) {
                throw new \Exception(sprintf('Failed to select static value "%s"', $staticValue), 0, $e);
            }
        }

        // simple SELECT AS of static value with alias
        $staticValues = [ 0, 1, '"test"', '"testing \'strings\'"', 123.456, 0.00001 ];
        foreach ($staticValues as $staticValue) {
            try {
                static::assertSame(
                    is_string($staticValue) ? trim(sprintf('%s', $staticValue), '"') : $staticValue,
                    self::$seminorm->execute(self::$seminorm->query()->selectRaw(sprintf('%s', $staticValue))->selectRawAs(sprintf('%s', $staticValue), 'static_value'))->getColumn()
                );
            } catch (\Throwable $e) {
                throw new \Exception(sprintf('Failed to select static value "%s"', $staticValue), 0, $e);
            }
        }
    }

    public function test_select_where(): void
    {
        // simple SELECT .. WHERE
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->whereEqual('id', 1))->getRows();
        static::assertCount(1, $rows);

        // simple SELECT .. WHERE .. AND
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->whereEqual('id', 1)->whereEqual('name', 'Afghanistan'))->getRows();
        static::assertCount(1, $rows);

        // simple SELECT .. WHERE .. AND .. AND
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->whereEqual('id', 1)->whereEqual('name', 'Afghanistan')->whereEqual('code', 'AF'))->getRows();

        // WHERE NOT
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->whereNotEqual('id', 1))->getRows();
        static::assertGreaterThan(100, count($rows));

        // WHERE IN
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->whereIn('id', [ 1, 2, 3 ]))->getRows();
        static::assertCount(3, $rows);

        // WHERE NOT IN
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->whereNotIn('id', [ 1, 2, 3 ]))->getRows();
        static::assertGreaterThan(100, count($rows));

        // WHERE LIKE
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->whereLike('name', 'Afghanistan'))->getRows();
        static::assertCount(1, $rows);

        // WHERE NOT LIKE
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->whereNotLike('name', 'Afghanistan'))->getRows();
        static::assertGreaterThan(100, count($rows));

        // WHERE IS NOT NULL
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->whereNotNull('name'))->getRows();
        static::assertGreaterThan(100, count($rows));

        // WHERE BETWEEN
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->whereBetween('id', 1, 3))->getRows();
        static::assertCount(3, $rows);

    }

    public function test_select_distinct(): void
    {
        // simple SELECT DISTINCT
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectDistinct('id'))->getRows();
        static::assertGreaterThan(100, count($rows));

        // simple SELECT DISTINCT .. WHERE
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectDistinct('id')->whereEqual('id', 1))->getRows();
        static::assertCount(1, $rows);
    }

    public function test_select_distinct_as(): void
    {
        // simple SELECT DISTINCT AS
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectDistinctAs('id', 'id'))->getRows();
        static::assertGreaterThan(100, count($rows));

        // simple SELECT DISTINCT AS .. WHERE
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectDistinctAs('id', 'id')->whereEqual('id', 1))->getRows();
        static::assertCount(1, $rows);
    }

    public function test_select_distinct_raw(): void
    {
        // simple SELECT DISTINCT
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectDistinctRaw('id'))->getRows();
        static::assertGreaterThan(100, count($rows));

        // simple SELECT DISTINCT .. WHERE
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectDistinctRaw('id')->whereEqual('id', 1))->getRows();
        static::assertCount(1, $rows);
    }

    public function test_select_distinct_raw_as(): void
    {
        // simple SELECT DISTINCT AS
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectDistinctRawAs('id', 'id'))->getRows();
        static::assertGreaterThan(100, count($rows));

        // simple SELECT DISTINCT AS .. WHERE
        $rows = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectDistinctRawAs('id', 'id')->whereEqual('id', 1))->getRows();
        static::assertCount(1, $rows);
    }

    public function test_select_count_min_max_avg_sum(): void
    {
        // count
        $count = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectCount('id'))->getColumn();
        static::assertGreaterThan(100, $count);

        // min
        $min = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectMin('id'))->getColumn();
        static::assertSame(1, $min);

        // max
        $max = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectMax('id'))->getColumn();
        static::assertGreaterThan(100, $max);

        // avg
        $avg = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectAvg('id'))->getColumn();
        static::assertGreaterThan(100, $avg);

        // sum
        $sum = self::$seminorm->execute(self::$seminorm->query()->selectFrom('countries')->selectSum('id'))->getColumn();
        static::assertGreaterThan(100, $sum);

    }
}

