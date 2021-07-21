<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests\Models;

use Sunaoka\LaravelPostgres\Eloquent\Model;

class TestModel extends Model
{
    protected $table = 'tests';
}
