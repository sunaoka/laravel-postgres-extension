<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests\Query\Grammars;

use Illuminate\Support\Facades\DB;
use Sunaoka\LaravelPostgres\Query\Builder;
use Sunaoka\LaravelPostgres\Tests\Models\TestModel;
use Sunaoka\LaravelPostgres\Tests\TestCase;

class PostgresGrammarTest extends TestCase
{
    public function test_compile_update(): void
    {
        /** @var Builder $builder */
        $builder = DB::table((new TestModel)->getTable());

        $actual = $builder->getGrammar()->compileUpdate(
            $builder->where('id', 1)->returning(['*']),
            ['x' => 1]
        );

        $expected = 'update "tests" set "x" = ? where "id" = ? returning *';

        self::assertSame($expected, $actual);
    }

    public function test_compile_update_without_returning(): void
    {
        /** @var Builder $builder */
        $builder = DB::table((new TestModel)->getTable());

        $actual = $builder->getGrammar()->compileUpdate(
            $builder->where('id', 1),
            ['x' => 1]
        );

        $expected = 'update "tests" set "x" = ? where "id" = ?';

        self::assertSame($expected, $actual);
    }

    public function test_compile_delete(): void
    {
        /** @var Builder $builder */
        $builder = DB::table((new TestModel)->getTable());

        $actual = $builder->getGrammar()->compileDelete(
            $builder->where('id', 1)->returning(['*'])
        );

        $expected = 'delete from "tests" where "id" = ? returning *';

        self::assertSame($expected, $actual);
    }

    public function test_compile_delete_without_returning(): void
    {
        /** @var Builder $builder */
        $builder = DB::table((new TestModel)->getTable());

        $actual = $builder->getGrammar()->compileDelete(
            $builder->where('id', 1)
        );

        $expected = 'delete from "tests" where "id" = ?';

        self::assertSame($expected, $actual);
    }

    public function test_prepare_bindings_for_update(): void
    {
        /** @var Builder $builder */
        $builder = DB::table((new TestModel)->getTable());

        $actual = $builder->getGrammar()->prepareBindingsForUpdate(
            $builder->getRawBindings(),
            [
                'a' => 1,
                'b' => [2],
            ],
        );

        self::assertSame([1, '[2]'], $actual);
    }
}
