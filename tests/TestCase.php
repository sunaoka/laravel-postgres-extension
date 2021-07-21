<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Sunaoka\LaravelPostgres\PostgresServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            PostgresServiceProvider::class,
        ];
    }
}
