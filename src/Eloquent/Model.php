<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Eloquent;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }
}
