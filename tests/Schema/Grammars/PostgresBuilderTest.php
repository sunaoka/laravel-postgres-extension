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

        if (version_compare(app()->version(), '10.30.0') >= 0) {
            /**
             * [10.x] Get columns of a table
             *
             * @link https://github.com/laravel/framework/pull/48357
             *
             * [11.x] Add support for modifying generated columns
             * @link https://github.com/laravel/framework/pull/50329
             */
            $columns = [
                (object) [
                    'name' => 'id',
                    'type_name' => 'int8',
                    'type' => 'bigint',
                    'collation' => null,
                    'nullable' => false,
                    'default' => null,
                    'comment' => null,
                    'generated' => null,
                ],
                (object) [
                    'name' => 'x',
                    'type_name' => 'text',
                    'type' => 'text',
                    'collation' => '',
                    'nullable' => false,
                    'default' => null,
                    'comment' => null,
                    'generated' => null,
                ],
            ];
        } else {
            $expected = [$expected];
            $columns = [
                (object) [
                    'column_name' => $expected[0],
                ],
            ];
        }

        $this->connection->shouldReceive('select')->andReturn($columns);

        $builder = new PostgresBuilder($this->connection);

        Config::set('postgres-extension.information_schema_caching', true);

        $actual = $builder->getColumnListing('tests');

        self::assertSame($expected, $actual);

        Config::set('postgres-extension.information_schema_caching', false);

        $actual = $builder->getColumnListing('tests');

        self::assertSame($expected, $actual);
    }
}
