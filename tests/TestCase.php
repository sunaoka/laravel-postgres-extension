<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Processors\Processor;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Sunaoka\LaravelPostgres\Eloquent\Builder as EloquentBuilder;
use Sunaoka\LaravelPostgres\Eloquent\Model;
use Sunaoka\LaravelPostgres\PostgresServiceProvider;
use Sunaoka\LaravelPostgres\Query\Builder as QueryBuilder;
use Sunaoka\LaravelPostgres\Query\Grammars\PostgresGrammar;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            PostgresServiceProvider::class,
        ];
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        $connection = \Mockery::mock(Connection::class);
        $connection->shouldReceive('getDatabaseName')->andReturn('database');
        $connection->shouldReceive('getName')->andReturn('pgsql');
        $connection->shouldReceive('getTablePrefix')->andReturn('');

        $grammar = new PostgresGrammar($connection);
        $processor = \Mockery::mock(Processor::class);

        return new QueryBuilder($connection, $grammar, $processor);
    }

    /**
     * @return EloquentBuilder<Model>
     */
    protected function getEloquentBuilder(): EloquentBuilder
    {
        return new EloquentBuilder($this->getQueryBuilder());
    }
}
