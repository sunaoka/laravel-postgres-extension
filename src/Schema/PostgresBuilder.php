<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Schema;

use Illuminate\Support\Facades\Cache;

class PostgresBuilder extends \Illuminate\Database\Schema\PostgresBuilder
{
    /**
     * Get the column listing for a given table.
     *
     * @param  string  $table
     * @return array
     */
    public function getColumnListing($table)
    {
        if (config('postgres-extension.information_schema_caching') !== true) {
            return parent::getColumnListing($table);
        }

        /** @var array */
        return Cache::rememberForever(
            __METHOD__.$table,
            function () use ($table) {
                return parent::getColumnListing($table);
            }
        );
    }
}
