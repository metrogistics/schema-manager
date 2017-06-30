<?php

namespace SchemaManager\Adapters;

use Closure;
use Doctrine\DBAL\Schema\Table;
use SchemaManager\TableSchemaAdapter;

class Doctrine extends TableSchemaAdapter
{
    public function defineTableSchema(Table $table, Closure $callback)
    {
        $callback($table);
    }

    public function getSchema($connection)
    {
        return $connection->getSchemaManager()->createSchema();
    }

    public function getPlatform($connection)
    {
        return $connection->getDriver()->getDatabasePlatform();
    }

    public function runSqlOn($connection, $statements)
    {
        foreach((array) $statements as $statement){
            $connection->query($statement);
        }
    }

    public function getIdColumnLogic()
    {
        return '$table->addColumn(\'id\', \'integer\', [\'autoincrement\' => true]);';
    }

    public function getBaseTableClass()
    {
        return Table::class;
    }
}
