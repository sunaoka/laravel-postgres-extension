<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests\Query;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Processors\Processor;
use Mockery;
use Sunaoka\LaravelPostgres\Query\Builder;
use Sunaoka\LaravelPostgres\Query\Grammars\PostgresGrammar;
use Sunaoka\LaravelPostgres\Tests\TestCase;

class BuilderTest extends TestCase
{
    protected function getBuilder(): Builder
    {
        $connection = Mockery::mock(ConnectionInterface::class);
        $connection->shouldReceive('getDatabaseName')->andReturn('database');

        $grammar = new PostgresGrammar();
        $processor = Mockery::mock(Processor::class);

        return new Builder($connection, $grammar, $processor);
    }

    public function testReturning(): void
    {
        $builder = $this->getBuilder();
        $builder->from('tests')->returning(['*']);

        self::assertSame(['*'], $builder->returning);
    }

    public function testUpdate(): void
    {
        $x = 10;
        $expected = 1;

        $builder = $this->getBuilder();

        /** @var Mockery\MockInterface $connection */
        $connection = $builder->getConnection();
        $connection->shouldReceive('update')
            ->once()
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('update "tests" set "x" = ?', $query);
                self::assertSame([$x], $bindings);

                return true;
            })
            ->andReturn($expected);

        $actual = $builder->from('tests')->update(['x' => $x]);

        self::assertSame($expected, $actual);
    }

    public function testUpdateSubQuery(): void
    {
        $id = 10;
        $type = 'foo';

        $builder = $this->getBuilder();

        /** @var Mockery\MockInterface $connection */
        $connection = $builder->getConnection();
        $connection->shouldReceive('update')
            ->once()
            ->withArgs(function ($query, $bindings) use ($id, $type) {
                self::assertSame('update "users" set "credits" = (select sum(credits) from "transactions" where "transactions"."user_id" = "users"."id" and "type" = ?) where "id" = ?', $query);
                self::assertSame($type, $bindings[0]()[0]);
                self::assertSame($id, $bindings[1]);

                return true;
            })
            ->andReturn(1);

        $actual = $builder->from('users')
            ->where('id', '=', $id)
            ->update([
                'credits' => $this->getBuilder()->from('transactions')
                    ->selectRaw('sum(credits)')
                    ->whereColumn('transactions.user_id', '=', 'users.id')
                    ->where('type', '=', $type),
            ]);

        $this->assertSame(1, $actual);
    }

    public function testUpdateWithReturning(): void
    {
        $x = 10;
        $expected = 1;

        $builder = $this->getBuilder();

        /** @var Mockery\MockInterface $connection */
        $connection = $builder->getConnection();
        $connection->shouldReceive('select')
            ->once()
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('update "tests" set "x" = ? returning *', $query);
                self::assertSame([$x], $bindings);

                return true;
            })
            ->andReturn(['id' => $expected]);

        $actual = $builder->from('tests')->returning(['*'])->update(['x' => $x]);

        /** @var array $actual */
        self::assertSame($expected, $actual['id']);
    }

    public function testUpdateSubQueryWithReturning(): void
    {
        $id = 10;
        $type = 'foo';

        $builder = $this->getBuilder();

        /** @var Mockery\MockInterface $connection */
        $connection = $builder->getConnection();
        $connection->shouldReceive('select')
            ->once()
            ->withArgs(function ($query, $bindings) use ($id, $type) {
                self::assertSame('update "users" set "credits" = (select sum(credits) from "transactions" where "transactions"."user_id" = "users"."id" and "type" = ?) where "id" = ? returning *', $query);
                self::assertSame($type, $bindings[0]()[0]);
                self::assertSame($id, $bindings[1]);

                return true;
            })
            ->andReturn(1);

        $actual = $builder->from('users')
            ->returning(['*'])
            ->where('id', '=', $id)
            ->update([
                'credits' => $this->getBuilder()->from('transactions')
                    ->selectRaw('sum(credits)')
                    ->whereColumn('transactions.user_id', '=', 'users.id')
                    ->where('type', '=', $type),
            ]);

        $this->assertSame(1, $actual);
    }

    public function testDelete(): void
    {
        $x = 10;
        $expected = 1;

        $builder = $this->getBuilder();

        /** @var Mockery\MockInterface $connection */
        $connection = $builder->getConnection();
        $connection->shouldReceive('delete')
            ->once()
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('delete from "tests" where "x" = ?', $query);
                self::assertSame([$x], $bindings);

                return true;
            })
            ->andReturn($expected);

        $actual = $builder->from('tests')->where('x', $x)->delete();

        self::assertSame($expected, $actual);
    }

    public function testDeleteWithId(): void
    {
        $id = 10;
        $expected = 1;

        $builder = $this->getBuilder();

        /** @var Mockery\MockInterface $connection */
        $connection = $builder->getConnection();
        $connection->shouldReceive('delete')
            ->once()
            ->withArgs(function ($query, $bindings) use ($id) {
                self::assertSame('delete from "tests" where "tests"."id" = ?', $query);
                self::assertSame([$id], $bindings);

                return true;
            })
            ->andReturn($expected);

        $actual = $builder->from('tests')->delete($id);

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
            ->once()
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('delete from "tests" where "x" = ? returning *', $query);
                self::assertSame([$x], $bindings);

                return true;
            })
            ->andReturn(['id' => $expected]);

        $actual = $builder->from('tests')->where('x', $x)->returning(['*'])->delete();

        /** @var array $actual */
        self::assertSame($expected, $actual['id']);
    }

    public function testDeleteWithIdAndReturning(): void
    {
        $x = 10;
        $expected = 1;

        $builder = $this->getBuilder();

        /** @var Mockery\MockInterface $connection */
        $connection = $builder->getConnection();
        $connection->shouldReceive('select')
            ->once()
            ->withArgs(function ($query, $bindings) use ($x, $expected) {
                self::assertSame('delete from "tests" where "x" = ? and "tests"."id" = ? returning *', $query);
                self::assertSame([$x, $expected], $bindings);

                return true;
            })
            ->andReturn(['id' => $expected]);

        $actual = $builder->from('tests')->where('x', $x)->returning(['*'])->delete($expected);

        /** @var array $actual */
        self::assertSame($expected, $actual['id']);
    }
}
