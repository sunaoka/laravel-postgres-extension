<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Query\Grammars;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class PostgresGrammar extends \Illuminate\Database\Query\Grammars\PostgresGrammar
{
    /**
     * Compile a returning clause.
     *
     * @return string
     */
    public function compileReturning(Builder $query)
    {
        /** @var \Sunaoka\LaravelPostgres\Query\Builder $query */
        if ($query->returning) {
            $returning = (new Collection($query->returning))->map(function ($value) {
                return $this->wrap($value);
            })->implode(', ');

            return "returning {$returning}";
        }

        return '';
    }

    /**
     * Compile an update statement into SQL.
     *
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
     * @return array
     */
    public function prepareBindingsForUpdate(array $bindings, array $values)
    {
        $values = (new Collection($values))->map(function ($value, $column) {
            return is_array($value) || ($this->isJsonSelector($column) && ! $this->isExpression($value))
                ? json_encode($value, config()->integer('postgres-extension.json_encode_options', 0))
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
     * @return string
     */
    public function compileDelete(Builder $query)
    {
        $sql = parent::compileDelete($query);
        $sql .= " {$this->compileReturning($query)}";

        return trim($sql);
    }
}
