<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests\Query;

use Mockery;
use Sunaoka\LaravelPostgres\PostgresConnection;
use Sunaoka\LaravelPostgres\Query\Builder;
use Sunaoka\LaravelPostgres\Query\Grammars\PostgresGrammar;
use Sunaoka\LaravelPostgres\Tests\TestCase;

class BuilderTest extends TestCase
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
        $x = 10;
        $expected = 1;

        $this->connection->shouldReceive('update')
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('update "tests" set "x" = ?', $query);
                self::assertSame([$x], $bindings);
                return true;
            })
            ->andReturn($expected);

        $builder = new Builder($this->connection, new PostgresGrammar());
        $actual = $builder->from('tests')->update(['x' => $x]);

        self::assertSame($expected, $actual);
    }

    public function testUpdateWithReturning(): void
    {
        $x = 10;
        $expected = 1;

        $this->connection->shouldReceive('select')
            ->withArgs(function ($query, $bindings) use ($x) {
                self::assertSame('update "tests" set "x" = ? returning *', $query);
                self::assertSame([$x], $bindings);
                return true;
            })
            ->andReturn(['id' => $expected]);

        $builder = new Builder($this->connection, new PostgresGrammar());
        $actual = $builder->from('tests')->returning(['*'])->update(['x' => $x]);

        self::assertSame($expected, $actual['id']);
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

        $builder = new Builder($this->connection, new PostgresGrammar());
        $actual = $builder->from('tests')->where('x', $x)->delete();

        self::assertSame($expected, $actual);
    }

    public function testDeleteWithId(): void
    {
        $id = 10;
        $expected = 1;

        $this->connection->shouldReceive('delete')
            ->withArgs(function ($query, $bindings) use ($id) {
                self::assertSame('delete from "tests" where "tests"."id" = ?', $query);
                self::assertSame([$id], $bindings);
                return true;
            })
            ->andReturn($expected);

        $builder = new Builder($this->connection, new PostgresGrammar());
        $actual = $builder->from('tests')->delete($id);

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
            ->andReturn(['id' => $expected]);

        $builder = new Builder($this->connection, new PostgresGrammar());
        $actual = $builder->from('tests')->where('x', $x)->returning(['*'])->delete();

        self::assertSame($expected, $actual['id']);
    }

    public function testDeleteWithIdAndReturning(): void
    {
        $x = 10;
        $expected = 1;

        $this->connection->shouldReceive('select')
            ->withArgs(function ($query, $bindings) use ($x, $expected) {
                self::assertSame('delete from "tests" where "x" = ? and "tests"."id" = ? returning *', $query);
                self::assertSame([$x, $expected], $bindings);
                return true;
            })
            ->andReturn(['id' => $expected]);

        $builder = new Builder($this->connection, new PostgresGrammar());
        $actual = $builder->from('tests')->where('x', $x)->returning(['*'])->delete($expected);

        self::assertSame($expected, $actual['id']);
    }
}
