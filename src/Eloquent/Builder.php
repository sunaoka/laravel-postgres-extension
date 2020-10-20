<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Eloquent;

/**
 * @method \Sunaoka\LaravelPostgres\Query\Builder toBase()
 */
class Builder extends \Illuminate\Database\Eloquent\Builder
{
    /**
     * Update records in the database.
     *
     * @param  array  $values
     * @return int|\Illuminate\Database\Eloquent\Collection
     */
    public function update(array $values)
    {
        $result = parent::update($values);
        if (empty($this->toBase()->returning)) {
            return $result;
        }

        /** @var array $result */
        return !empty($result) ? $this->hydrate($result) : null;
    }

    /**
     * Delete records from the database.
     *
     * @return mixed
     */
    public function delete()
    {
        $result = parent::delete();
        if (empty($this->toBase()->returning)) {
            return $result;
        }

        /** @var array $result */
        return !empty($result) ? $this->hydrate($result) : null;
    }
}
