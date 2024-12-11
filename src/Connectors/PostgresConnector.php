<?php

namespace Sunaoka\LaravelPostgres\Connectors;

class PostgresConnector extends \Illuminate\Database\Connectors\PostgresConnector
{
    /**
     * Create a DSN string from a configuration.
     */
    protected function getDsn(array $config): string
    {
        $dsn = parent::getDsn($config);

        $dsn .= config('postgres-extension.additional_dns_string');  // @phpstan-ignore assignOp.invalid

        return $dsn;
    }
}
