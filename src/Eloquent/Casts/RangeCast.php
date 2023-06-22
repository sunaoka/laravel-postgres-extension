<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Eloquent\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Sunaoka\LaravelPostgres\Types\Range;

/**
 * @template TGet of Range
 * @template TSet
 *
 * @implements CastsAttributes<TGet, TSet>
 */
abstract class RangeCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     *
     * @param \Sunaoka\LaravelPostgres\Eloquent\Model $model
     * @param string                                  $key
     * @param string                                  $value
     * @param array<string, mixed>                    $attributes
     *
     * @return TGet|null
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
     * @param \Sunaoka\LaravelPostgres\Eloquent\Model $model
     * @param string                                  $key
     * @param TSet|null                               $value
     * @param array<string, mixed>                    $attributes
     *
     * @return null[]|string[]
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return [
            $key => ($value !== null) ? (string)$value : null,
        ];
    }

    /**
     * @param string $value
     *
     * @return array
     */
    protected function parse(string $value): array
    {
        $matches = [];
        preg_match('/([\[(])"?(.*?)"?,"?(.*?)"?([])])/', $value, $matches);

        return $matches;
    }

    /**
     * @param array $matches
     *
     * @return TGet
     */
    abstract public function factory(array $matches): Range;
}
