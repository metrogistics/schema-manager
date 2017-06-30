<?php

namespace SchemaManager;

use Closure;
use Doctrine\DBAL\Schema\Table;
use SchemaManager\Adapters\Laravel;
use SchemaManager\Adapters\Doctrine;

abstract class TableSchemaAdapter
{
    abstract public function defineTableSchema(Table $table, Closure $callback);

    abstract public function getSchema($connection);

    abstract public function getPlatform($connection);

    abstract public function runSqlOn($connection, $statements);

    abstract public function getBaseTableClass();

    abstract public function getIdColumnLogic();

    public static function getAdapterClass($adapter)
    {
        switch($adapter){
            case 'doctrine':
                return Doctrine::class;
            case 'laravel':
                return Laravel::class;
        }
    }
}
