<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres;

use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;
use Sunaoka\LaravelPostgres\Connectors\PostgresConnector;

class PostgresServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/postgres-extension.php', 'postgres-extension'
        );

        $this->app->bind('db.connector.pgsql', PostgresConnector::class);

        Connection::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
            return new PostgresConnection($connection, $database, $prefix, $config);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/postgres-extension.php' => config_path('postgres-extension.php'),
        ], 'postgres-extension');
    }
}
