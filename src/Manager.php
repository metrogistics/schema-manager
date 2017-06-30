<?php

namespace SchemaManager;

use Closure;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Schema\Schema;
use SchemaManager\Adapters\Laravel;
use Doctrine\DBAL\Schema\Comparator;
use SchemaManager\Adapters\Doctrine;
use SchemaManager\DbTypes\JsonType;
use SchemaManager\DbTypes\CharType;
use SchemaManager\DbTypes\TinyIntType;
use SchemaManager\DbTypes\TimestampType;

class Manager
{
    protected $config = [];
    protected $schema;
    protected $adapter;

    public function __construct($config)
    {
        $this->config = $config;
        $this->schema = new Schema;

        switch($this->config['adapter']){
            case 'laravel':
                $this->adapter = new Laravel();
                break;
            case 'doctrine':
            default:
                $this->adapter = new Doctrine();
                break;
        }

        $this->addCustomTypes();
    }

    public function addTable($table_name, Closure $callback)
    {
        $table = $this->schema->createTable($table_name);
        $this->adapter->defineTableSchema($table, $callback);

        return $this;
    }

    public function compareToDatabase($connection)
    {
        $db_schema = $this->adapter->getSchema($connection);

        return $this->compareToSchema($db_schema);
    }

    public function compareToSchema($schema)
    {
        return Comparator::compareSchemas(
            $schema,
            $this->schema
        );
    }

    public function performMigrationOnDatabase($connection, $include_deletes = false)
    {
        $statements = $this->getSqlFor($connection, $include_deletes);

        $this->adapter->runSqlOn($connection, $statements);
    }

    public function getSqlFor($connection, $include_deletes = false)
    {
        $comparison = $this->compareToDatabase($connection);
        $platform = $this->adapter->getPlatform($connection);

        if($include_deletes){
            return $comparison->toSql($platform);
        }

        return $comparison->toSaveSql($platform);
    }

    public function getSchemaObject()
    {
        return $this->schema;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function addCustomTypes()
    {
        if(!Type::hasType('json')){
            Type::addType('json', JsonType::class);
        }

        if(!Type::hasType('char')){
            Type::addType('char', CharType::class);
        }

        if(!Type::hasType('tinyInt')){
            Type::addType('tinyInt', TinyIntType::class);
        }

        if(!Type::hasType('timestamp')){
            Type::addType('timestamp', TimestampType::class);
        }
    }
}
