<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests\Query;

use Sunaoka\LaravelPostgres\Tests\Models\TestModel;
use Sunaoka\LaravelPostgres\Tests\TestCase;

class ModelTest extends TestCase
{
    public function testNewEloquentBuilder(): void
    {
        $model = new TestModel;
        $actual = $model->newEloquentBuilder($this->getQueryBuilder());

        self::assertInstanceOf(\Sunaoka\LaravelPostgres\Eloquent\Builder::class, $actual);
    }
}
