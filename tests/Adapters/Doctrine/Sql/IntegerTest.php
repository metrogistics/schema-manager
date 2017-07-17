<?php

use Doctrine\DBAL\Configuration;
use SchemaManager\Manager;
use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\DriverManager;

class IntegerTest extends TestCase
{
    public function setUp()
    {
        $this->manager = $this->newManager();
        $this->connection = DriverManager::getConnection([
            'memory' => true,
            'driver' => 'pdo_sqlite',
        ], new Configuration);
    }

    protected function newManager()
    {
        return new Manager(['adapter' => 'doctrine']);
    }

    public function test_create_table_integers_sql()
    {
        $statements = $this->manager->addTable('users', function(Table $table){
            $table->addColumn('id_1', 'integer');
            $table->addColumn('id_2', 'integer', ['unsigned' => true]);
            $table->addColumn('id_3', 'integer', ['default' => 2]);
            $table->addColumn('id_4', 'integer', ['customSchemaOptions' => ['unique' => true]]);
        })->getSqlFor($this->connection);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id_1 INTEGER NOT NULL, '.
                'id_2 INTEGER UNSIGNED NOT NULL, '.
                'id_3 INTEGER DEFAULT 2 NOT NULL, '.
                'id_4 INTEGER NOT NULL UNIQUE'.
            ')'
        ], $statements);
    }

    public function test_create_table_smallints_sql()
    {
        $statements = $this->manager->addTable('users', function(Table $table){
            $table->addColumn('id_1', 'smallint');
            $table->addColumn('id_2', 'smallint', ['unsigned' => true]);
            $table->addColumn('id_3', 'smallint', ['default' => 2]);
            $table->addColumn('id_4', 'smallint', ['customSchemaOptions' => ['unique' => true]]);
        })->getSqlFor($this->connection);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id_1 SMALLINT NOT NULL, '.
                'id_2 SMALLINT UNSIGNED NOT NULL, '.
                'id_3 SMALLINT DEFAULT 2 NOT NULL, '.
                'id_4 SMALLINT NOT NULL UNIQUE'.
            ')'
        ], $statements);
    }

    public function test_create_table_bigints_sql()
    {
        $statements = $this->manager->addTable('users', function(Table $table){
            $table->addColumn('id_1', 'bigint');
            $table->addColumn('id_2', 'bigint', ['unsigned' => true]);
            $table->addColumn('id_3', 'bigint', ['default' => 2]);
            $table->addColumn('id_4', 'bigint', ['customSchemaOptions' => ['unique' => true]]);
        })->getSqlFor($this->connection);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id_1 BIGINT NOT NULL, '.
                'id_2 BIGINT UNSIGNED NOT NULL, '.
                'id_3 BIGINT DEFAULT 2 NOT NULL, '.
                'id_4 BIGINT NOT NULL UNIQUE'.
            ')'
        ], $statements);
    }

    // public function test_update_table_integers_sql()
    // {
    //     $this->createTestTable();
    //
    //     $statements = $this->newManager()->addTable('test_table', function(Table $table){
    //         $table->addColumn('id_1', 'integer', ['unsigned' => true]);
    //         $table->addColumn('id_2', 'smallint', ['default' => 0]);
    //         $table->addColumn('id_3', 'bigint');
    //         $table->addColumn('id_4', 'bigint', ['customSchemaOptions' => ['unique' => true]]);
    //     })->getSqlFor($this->connection);
    //
    //     $this->assertEquals([
    //         'CREATE TABLE users ('.
    //             'id_1 BIGINT NOT NULL, '.
    //             'id_2 BIGINT UNSIGNED NOT NULL, '.
    //             'id_3 BIGINT DEFAULT 2 NOT NULL, '.
    //             'id_4 BIGINT NOT NULL UNIQUE'.
    //         ')'
    //     ], $statements);
    // }
    //
    // protected function createTestTable()
    // {
    //     $this->newManager()->addTable('test_table', function(Table $table){
    //         $table->addColumn('id_1', 'integer', ['unsigned' => true]);
    //         $table->addColumn('id_2', 'smallint', ['default' => 1]);
    //         $table->addColumn('id_3', 'bigint');
    //         $table->addColumn('id_4', 'bigint', ['customSchemaOptions' => ['unique' => true]]);
    //     })->performMigrationOnDatabase($this->connection);
    // }
}
