<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests;

use Illuminate\Support\Facades\Config;
use Sunaoka\LaravelPostgres\Connectors\PostgresConnector;

class PostgresConnectorTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function test_get_dsn(): void
    {
        $method = new \ReflectionMethod(PostgresConnector::class, 'getDsn');
        $method->setAccessible(true);

        $actual = $method->invoke(new PostgresConnector, [
            'database' => 'forge',
        ]);

        self::assertSame("pgsql:dbname='forge'", $actual);

        Config::set('postgres-extension.additional_dns_string', ";application_name='Laravel'");

        $actual = $method->invoke(new PostgresConnector, [
            'database' => 'forge',
        ]);

        self::assertSame("pgsql:dbname='forge';application_name='Laravel'", $actual);
    }
}
