# Laravel extended PostgreSQL driver for Laravel 8

## Installation

```bash
composer require sunaoka/laravel-postgres-extention
```

## Features

- Range Types
    - [ ] int4range — Range of integer
    - [ ] int8range — Range of bigint
    - [ ] numrange — Range of numeric
    - [x] tsrange — Range of timestamp without time zone
    - [ ] tstzrange — Range of timestamp with time zone
    - [ ] daterange — Range of date

- [x] UPSERT (ON CONFLICT DO UPDATE)

- [x] RETURNING
    - UPDATE
    - DELETE
    - UPSERT

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

### UPSERT (ON CONFLICT DO UPDATE)

```php
$result = SomeModel::upsert([
    'code' => 'some code',
    'term' => new TsRange('2020-08-01 00:00:00', '2020-08-01 23:59:59'),
], [
    'code',
]);

echo $result;
// => true or false
```

```sql
insert into "some_models" ("code", "term") values 
  ('some code', '[2020-08-01 00:00:00,2020-08-01 23:59:59)') 
  on conflict ("code") do update
  set "code" = 'some code', "term" = '[2020-08-01 00:00:00,2020-08-01 23:59:59)';
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
