<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests\Models;

use Sunaoka\LaravelPostgres\Eloquent\Builder;
use Sunaoka\LaravelPostgres\Tests\TestCase;

class ModelTest extends TestCase
{
    public function test_new_eloquent_builder(): void
    {
        $model = new TestModel;
        $actual = $model->newEloquentBuilder($this->getQueryBuilder());

        self::assertInstanceOf(Builder::class, $actual);
    }
}
