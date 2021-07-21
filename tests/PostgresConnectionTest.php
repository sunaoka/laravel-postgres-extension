<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests;

use Illuminate\Support\Facades\DB;
use Sunaoka\LaravelPostgres\Schema\PostgresBuilder;

class PostgresConnectionTest extends TestCase
{
    public function testGetSchemaBuilder(): void
    {
        $actual = DB::connection()->getSchemaBuilder();

        self::assertInstanceOf(PostgresBuilder::class, $actual);
    }
}
