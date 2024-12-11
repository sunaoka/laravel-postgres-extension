<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests;

use Illuminate\Support\Facades\DB;
use Sunaoka\LaravelPostgres\Schema\PostgresBuilder;

class PostgresConnectionTest extends TestCase
{
    public function test_get_schema_builder(): void
    {
        $actual = DB::connection()->getSchemaBuilder();

        self::assertInstanceOf(PostgresBuilder::class, $actual);
    }
}
