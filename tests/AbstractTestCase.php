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
 * @coversNothing
 */
abstract class AbstractTestCase extends TestCase
{
    protected function newSeminormInstance(bool $establishConnection = true): Seminorm
    {
        $seminorm = new Seminorm(
            new PdoConnection(
                PdoConnection::dsn('sqlite', ':memory:'),
            ),
            new Factory(QueryBuilder::class),
            new Factory(PdoQueryExecutor::class),
        );

        if (true === $establishConnection) {
            $seminorm->getConnection()->connect();
        }

        return $seminorm;
    }

    protected function newSeminormInstanceWithData(): Seminorm
    {
        $seminorm = $this->newSeminormInstance(true);
        $seminorm->execute(file_get_contents('tests/sql/countries_schema.sql'))->getRowCount();
        $seminorm->execute(file_get_contents('tests/sql/countries_data.sql'))->getRowCount();

        return $seminorm;
    }

    protected function assertArrayHasValues(array $expected, array $actual): void
    {
        $notFound = $expected;

        foreach ($actual as $actualValue) {
            foreach ($expected as $expectedKey => $expectedValue) {
                if ($expectedValue === $actualValue) {
                    unset($notFound[$expectedKey]);
                }
            }
        }

        if (0 !== count($notFound)) {
            $this->fail(sprintf('The following values were not found in array: %s', implode(', ', $notFound)));
        }
    }
}
