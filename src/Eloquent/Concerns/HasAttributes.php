<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Eloquent\Concerns;

use Illuminate\Support\Facades\Config;

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
        $flags = Config::get('postgres-extension.json_encode_options');

        /** @var string */
        return json_encode($value, $flags);
    }
}
