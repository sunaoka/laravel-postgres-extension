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
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/postgres-extension.php', 'postgres-extension'
        );

        $this->app->bind('db.connector.pgsql', PostgresConnector::class);

        Connection::resolverFor('pgsql', function (\PDO|\Closure $connection, string $database, string $prefix, array $config) {
            return new PostgresConnection($connection, $database, $prefix, $config);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/postgres-extension.php' => config_path('postgres-extension.php'),
        ], 'postgres-extension');
    }
}
