<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Types;

use Carbon\CarbonImmutable;

/**
 * @extends Range<CarbonImmutable>
 */
class TsRange extends Range
{
    /**
     * @param string $boundary
     *
     * @return CarbonImmutable
     */
    protected function transform(string $boundary): CarbonImmutable
    {
        return CarbonImmutable::parse($boundary);
    }
}
