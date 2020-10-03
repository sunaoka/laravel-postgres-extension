<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres;

use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class PostgresServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Connection::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
            return new PostgresConnection($connection, $database, $prefix, $config);
        });
    }
}
