<?php

namespace Sunaoka\LaravelPostgres\Connectors;

use Illuminate\Support\Facades\Config;

class PostgresConnector extends \Illuminate\Database\Connectors\PostgresConnector
{
    /**
     * Create a DSN string from a configuration.
     *
     * @param  array  $config
     * @return string
     */
    protected function getDsn(array $config): string
    {
        $dsn = parent::getDsn($config);

        $dsn .= Config::get('postgres-extension.additional_dns_string');

        return $dsn;
    }
}
