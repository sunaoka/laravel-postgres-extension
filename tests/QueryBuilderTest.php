<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests;

use Mockery;
use Sunaoka\LaravelPostgres\PostgresConnection;
use Sunaoka\LaravelPostgres\Query\Builder;
use Sunaoka\LaravelPostgres\Query\Grammars\PostgresGrammar;

class QueryBuilderTest extends TestCase
{
    /**
     * @var Mockery\Mock|PostgresConnection
     */
    private $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = Mockery::mock(PostgresConnection::class)->makePartial();
    }

    public function testReturning(): void
    {
        $builder = new Builder($this->connection, new PostgresGrammar());
        $builder->from('tests')->returning(['*']);

        self::assertSame(['*'], $builder->returning);
    }

    public function testUpdate(): void
    {
        $this->connection->shouldReceive('update')
            ->withArgs(function ($query, $bindings) {
                self::assertSame('update "tests" set "x" = ?', $query);
                self::assertSame([1], $bindings);
                return true;
            })
            ->andReturn(1);

        $builder = new Builder($this->connection, new PostgresGrammar());
        $actual = $builder->from('tests')->update(['x' => 1]);

        self::assertSame(1, $actual);
    }

    public function testUpdateWithReturning(): void
    {
        $this->connection->shouldReceive('select')
            ->withArgs(function ($query, $bindings) {
                self::assertSame('update "tests" set "x" = ? returning *', $query);
                self::assertSame([1], $bindings);
                return true;
            })
            ->andReturn(['id' => 1]);

        $builder = new Builder($this->connection, new PostgresGrammar());
        $actual = $builder->from('tests')->returning(['*'])->update(['x' => 1]);

        self::assertSame(['id' => 1], $actual);
    }

    public function testDelete(): void
    {
        $this->connection->shouldReceive('delete')
            ->withArgs(function ($query, $bindings) {
                self::assertSame('delete from "tests" where "x" = ?', $query);
                self::assertSame([1], $bindings);
                return true;
            })
            ->andReturn(1);

        $builder = new Builder($this->connection, new PostgresGrammar());
        $actual = $builder->from('tests')->where('x', 1)->delete();

        self::assertSame(1, $actual);
    }

    public function testDeleteWithId(): void
    {
        $this->connection->shouldReceive('delete')
            ->withArgs(function ($query, $bindings) {
                self::assertSame('delete from "tests" where "tests"."id" = ?', $query);
                self::assertSame([1], $bindings);
                return true;
            })
            ->andReturn(1);

        $builder = new Builder($this->connection, new PostgresGrammar());
        $actual = $builder->from('tests')->delete(1);

        self::assertSame(1, $actual);
    }

    public function testDeleteWithReturning(): void
    {
        $this->connection->shouldReceive('select')
            ->withArgs(function ($query, $bindings) {
                self::assertSame('delete from "tests" where "x" = ? returning *', $query);
                self::assertSame([1], $bindings);
                return true;
            })
            ->andReturn(['id' => 1]);

        $builder = new Builder($this->connection, new PostgresGrammar());
        $actual = $builder->from('tests')->where('x', 1)->returning(['*'])->delete();

        self::assertSame(['id' => 1], $actual);
    }

    public function testDeleteWithIdAndReturning(): void
    {
        $this->connection->shouldReceive('select')
            ->withArgs(function ($query, $bindings) {
                self::assertSame('delete from "tests" where "x" = ? and "tests"."id" = ? returning *', $query);
                self::assertSame([1, 2], $bindings);
                return true;
            })
            ->andReturn(['id' => 1]);

        $builder = new Builder($this->connection, new PostgresGrammar());
        $actual = $builder->from('tests')->where('x', 1)->returning(['*'])->delete(2);

        self::assertSame(['id' => 1], $actual);
    }
}
