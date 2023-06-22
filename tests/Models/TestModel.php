<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests\Models;

use Sunaoka\LaravelPostgres\Eloquent\Builder;
use Sunaoka\LaravelPostgres\Eloquent\Casts\TsRangeCast;
use Sunaoka\LaravelPostgres\Eloquent\Model;
use Sunaoka\LaravelPostgres\Types\Range;

/**
 * @property array $json
 * @property Range $term
 * @method static Builder|self make($attributes = [])
 */
class TestModel extends Model
{
    protected $table = 'tests';

    protected $fillable = [
        'json',
        'term',
    ];

    protected $casts = [
        'json' => 'json',
        'term' => TsRangeCast::class,
    ];
}
