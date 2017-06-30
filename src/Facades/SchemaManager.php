<?php

namespace SchemaManager\Facades;

use SchemaManager\Manager;
use Illuminate\Support\Facades\Facade;

class SchemaManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return static::$app[Manager::class];
    }
}
