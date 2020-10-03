<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Query\Grammars;

use Illuminate\Database\Query\Builder;

class PostgresGrammar extends \Illuminate\Database\Query\Grammars\PostgresGrammar
{
    /**
     * Compile a returning clause.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    public function compileReturning(Builder $query)
    {
        /** @var \Sunaoka\LaravelPostgres\Query\Builder $query */
        if ($query->returning) {
            $returning = collect($query->returning)->map(function ($value) {
                return $this->wrap($value);
            })->implode(', ');
            return "returning {$returning}";
        }

        return '';
    }

    /**
     * Compile an update statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    public function compileUpdate(Builder $query, array $values)
    {
        $sql = parent::compileUpdate($query, $values);
        $sql .= " {$this->compileReturning($query)}";

        return trim($sql);
    }

    /**
     * Compile a delete statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    public function compileDelete(Builder $query)
    {
        $sql = parent::compileDelete($query);
        $sql .= " {$this->compileReturning($query)}";

        return trim($sql);
    }

    /**
     * Compile a upsert statement into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array   $values
     * @param  array   $columns
     * @return string
     */
    public function compileUpsert(Builder $query, array $values, array $columns)
    {
        $values = $this->cleanColumns($values);

        $conflict = collect($columns)->map(function ($value) {
            return $this->wrap($value);
        })->implode(', ');

        $update = $this->compileUpdateColumns($query, $values);

        $sql = $this->compileInsert($query, $values);
        $sql .= " on conflict ({$conflict}) do update set {$update}";

        $sql .= " {$this->compileReturning($query)}";

        return trim($sql);
    }

    /**
     * Prepare the bindings for an upsert statement.
     *
     * @param  array  $bindings
     * @param  array  $values
     * @return array
     */
    public function prepareBindingsForUpsert(array $bindings, array $values): array
    {
        $bindings = $this->prepareBindingsForUpdate($bindings, $values);

        return array_merge($bindings, $bindings);
    }

    /**
     * Removes the table name from the column name.
     *
     * "table"."updated_at" to "updated_at"
     *
     * @param  array  $values
     * @return array
     */
    protected function cleanColumns(array $values): array
    {
        $result = [];
        foreach ($values as $column => $value) {
            $column = last(explode('.', $column));
            $result[$column] = $value;
        }
        return $result;
    }
}
