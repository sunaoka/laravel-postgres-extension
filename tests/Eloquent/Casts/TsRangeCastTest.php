<?php

declare(strict_types=1);

namespace Sunaoka\LaravelPostgres\Tests\Eloquent\Casts;

use Sunaoka\LaravelPostgres\Eloquent\Casts\TsRangeCast;
use Sunaoka\LaravelPostgres\Tests\Models\TestModel;
use Sunaoka\LaravelPostgres\Tests\TestCase;

class TsRangeCastTest extends TestCase
{
    public function testSet(): void
    {
        $cast = new TsRangeCast();
        $actual = $cast->set(new TestModel(), 'term', '[2020-10-01 00:00:00,2020-10-01 23:59:59)', []);

        self::assertSame('[2020-10-01 00:00:00,2020-10-01 23:59:59)', $actual['term']);
    }

    public function testGet(): void
    {
        $cast = new TsRangeCast();
        $actual = $cast->get(new TestModel(), 'term', '[2020-10-01 00:00:00,2020-10-01 23:59:59)', []);

        self::assertSame('[2020-10-01 00:00:00,2020-10-01 23:59:59)', (string)$actual);

        $actual = $cast->get(new TestModel(), 'term', '', []);

        self::assertNull($actual);
    }
}
