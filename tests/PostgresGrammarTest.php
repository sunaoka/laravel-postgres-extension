<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests;

use Illuminate\Support\Facades\DB;
use Sunaoka\LaravelPostgres\Tests\Models\TestModel;

class PostgresGrammarTest extends TestCase
{
    public function testCompileUpdate(): void
    {
        $builder = DB::table((new TestModel())->getTable());

        $actual = $builder->getGrammar()->compileUpdate(
            $builder->where('id', 1)->returning(['*']),
            ['x' => 1]
        );

        $expected = 'update "tests" set "x" = ? where "id" = ? returning *';

        self::assertSame($expected, $actual);
    }

    public function testCompileUpdateWithoutReturning(): void
    {
        $builder = DB::table((new TestModel())->getTable());

        $actual = $builder->getGrammar()->compileUpdate(
            $builder->where('id', 1),
            ['x' => 1]
        );

        $expected = 'update "tests" set "x" = ? where "id" = ?';

        self::assertSame($expected, $actual);
    }

    public function testCompileDelete(): void
    {
        $builder = DB::table((new TestModel())->getTable());

        $actual = $builder->getGrammar()->compileDelete(
            $builder->where('id', 1)->returning(['*'])
        );

        $expected = 'delete from "tests" where "id" = ? returning *';

        self::assertSame($expected, $actual);
    }

    public function testCompileDeleteWithoutReturning(): void
    {
        $builder = DB::table((new TestModel())->getTable());

        $actual = $builder->getGrammar()->compileDelete(
            $builder->where('id', 1)
        );

        $expected = 'delete from "tests" where "id" = ?';

        self::assertSame($expected, $actual);
    }
}
