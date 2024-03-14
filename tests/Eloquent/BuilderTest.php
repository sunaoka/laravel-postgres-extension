<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests\Eloquent;

use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Processors\Processor;
use Mockery;
use Sunaoka\LaravelPostgres\Eloquent\Builder;
use Sunaoka\LaravelPostgres\Query\Builder as QueryBuilder;
use Sunaoka\LaravelPostgres\Query\Grammars\PostgresGrammar;
use Sunaoka\LaravelPostgres\Tests\Models\TestModel;
use Sunaoka\LaravelPostgres\Tests\TestCase;

class BuilderTest extends TestCase
{
    private const NOW = '2021-07-21 19:38:17';

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(self::NOW);
    }

    /**
     * @return Builder<\Sunaoka\LaravelPostgres\Eloquent\Model>
     */
    protected function getBuilder(): Builder
    {
        $connection = Mockery::mock(ConnectionInterface::class);
        $connection->shouldReceive('getDatabaseName')->andReturn('database');
        $connection->shouldReceive('getName')->andReturn('pgsql');

        $grammar = new PostgresGrammar();
        $processor = Mockery::mock(Processor::class);

        return new Builder(new QueryBuilder($connection, $grammar, $processor));
    }

    public function testUpdate(): void
    {
        $x = 10;
        $expected = 1;

        $builder = $this->getBuilder();

        /** @var Mockery\MockInterface $connection */
        $connection = $builder->getConnection();
        $connection->shouldReceive('update')
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('update "tests" set "x" = ?, "updated_at" = ?', $query);
                self::assertSame([$x, self::NOW], $bindings);
                return true;
            })
            ->andReturn($expected);

        $builder->setModel(new TestModel());
        $actual = $builder->update(['x' => $x]);

        self::assertSame($expected, $actual);
    }

    public function testUpdateWithReturning(): void
    {
        $x = 10;
        $expected = 1;

        $builder = $this->getBuilder();

        /** @var Mockery\MockInterface $connection */
        $connection = $builder->getConnection();
        $connection->shouldReceive('select')
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('update "tests" set "x" = ?, "updated_at" = ? returning *', $query);
                self::assertSame([$x, self::NOW], $bindings);
                return true;
            })
            ->andReturn([['id' => $expected]]);

        $builder->setModel(new TestModel());

        $actual = $builder->returning(['*'])->update(['x' => $x]);

        self::assertInstanceOf(Collection::class, $actual);

        $model = $actual->first();

        self::assertInstanceOf(TestModel::class, $model);
        self::assertSame($expected, $model->id);
    }

    public function testDelete(): void
    {
        $x = 10;
        $expected = 1;

        $builder = $this->getBuilder();

        /** @var Mockery\MockInterface $connection */
        $connection = $builder->getConnection();
        $connection->shouldReceive('delete')
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('delete from "tests" where "x" = ?', $query);
                self::assertSame([$x], $bindings);
                return true;
            })
            ->andReturn($expected);

        $builder->setModel(new TestModel());
        $actual = $builder->where('x', $x)->delete();

        self::assertSame($expected, $actual);
    }

    public function testDeleteWithReturning(): void
    {
        $x = 10;
        $expected = 1;

        $builder = $this->getBuilder();

        /** @var Mockery\MockInterface $connection */
        $connection = $builder->getConnection();
        $connection->shouldReceive('select')
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('delete from "tests" where "x" = ? returning *', $query);
                self::assertSame([$x], $bindings);
                return true;
            })
            ->andReturn([['id' => $expected]]);

        $builder->setModel(new TestModel());

        $actual = $builder->returning(['*'])->where('x', $x)->delete();

        self::assertInstanceOf(Collection::class, $actual);

        $model = $actual->first();

        self::assertInstanceOf(TestModel::class, $model);
        self::assertSame($expected, $model->id);
    }
}
