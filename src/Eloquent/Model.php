<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Eloquent;

class Model extends \Illuminate\Database\Eloquent\Model
{
    use Concerns\HasAttributes;

    /**
     * @param  \Sunaoka\LaravelPostgres\Query\Builder  $query
     * @return \Sunaoka\LaravelPostgres\Eloquent\Builder<*>
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }
}
