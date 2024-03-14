<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Query;

use Illuminate\Database\Query\Expression;

/**
 * @property \Sunaoka\LaravelPostgres\Query\Grammars\PostgresGrammar $grammar
 */
class Builder extends \Illuminate\Database\Query\Builder
{
    /**
     * @var array
     */
    public $returning = [];

    /**
     * @return $this
     */
    public function returning(array $columns = []): self
    {
        $this->returning = $columns;

        return $this;
    }

    /**
     * Update records in the database.
     *
     * @return int|array
     */
    public function update(array $values)
    {
        $this->applyBeforeQueryCallbacks();

        /**
         * [11.x] Allow an update query to have subqueries as values
         *
         * @link https://github.com/laravel/framework/pull/50030
         */
        $values = collect($values)->map(function ($value) {
            if (! $value instanceof Builder) {
                return ['value' => $value, 'bindings' => $value];
            }

            [$query, $bindings] = $this->parseSub($value);

            return ['value' => new Expression("({$query})"), 'bindings' => function () use ($bindings) {
                return $bindings;
            }];
        });

        $sql = $this->grammar->compileUpdate($this, $values->map(function ($value) {
            return $value['value'];
        })->all());

        $bindings = $this->cleanBindings(
            $this->grammar->prepareBindingsForUpdate($this->bindings, $values->map(function ($value) {
                return $value['bindings'];
            })->all())
        );

        if (empty($this->returning)) {
            return $this->connection->update($sql, $bindings);
        }

        return $this->connection->select($sql, $bindings);
    }

    /**
     * Delete records from the database.
     *
     * @param  mixed  $id
     * @return int|array
     */
    public function delete($id = null)
    {
        // If an ID is passed to the method, we will set the where clause to check the
        // ID to let developers to simply and quickly remove a single row from this
        // database without manually specifying the "where" clauses on the query.
        if (! is_null($id)) {
            $this->where($this->from.'.id', '=', $id);  // @phpstan-ignore binaryOp.invalid
        }

        $this->applyBeforeQueryCallbacks();

        $sql = $this->grammar->compileDelete($this);

        $bindings = $this->cleanBindings(
            $this->grammar->prepareBindingsForDelete($this->bindings)
        );

        if (empty($this->returning)) {
            return $this->connection->delete($sql, $bindings);
        }

        return $this->connection->select($sql, $bindings);
    }
}
