<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests\Schema\Grammars;

use Illuminate\Database\Query\Processors\PostgresProcessor;
use Illuminate\Support\Facades\Config;
use Mockery;
use Sunaoka\LaravelPostgres\PostgresConnection;
use Sunaoka\LaravelPostgres\Schema\Grammars\PostgresGrammar;
use Sunaoka\LaravelPostgres\Schema\PostgresBuilder;
use Sunaoka\LaravelPostgres\Tests\TestCase;

class PostgresBuilderTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|PostgresConnection
     */
    private $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = Mockery::mock(PostgresConnection::class)->makePartial();
        $this->connection->setSchemaGrammar(new PostgresGrammar());
        $this->connection->setPostProcessor(new PostgresProcessor());
    }

    public function testGetColumnListing(): void
    {
        $expected = [
            'id',
            'x',
        ];

        $this->connection->shouldReceive('select')->andReturn([(object)['column_name' => $expected]]);

        $builder = new PostgresBuilder($this->connection);

        Config::set('postgres-extension.information_schema_caching', true);

        $actual = $builder->getColumnListing('tests');

        self::assertSame([$expected], $actual);

        Config::set('postgres-extension.information_schema_caching', false);

        $actual = $builder->getColumnListing('tests');

        self::assertSame([$expected], $actual);
    }
}
