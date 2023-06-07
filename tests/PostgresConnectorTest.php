<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests;

use ReflectionException;
use ReflectionMethod;
use Sunaoka\LaravelPostgres\Connectors\PostgresConnector;

class PostgresConnectorTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testGetDsn(): void
    {
        $method = new ReflectionMethod(PostgresConnector::class, 'getDsn');
        $method->setAccessible(true);

        $actual = $method->invoke(new PostgresConnector(), [
            'database' => 'forge',
        ]);

        self::assertSame("pgsql:dbname='forge';application_name='Laravel extended PostgreSQL driver'", $actual);
    }
}
