<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Eloquent\Casts;

use Sunaoka\LaravelPostgres\Types\Range;
use Sunaoka\LaravelPostgres\Types\TsRange;

class TsRangeCast extends RangeCast
{
    public function factory(array $matches): Range
    {
        return new TsRange($matches[2], $matches[3], $matches[1], $matches[4]);
    }
}
