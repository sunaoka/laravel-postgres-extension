<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Eloquent\Casts;

use Sunaoka\LaravelPostgres\Types\Range;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

abstract class RangeCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param  \Sunaoka\LaravelPostgres\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $matches = $this->parse($value);

        if (empty($matches)) {
            return null;
        }

        return $this->factory($matches);
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Sunaoka\LaravelPostgres\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return [
            $key => ($value !== null) ? (string)$value : null,
        ];
    }

    /**
     * @param mixed $value
     *
     * @return array
     */
    protected function parse($value): array
    {
        $matches = [];
        preg_match('/([\[(])"?(.*?)"?,"?(.*?)"?([])])/', $value, $matches);

        return $matches;
    }

    /**
     * @param array $matches
     *
     * @return Range
     */
    abstract public function factory(array $matches): Range;
}
