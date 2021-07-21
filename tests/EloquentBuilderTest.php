<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests;

use Carbon\Carbon;
use Mockery;
use Sunaoka\LaravelPostgres\Eloquent\Builder;
use Sunaoka\LaravelPostgres\PostgresConnection;
use Sunaoka\LaravelPostgres\Query\Builder as QueryBuilder;
use Sunaoka\LaravelPostgres\Query\Grammars\PostgresGrammar;
use Sunaoka\LaravelPostgres\Tests\Models\TestModel;

class EloquentBuilderTest extends TestCase
{
    private const NOW = '2021-07-21 19:38:17';

    /**
     * @var Mockery\Mock|PostgresConnection
     */
    private $connection;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(self::NOW);

        $this->connection = Mockery::mock(PostgresConnection::class)->makePartial();
    }

    public function testUpdate(): void
    {
        $x = 10;
        $expected = 1;

        $this->connection->shouldReceive('update')
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('update "tests" set "x" = ?, "updated_at" = ?', $query);
                self::assertSame([$x, self::NOW], $bindings);
                return true;
            })
            ->andReturn($expected);

        $builder = new Builder(new QueryBuilder($this->connection, new PostgresGrammar()));
        $builder->setModel(TestModel::make());
        $actual = $builder->update(['x' => $x]);

        self::assertSame($expected, $actual);
    }

    public function testUpdateWithReturning(): void
    {
        $x = 10;
        $expected = 1;

        $this->connection->shouldReceive('select')
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('update "tests" set "x" = ?, "updated_at" = ? returning *', $query);
                self::assertSame([$x, self::NOW], $bindings);
                return true;
            })
            ->andReturn([['id' => $expected]]);

        $builder = new Builder(new QueryBuilder($this->connection, new PostgresGrammar()));
        $builder->setModel(TestModel::make());
        $actual = $builder->returning(['*'])->update(['x' => $x]);

        $model = $actual->first();

        self::assertInstanceOf(TestModel::class, $model);
        self::assertSame($expected, $model->id);
    }


    public function testDelete(): void
    {
        $x = 10;
        $expected = 1;

        $this->connection->shouldReceive('delete')
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('delete from "tests" where "x" = ?', $query);
                self::assertSame([$x], $bindings);
                return true;
            })
            ->andReturn($expected);

        $builder = new Builder(new QueryBuilder($this->connection, new PostgresGrammar()));
        $builder->setModel(TestModel::make());
        $actual = $builder->where('x', $x)->delete();

        self::assertSame($expected, $actual);
    }

    public function testDeleteWithReturning(): void
    {
        $x = 10;
        $expected = 1;

        $this->connection->shouldReceive('select')
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('delete from "tests" where "x" = ? returning *', $query);
                self::assertSame([$x], $bindings);
                return true;
            })
            ->andReturn([['id' => $expected]]);

        $builder = new Builder(new QueryBuilder($this->connection, new PostgresGrammar()));
        $builder->setModel(TestModel::make());
        $actual = $builder->returning(['*'])->where('x', $x)->delete();

        $model = $actual->first();

        self::assertInstanceOf(TestModel::class, $model);
        self::assertSame($expected, $model->id);
    }
}
