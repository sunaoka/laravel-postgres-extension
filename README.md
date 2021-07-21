# Laravel extended PostgreSQL driver for Laravel 8

[![Latest Stable Version](https://poser.pugx.org/sunaoka/laravel-postgres-extension/v/stable)](https://packagist.org/packages/sunaoka/laravel-postgres-extension)
[![License](https://poser.pugx.org/sunaoka/laravel-postgres-extension/license)](https://packagist.org/packages/sunaoka/laravel-postgres-extension)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/sunaoka/laravel-postgres-extension)](composer.json)
[![Laravel](https://img.shields.io/badge/laravel-8.x-red)](https://laravel.com/)
[![Test](https://github.com/sunaoka/laravel-postgres-extension/actions/workflows/test.yml/badge.svg)](https://github.com/sunaoka/laravel-postgres-extension/actions/workflows/test.yml)
[![codecov](https://codecov.io/gh/sunaoka/laravel-postgres-extension/branch/develop/graph/badge.svg)](https://codecov.io/gh/sunaoka/laravel-postgres-extension)

----

## Installation

```bash
composer require sunaoka/laravel-postgres-extension
```

## Features

- Range Types
    - [ ] int4range — Range of integer
    - [ ] int8range — Range of bigint
    - [ ] numrange — Range of numeric
    - [x] tsrange — Range of timestamp without time zone
    - [ ] tstzrange — Range of timestamp with time zone
    - [ ] daterange — Range of date

- [x] RETURNING
    - UPDATE
    - DELETE

- [x] Caching "information_schema" table.

## Usage

### Table

```sql
CREATE TABLE some_models
(
    id bigserial PRIMARY KEY NOT NULL,
    code text NOT NULL,
    term tsrange NOT NULL,
    CONSTRAINT code_uq UNIQUE (code)
);
```

### Model

```php
<?php

namespace App\Models;

class SomeModel extends \Sunaoka\LaravelPostgres\Eloquent\Model
{
    protected $casts = [
        'term' => \Sunaoka\LaravelPostgres\Eloquent\Casts\TsRangeCast::class, // tsrange
    ];
}
```

### Range Types

#### tsrange — Range of timestamp without time zone

```php
$some = new SomeModel();
$some->code = 'some code';
$some->term = new TsRange('2020-10-01 00:00:00', '2020-10-01 23:59:59');
$some->save();
```

```sql
insert into "some_models" ("code", "term") values
  ('some code', '[2020-10-01 00:00:00,2020-10-01 23:59:59)')
  returning "id";
```

```php
$some = SomeModel::find(1);

echo $some->term->lower()->format('Y-m-d H:i:s'); // lower() or from()
// => 2020-10-01 00:00:00
echo $some->term->upper()->format('Y-m-d H:i:s'); // upper() or to()
// => 2020-10-01 23:59:59
```

### RETURNING

```php
$some = SomeModel::whereId(1)
    ->returning(['*'])
    ->update([
        'term' => new TsRange('2020-09-01 00:00:00', '2020-09-01 23:59:59'),
    ]);

echo get_class($some);
// => Illuminate\Database\Eloquent\Collection

echo get_class($some->first());
// => App\Models\SomeModel
```

```sql
update "some_models" 
set "term" = '[2020-09-01 00:00:00,2020-09-01 23:59:59)' 
where "id" = '1' 
returning *
```

### Caching "information_schema" table.

Permanently cache the results for a table like the one below.

```sql
select * 
from information_schema.tables 
where table_schema = 'public' and table_name = 'some_models'
```
