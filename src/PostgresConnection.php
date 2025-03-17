<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres;

use Sunaoka\LaravelPostgres\Query\Builder as QueryBuilder;
use Sunaoka\LaravelPostgres\Query\Grammars\PostgresGrammar as QueryGrammar;
use Sunaoka\LaravelPostgres\Schema\Grammars\PostgresGrammar as SchemaGrammar;
use Sunaoka\LaravelPostgres\Schema\PostgresBuilder;

class PostgresConnection extends \Illuminate\Database\PostgresConnection
{
    /**
     * Get the default query grammar instance.
     *
     * @return QueryGrammar|\Illuminate\Database\Query\Grammars\PostgresGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return new QueryGrammar($this);
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return PostgresBuilder|\Illuminate\Database\Schema\PostgresBuilder
     */
    public function getSchemaBuilder()
    {
        if ($this->schemaGrammar === null) {
            $this->useDefaultSchemaGrammar();
        }

        return new PostgresBuilder($this);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return SchemaGrammar|\Illuminate\Database\Schema\Grammars\PostgresGrammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return new SchemaGrammar($this);
    }

    /**
     * Get a new query builder instance.
     *
     * @return QueryBuilder|\Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return new QueryBuilder(
            $this, $this->getQueryGrammar(), $this->getPostProcessor()
        );
    }
}
