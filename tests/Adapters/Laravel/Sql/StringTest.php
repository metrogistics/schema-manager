<?php

use Orchestra\Testbench\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use SchemaManager\Exceptions\MethodNotImplementedException;

class StringTest extends TestCase
{
    use LaravelTrait;

    public function test_create_table_binary_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->binary('string_1');
            $table->binary('string_2')->nullable();
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'string_1 BLOB NOT NULL, '.
                'string_2 BLOB DEFAULT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_char_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->char('string_1');
            $table->char('string_2')->nullable();
            $table->char('string_3')->default('test');
            $table->char('string_4')->nullable()->default('test 2');
            $table->char('string_5', 32);
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'string_1 CHAR(255) NOT NULL, '.
                'string_2 CHAR(255) DEFAULT NULL, '.
                'string_3 CHAR(255) DEFAULT \'test\' NOT NULL, '.
                'string_4 CHAR(255) DEFAULT \'test 2\', '.
                'string_5 CHAR(32) NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_enum_sql()
    {
        $this->expectException(MethodNotImplementedException::class);
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->enum('enum_1', ['option']);
        })->getSchemaObject()->toSql(new MySqlPlatform);
    }

    public function test_create_table_text_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->text('string_1');
            $table->text('string_2')->nullable();
            $table->text('string_3')->default('test');
            $table->text('string_4')->nullable()->default('test 2');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'string_1 TEXT NOT NULL, '.
                'string_2 TEXT DEFAULT NULL, '.
                'string_3 TEXT NOT NULL, '.// defaults not allowed
                'string_4 TEXT DEFAULT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_medium_text_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->mediumText('string_1');
            $table->mediumText('string_2')->nullable();
            $table->mediumText('string_3')->default('test');
            $table->mediumText('string_4')->nullable()->default('test 2');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'string_1 MEDIUMTEXT NOT NULL, '.
                'string_2 MEDIUMTEXT DEFAULT NULL, '.
                'string_3 MEDIUMTEXT NOT NULL, '.// defaults not allowed
                'string_4 MEDIUMTEXT DEFAULT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_long_text_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->longText('string_1');
            $table->longText('string_2')->nullable();
            $table->longText('string_3')->default('test');
            $table->longText('string_4')->nullable()->default('test 2');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'string_1 LONGTEXT NOT NULL, '.
                'string_2 LONGTEXT DEFAULT NULL, '.
                'string_3 LONGTEXT NOT NULL, '.// defaults not allowed
                'string_4 LONGTEXT DEFAULT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_string_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->string('string_1');
            $table->string('string_2')->nullable();
            $table->string('string_3')->default('test');
            $table->string('string_4')->nullable()->default('test 2');
            $table->string('string_5', 32);
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'string_1 VARCHAR(255) NOT NULL, '.
                'string_2 VARCHAR(255) DEFAULT NULL, '.
                'string_3 VARCHAR(255) DEFAULT \'test\' NOT NULL, '.
                'string_4 VARCHAR(255) DEFAULT \'test 2\', '.
                'string_5 VARCHAR(32) NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_ipaddress_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->ipAddress('string_1');
            $table->ipAddress('string_2')->nullable();
            $table->ipAddress('string_3')->default('test');
            $table->ipAddress('string_4')->nullable()->default('test 2');
            $table->ipAddress('string_5', 32);
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'string_1 VARCHAR(45) NOT NULL, '.
                'string_2 VARCHAR(45) DEFAULT NULL, '.
                'string_3 VARCHAR(45) DEFAULT \'test\' NOT NULL, '.
                'string_4 VARCHAR(45) DEFAULT \'test 2\', '.
                'string_5 VARCHAR(45) NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_json_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->json('string_1');
            $table->json('string_2')->nullable();
            $table->json('string_3')->default('test');
            $table->json('string_4')->nullable()->default('test 2');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'string_1 JSON NOT NULL, '.
                'string_2 JSON DEFAULT NULL, '.
                'string_3 JSON NOT NULL, '.// defaults not allowed
                'string_4 JSON DEFAULT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_jsonb_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->jsonb('string_1');
            $table->jsonb('string_2')->nullable();
            $table->jsonb('string_3')->default('test');
            $table->jsonb('string_4')->nullable()->default('test 2');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'string_1 JSON NOT NULL, '.
                'string_2 JSON DEFAULT NULL, '.
                'string_3 JSON NOT NULL, '.// defaults not allowed
                'string_4 JSON DEFAULT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_macaddress_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->macAddress('string_1');
            $table->macAddress('string_2')->nullable();
            $table->macAddress('string_3')->default('test');
            $table->macAddress('string_4')->nullable()->default('test 2');
            $table->macAddress('string_5', 32);
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'string_1 VARCHAR(17) NOT NULL, '.
                'string_2 VARCHAR(17) DEFAULT NULL, '.
                'string_3 VARCHAR(17) DEFAULT \'test\' NOT NULL, '.
                'string_4 VARCHAR(17) DEFAULT \'test 2\', '.
                'string_5 VARCHAR(17) NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_morphs_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->morphs('testable');
            $table->morphs('passable', 'myindex');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'testable_id INT UNSIGNED NOT NULL, '.
                'testable_type VARCHAR(255) NOT NULL, '.
                'passable_id INT UNSIGNED NOT NULL, '.
                'passable_type VARCHAR(255) NOT NULL, '.
                'INDEX users_testable_id_testable_type_index (testable_id, testable_type), '.
                'INDEX myindex (passable_id, passable_type)'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_nullablemorphs_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->nullableMorphs('testable');
            $table->nullableMorphs('passable', 'myindex');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'testable_id INT UNSIGNED DEFAULT NULL, '.
                'testable_type VARCHAR(255) DEFAULT NULL, '.
                'passable_id INT UNSIGNED DEFAULT NULL, '.
                'passable_type VARCHAR(255) DEFAULT NULL, '.
                'INDEX users_testable_id_testable_type_index (testable_id, testable_type), '.
                'INDEX myindex (passable_id, passable_type)'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_remembertoken_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->rememberToken();
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'remember_token VARCHAR(100) DEFAULT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_uuid_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->uuid('string_1');
            $table->uuid('string_2')->nullable();
            $table->uuid('string_3')->default('test');
            $table->uuid('string_4')->nullable()->default('test 2');
            $table->uuid('string_5', 32);
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'string_1 CHAR(36) NOT NULL, '.
                'string_2 CHAR(36) DEFAULT NULL, '.
                'string_3 CHAR(36) DEFAULT \'test\' NOT NULL, '.
                'string_4 CHAR(36) DEFAULT \'test 2\', '.
                'string_5 CHAR(36) NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }
}
