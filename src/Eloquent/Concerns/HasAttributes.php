<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Eloquent\Concerns;

trait HasAttributes
{
    /**
     * Encode the given value as JSON.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function asJson($value)
    {
        /** @var int $flags */
        $flags = config('postgres-extension.json_encode_options');

        /** @var string */
        return json_encode($value, $flags);
    }
}
