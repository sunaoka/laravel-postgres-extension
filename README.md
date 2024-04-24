# Laravel extended PostgreSQL driver for Laravel 8.x to 11

[![Latest Stable Version](https://poser.pugx.org/sunaoka/laravel-postgres-extension/v/stable)](https://packagist.org/packages/sunaoka/laravel-postgres-extension)
[![License](https://poser.pugx.org/sunaoka/laravel-postgres-extension/license)](https://packagist.org/packages/sunaoka/laravel-postgres-extension)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/sunaoka/laravel-postgres-extension)](composer.json)
[![Laravel](https://img.shields.io/badge/laravel-%3E=%208.x-red)](https://laravel.com/)
[![Test](https://github.com/sunaoka/laravel-postgres-extension/actions/workflows/test.yml/badge.svg)](https://github.com/sunaoka/laravel-postgres-extension/actions/workflows/test.yml)
[![codecov](https://codecov.io/gh/sunaoka/laravel-postgres-extension/branch/develop/graph/badge.svg)](https://codecov.io/gh/sunaoka/laravel-postgres-extension)

----

## Installation

```bash
composer require sunaoka/laravel-postgres-extension
```

## Configurations

```bash
php artisan vendor:publish --tag=postgres-extension
```

## Features

- [x] RETURNING
    - UPDATE
    - DELETE

- [x] Caching "information_schema" table.
- 
- [x] Range Types
  - Depends on [sunaoka/laravel-postgres-range](https://github.com/sunaoka/laravel-postgres-range)

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

### Range Types

see: [sunaoka/laravel-postgres-range](https://github.com/sunaoka/laravel-postgres-range)
