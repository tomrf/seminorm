<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Test;

use PHPUnit\Framework\TestCase;
use Tomrf\Seminorm\Factory\Factory;
use Tomrf\Seminorm\Pdo\PdoConnection;
use Tomrf\Seminorm\Pdo\PdoQueryExecutor;
use Tomrf\Seminorm\QueryBuilder\QueryBuilder;
use Tomrf\Seminorm\Seminorm;

/**
 * @internal
 * @covers \Tomrf\Seminorm\QueryBuilder\QueryBuilder
 * @covers \Tomrf\Seminorm\Sql\SqlCompiler
 */
final class QueryBuilderTest extends AbstractTestCase
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

    public function test_querybuilder_sql_statement_syntax__update(): void
    {
        $queryBuilder = new QueryBuilder();

        $queryBuilder = $queryBuilder
            ->update('table')
            ->set('column', 'value')
            ->set('column2', 'value2')
            ->setRaw('column3', 'RAW_EXPRESSION')
            ->whereEqual('id', 1000)
        ;

        static::assertSame(
            'UPDATE `table` SET `column` = ?, `column2` = ?, `column3` = RAW_EXPRESSION WHERE `id` = ?',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues(
            ['value', 'value2', 1000],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__insert(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->insertInto('table')
            ->set('column', 'value')
            ->set('column2', 'value2')
            ->setRaw('column3', 'RAW_EXPRESSION')
        ;
        static::assertSame(
            'INSERT INTO `table` (`column`, `column2`, `column3`) VALUES (?, ?, RAW_EXPRESSION)',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues(['value', 'value2'],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__select_with_join(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->select('table.id', 'table2.id')
            ->join('table2', 'table.table2_id = table2.id')
            ->where('table2.value', '>', 50)
        ;
        static::assertSame(
            'SELECT `table`.`id`, `table2`.`id` FROM `table` JOIN `table2` ON `table`.`table2_id`=`table2`.`id` WHERE `table2`.`value` > ?',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues(
            [50],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__select_distinct(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->selectDistinct('table.id', 'table2.id')
            ->orderByAsc('table.id')
        ;

        static::assertSame(
            'SELECT DISTINCT `table`.`id`, `table2`.`id` FROM `table` ORDER BY `table`.`id` ASC',
            $queryBuilder->getQuery()
        );

        static::assertArrayHasValues([],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__select_with_limit(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->select('table.id', 'table2.id')
            ->join('table2', 'table.table2_id = table2.id')
            ->where('table2.value', '>', 50)
            ->limit(10)
        ;
        static::assertSame(
            'SELECT `table`.`id`, `table2`.`id` FROM `table` JOIN `table2` ON `table`.`table2_id`=`table2`.`id` WHERE `table2`.`value` > ? LIMIT 10',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues(
            [50],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__select_with_limit_and_offset(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->select('table.id', 'table2.id')
            ->join('table2', 'table.table2_id = table2.id')
            ->where('table2.value', '>', 50)
            ->limit(10)
            ->offset(5)
        ;
        static::assertSame(
            'SELECT `table`.`id`, `table2`.`id` FROM `table` JOIN `table2` ON `table`.`table2_id`=`table2`.`id` WHERE `table2`.`value` > ? LIMIT 10 OFFSET 5',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues(
            [50],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__select_with_order_by(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->select('table.id', 'table2.id')
            ->join('table2', 'table.table2_id = table2.id')
            ->where('table2.value', '>', 50)
            ->orderByAsc('table.id')
            ->orderByDesc('table2.id')
        ;
        static::assertSame(
            'SELECT `table`.`id`, `table2`.`id` FROM `table` JOIN `table2` ON `table`.`table2_id`=`table2`.`id` WHERE `table2`.`value` > ? ORDER BY `table`.`id` ASC, `table2`.`id` DESC',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([50],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__select_with_group_by(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->select('table.id', 'table2.id')
            ->join('table2', 'table.table2_id = table2.id')
            ->where('table2.value', '>', 50)
            ->groupBy('table.id')
            ->groupBy('table2.id')
        ;
        static::assertSame(
            'SELECT `table`.`id`, `table2`.`id` FROM `table` JOIN `table2` ON `table`.`table2_id`=`table2`.`id` WHERE `table2`.`value` > ? GROUP BY `table`.`id`, `table2`.`id`',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([50],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__select_with_having(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->select('table.id', 'table2.id')
            ->join('table2', 'table.table2_id = table2.id')
            ->where('table2.value', '>', 50)
            ->groupBy('table.id')
            ->groupBy('table2.id')
            ->having('table.id', '>', 10)
            ->having('table2.id', '>', 20)
        ;
        static::assertSame(
            'SELECT `table`.`id`, `table2`.`id` FROM `table` JOIN `table2` ON `table`.`table2_id`=`table2`.`id` WHERE `table2`.`value` > ? GROUP BY `table`.`id`, `table2`.`id` HAVING `table`.`id` > ? AND `table2`.`id` > ?',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues(
            [50, 10, 20],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__select_with_having_raw(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->select('table.id', 'table2.id')
            ->join('table2', 'table.table2_id = table2.id')
            ->where('table2.value', '>', 50)
            ->groupBy('table.id')
            ->groupBy('table2.id')
            ->havingRaw('table.id > 10')
            ->havingRaw('table2.id > 20')
        ;
        static::assertSame(
            'SELECT `table`.`id`, `table2`.`id` FROM `table` JOIN `table2` ON `table`.`table2_id`=`table2`.`id` WHERE `table2`.`value` > ? GROUP BY `table`.`id`, `table2`.`id` HAVING table.id > 10 AND table2.id > 20',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([50],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__delete(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->deleteFrom('table')
            ->whereNotEqual('id', 1)
            ->whereNotNull('immutable')
        ;
        static::assertSame(
            'DELETE FROM `table` WHERE `id` != ? AND `immutable` IS NOT NULL',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([1],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__where_variants(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->where('active', '=', 1)
            ->whereColumnRaw('columnRaw', 'COLUMN_RAW')
            ->whereEqual('columnEqual', 'valueEqual')
            ->whereNotEqual('columnNotEqual', 'valueNotEqual')
            ->whereNotNull('columnNotNull')
            ->whereNull('columnNull')
            ->whereRaw('WHERE_RAW = TEST')
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` WHERE `active` = ? AND `columnRaw` COLUMN_RAW AND `columnEqual` = ? AND `columnNotEqual` != ? AND `columnNotNull` IS NOT NULL AND `columnNull` IS NULL AND WHERE_RAW = TEST',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([1, 'valueEqual', 'valueNotEqual'],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__where_in(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->whereIn('column', [1, 2, 3])
            ->whereNotIn('columnNotIn', [4, 5, 6])
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` WHERE `column` IN (?, ?, ?) AND `columnNotIn` NOT IN (?, ?, ?)',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([1, 2, 3, 4, 5, 6],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__where_between(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->whereBetween('column', 1, 3)
            ->whereNotBetween('columnNotBetween', 4, 6)
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` WHERE `column` BETWEEN ? AND ? AND `columnNotBetween` NOT BETWEEN ? AND ?',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([1, 3, 4, 6],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__where_like(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->whereLike('column', 'value')
            ->whereNotLike('columnNotLike', 'valueNotLike')
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` WHERE `column` LIKE ? AND `columnNotLike` NOT LIKE ?',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues(['value', 'valueNotLike'], $queryBuilder->getQueryParameters());
    }

    public function test_querybuilder_sql_statement_syntax__where_not_in(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->whereNotIn('column', [1, 2, 3])
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` WHERE `column` NOT IN (?, ?, ?)',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([1, 2, 3],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__where_not_between(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->whereNotBetween('column', 1, 3)
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` WHERE `column` NOT BETWEEN ? AND ?',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([1, 3],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__where_not_like(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->whereNotLike('column', 'value')
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` WHERE `column` NOT LIKE ?',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues(['column' => 'value'],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__joins(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->join('table2', 'table.table2_id = table2.id')
            ->join('table3', 'table.table3_id = table3.id', 'LEFT')
            ->join('table4', 'table.table4_id = table4.id', 'right')
            ->join('table5', 'table.table5_id = table5.id', 'INNER LEFT')
            ->join('table6', 'table.table6_id = table6.id', 'INNER RIGHT')
            ->join('table7', 'table.table7_id = table7.id', 'CROSS')
        ;

        static::assertSame(
            'SELECT `table`.* FROM `table` JOIN `table2` ON `table`.`table2_id`=`table2`.`id` LEFT JOIN `table3` ON `table`.`table3_id`=`table3`.`id` RIGHT JOIN `table4` ON `table`.`table4_id`=`table4`.`id` INNER LEFT JOIN `table5` ON `table`.`table5_id`=`table5`.`id` INNER RIGHT JOIN `table6` ON `table`.`table6_id`=`table6`.`id` CROSS JOIN `table7` ON `table`.`table7_id`=`table7`.`id`',
            $queryBuilder->getQuery()
        );

        static::assertArrayHasValues([],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__group_by(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->groupBy('column')
            ->groupBy('column2')
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` GROUP BY `column`, `column2`',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__order_by(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->orderByAsc('column')
            ->orderByDesc('column2')
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` ORDER BY `column` ASC, `column2` DESC',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__limit(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->limit(10)
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` LIMIT 10',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__limit_and_offset(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->limit(10)
            ->offset(10)
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` LIMIT 10 OFFSET 10',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__limit_and_offset__with_order_by(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->orderByAsc('column')
            ->limit(10)
            ->offset(10)
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` ORDER BY `column` ASC LIMIT 10 OFFSET 10',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__limit_and_offset__with_group_by(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->groupBy('column')
            ->limit(10)
            ->offset(10)
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` GROUP BY `column` LIMIT 10 OFFSET 10',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__limit_and_offset__with_group_by_and_order_by(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->groupBy('column')
            ->orderByAsc('column')
            ->limit(10)
            ->offset(10)
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` GROUP BY `column` ORDER BY `column` ASC LIMIT 10 OFFSET 10',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__limit_and_offset__with_group_by_and_order_by_and_join(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->groupBy('column')
            ->orderByAsc('column')
            ->join('table2', 'table.table2_id = table2.id')
            ->limit(10)
            ->offset(10)
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` JOIN `table2` ON `table`.`table2_id`=`table2`.`id` GROUP BY `column` ORDER BY `column` ASC LIMIT 10 OFFSET 10',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__limit_and_offset__with_group_by_and_order_by_and_join_and_where(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->groupBy('column')
            ->orderByAsc('column')
            ->join('table2', 'table.table2_id = table2.id')
            ->whereEqual('column', 'value')
            ->limit(10)
            ->offset(10)
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` JOIN `table2` ON `table`.`table2_id`=`table2`.`id` WHERE `column` = ? GROUP BY `column` ORDER BY `column` ASC LIMIT 10 OFFSET 10',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([
                'column' => 'value',
            ],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__limit_and_offset__with_group_by_and_order_by_and_join_and_where_and_having(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->groupBy('column')
            ->orderByAsc('column')
            ->join('table2', 'table.table2_id = table2.id')
            ->whereEqual('column', 'value')
            ->having('column', '=', 'value')
            ->limit(10)
            ->offset(10)
        ;
        static::assertSame(
            'SELECT `table`.* FROM `table` JOIN `table2` ON `table`.`table2_id`=`table2`.`id` WHERE `column` = ? GROUP BY `column` HAVING `column` = ? ORDER BY `column` ASC LIMIT 10 OFFSET 10',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([
                'column' => 'value',
            ],
            $queryBuilder->getQueryParameters()
        );
    }

    public function test_querybuilder_sql_statement_syntax__limit_and_offset__with_group_by_and_order_by_and_join_and_where_and_having_and_select(): void
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder = $queryBuilder
            ->selectFrom('table')
            ->groupBy('column')
            ->orderByAsc('column')
            ->join('table2', 'table.table2_id = table2.id')
            ->whereEqual('column', 'value')
            ->having('column', '=', 'value')
            ->select('column')
            ->limit(10)
            ->offset(10)
        ;
        static::assertSame(
            'SELECT `column` FROM `table` JOIN `table2` ON `table`.`table2_id`=`table2`.`id` WHERE `column` = ? GROUP BY `column` HAVING `column` = ? ORDER BY `column` ASC LIMIT 10 OFFSET 10',
            $queryBuilder->getQuery()
        );
        static::assertArrayHasValues([
                'column' => 'value',
            ],
            $queryBuilder->getQueryParameters()
        );
    }
}
