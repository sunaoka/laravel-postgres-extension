<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests\Eloquent\Concerns;

use Sunaoka\LaravelPostgres\Tests\Models\TestModel;
use Sunaoka\LaravelPostgres\Tests\TestCase;

class HasAttributesTest extends TestCase
{
    public function testAsJson(): void
    {
        $model = TestModel::make(['json' => ['a' => 1]]);

        self::assertSame(['a' => 1], $model->json);
        self::assertSame('{"a":1}', $model->getAttributes()['json']);
    }
}
