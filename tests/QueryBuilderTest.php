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
final class QueryBuilderTest extends TestCase
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
        static::assertSame(
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
        static::assertSame(
            ['value', 'value2'],
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
        static::assertSame(
            [50],
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
        static::assertSame(
            [1],
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
        static::assertSame(
            [1, 'valueEqual', 'valueNotEqual'],
            $queryBuilder->getQueryParameters()
        );
    }
}
