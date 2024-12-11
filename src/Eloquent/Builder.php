<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Eloquent;

/**
 * @template TModel of \Sunaoka\LaravelPostgres\Eloquent\Model
 *
 * @extends \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @mixin \Sunaoka\LaravelPostgres\Query\Builder
 */
class Builder extends \Illuminate\Database\Eloquent\Builder
{
    /**
     * Update records in the database.
     *
     * @param  array<model-property<TModel>, mixed>  $values
     * @return int|\Illuminate\Database\Eloquent\Collection<int, TModel>|null
     */
    public function update(array $values)
    {
        $result = parent::update($values);
        if (empty($this->toBase()->returning)) {
            return $result;
        }

        /** @var array $result */
        return ! empty($result) ? $this->hydrate($result) : null;
    }

    /**
     * Delete records from the database.
     *
     * @return mixed|\Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    public function delete()
    {
        $result = parent::delete();
        if (empty($this->toBase()->returning)) {
            return $result;
        }

        /** @var array $result */
        return ! empty($result) ? $this->hydrate($result) : null;
    }
}
