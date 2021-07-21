<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Query\Grammars;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

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
     * Prepare the bindings for an update statement.
     *
     * @param  array  $bindings
     * @param  array  $values
     * @return array
     */
    public function prepareBindingsForUpdate(array $bindings, array $values)
    {
        $values = collect($values)->map(function ($value, $column) {
            return is_array($value) || ($this->isJsonSelector($column) && ! $this->isExpression($value))
                ? json_encode($value, Config::get('postgres-extension.json_encode_options'))
                : $value;
        })->all();

        $cleanBindings = Arr::except($bindings, 'select');

        return array_values(
            array_merge($values, Arr::flatten($cleanBindings))
        );
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
}
