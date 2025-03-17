<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Eloquent\Concerns;

trait HasAttributes
{
    /**
     * Encode the given value as JSON.
     *
     * @param  mixed  $value
     * @return non-empty-string
     */
    protected function asJson($value)
    {
        /** @var non-empty-string */
        return json_encode($value, config()->integer('postgres-extension.json_encode_options', 0));
    }
}
