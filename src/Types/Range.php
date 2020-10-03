<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Types;

abstract class Range
{
    /**
     * @var string|null
     */
    protected $lower;

    /**
     * @var string|null
     */
    protected $upper;

    /**
     * @var string
     */
    protected $upperBound;

    /**
     * @var string
     */
    protected $lowerBound;

    /**
     * Get lower bound
     *
     * @return mixed
     */
    public function lower()
    {
        return $this->lower ? $this->transform($this->lower) : null;
    }

    /**
     * Get upper bound
     *
     * @return mixed
     */
    public function upper()
    {
        return $this->upper ? $this->transform($this->upper) : null;
    }

    /**
     * Alias of lower()
     *
     * @return mixed
     */
    public function from()
    {
        return $this->lower();
    }

    /**
     * Alias of upper()
     *
     * @return mixed
     */
    public function to()
    {
        return $this->upper();
    }

    /**
     * @param  string  $boundary
     * @return mixed
     */
    abstract protected function transform(string $boundary);

    /**
     * TsRange constructor.
     *
     * @param string|null  $lower
     * @param string|null  $upper
     * @param string       $lowerBound
     * @param string       $upperBound
     */
    public function __construct(string $lower = null, string $upper = null, string $lowerBound = '[', string $upperBound = ')')
    {
        $this->lower = $lower ?: null;
        $this->upper = $upper ?: null;
        $this->upperBound = $lowerBound;
        $this->lowerBound = $upperBound;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "{$this->upperBound}{$this->lower},{$this->upper}{$this->lowerBound}";
    }
}
