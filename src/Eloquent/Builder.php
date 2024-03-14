<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Eloquent;

/**
 * @template TModelClass of \Sunaoka\LaravelPostgres\Eloquent\Model
 *
 * @extends \Illuminate\Database\Eloquent\Builder<TModelClass>
 *
 * @method \Sunaoka\LaravelPostgres\Query\Builder toBase()
 *
 * @mixin \Sunaoka\LaravelPostgres\Query\Builder
 */
class Builder extends \Illuminate\Database\Eloquent\Builder
{
    /**
     * Update records in the database.
     *
     * @return int|\Illuminate\Database\Eloquent\Collection<int, TModelClass>|null
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
     * @return mixed|\Illuminate\Database\Eloquent\Collection<int, TModelClass>
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
