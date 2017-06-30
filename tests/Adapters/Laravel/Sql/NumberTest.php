<?php

use Orchestra\Testbench\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Doctrine\DBAL\Platforms\MySqlPlatform;

class NumberTest extends TestCase
{
    use LaravelTrait;

    public function test_create_table_boolean_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->boolean('id_1');
            $table->boolean('id_2')->unsigned();
            $table->boolean('id_3')->default(false);
            $table->boolean('id_4')->default(true);
            $table->boolean('id_5')->nullable();
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id_1 TINYINT(1) NOT NULL, '.
                'id_2 TINYINT(1) NOT NULL, '.
                'id_3 TINYINT(1) DEFAULT \'0\' NOT NULL, '.
                'id_4 TINYINT(1) DEFAULT \'1\' NOT NULL, '.
                'id_5 TINYINT(1) DEFAULT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_tiny_integers_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->tinyInteger('id_1');
            $table->tinyInteger('id_2')->unsigned();
            $table->tinyInteger('id_3')->default(2);
            $table->unsignedTinyInteger('id_4');
            $table->unsignedTinyInteger('id_5')->default(1);
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id_1 TINYINT NOT NULL, '.
                'id_2 TINYINT UNSIGNED NOT NULL, '.
                'id_3 TINYINT DEFAULT \'2\' NOT NULL, '.
                'id_4 TINYINT UNSIGNED NOT NULL, '.
                'id_5 TINYINT UNSIGNED DEFAULT \'1\' NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_small_integers_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->smallIncrements('id_0');
            $table->smallInteger('id_1');
            $table->smallInteger('id_2')->unsigned();
            $table->smallInteger('id_3')->default(2);
            $table->unsignedSmallInteger('id_4');
            $table->unsignedSmallInteger('id_5')->default(1);
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id_0 SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, '.
                'id_1 SMALLINT NOT NULL, '.
                'id_2 SMALLINT UNSIGNED NOT NULL, '.
                'id_3 SMALLINT DEFAULT 2 NOT NULL, '.
                'id_4 SMALLINT UNSIGNED NOT NULL, '.
                'id_5 SMALLINT UNSIGNED DEFAULT 1 NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_medium_integers_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->mediumIncrements('id_0');
            $table->mediumInteger('id_1');
            $table->mediumInteger('id_2')->unsigned();
            $table->mediumInteger('id_3')->default(2);
            $table->unsignedMediumInteger('id_4');
            $table->unsignedMediumInteger('id_5')->default(1);
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id_0 INT UNSIGNED AUTO_INCREMENT NOT NULL, '.
                'id_1 INT NOT NULL, '.
                'id_2 INT UNSIGNED NOT NULL, '.
                'id_3 INT DEFAULT 2 NOT NULL, '.
                'id_4 INT UNSIGNED NOT NULL, '.
                'id_5 INT UNSIGNED DEFAULT 1 NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_integers_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->increments('id_0');
            $table->integer('id_1');
            $table->integer('id_2')->unsigned();
            $table->integer('id_3')->default(2);
            $table->unsignedInteger('id_4');
            $table->unsignedInteger('id_5')->default(1);
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id_0 INT UNSIGNED AUTO_INCREMENT NOT NULL, '.
                'id_1 INT NOT NULL, '.
                'id_2 INT UNSIGNED NOT NULL, '.
                'id_3 INT DEFAULT 2 NOT NULL, '.
                'id_4 INT UNSIGNED NOT NULL, '.
                'id_5 INT UNSIGNED DEFAULT 1 NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_big_integers_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->bigIncrements('id_0');
            $table->bigInteger('id_1');
            $table->bigInteger('id_2')->unsigned();
            $table->bigInteger('id_3')->default(2);
            $table->unsignedBigInteger('id_4');
            $table->unsignedBigInteger('id_5')->default(1);
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id_0 BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, '.
                'id_1 BIGINT NOT NULL, '.
                'id_2 BIGINT UNSIGNED NOT NULL, '.
                'id_3 BIGINT DEFAULT 2 NOT NULL, '.
                'id_4 BIGINT UNSIGNED NOT NULL, '.
                'id_5 BIGINT UNSIGNED DEFAULT 1 NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_decimal_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->decimal('id_1');
            $table->decimal('id_2')->default(2.2);
            $table->decimal('id_3')->unsigned();
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id_1 NUMERIC(8, 2) NOT NULL, '.
                'id_2 NUMERIC(8, 2) DEFAULT \'2.2\' NOT NULL, '.
                'id_3 NUMERIC(8, 2) NOT NULL'.// There is a bug in doctrine that leaves out the UNSIGNED keyword.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_double_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->double('id_1');
            $table->double('id_2')->default(2.2);
            $table->double('id_3')->unsigned();
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id_1 DOUBLE PRECISION NOT NULL, '.
                'id_2 DOUBLE PRECISION DEFAULT \'2.2\' NOT NULL, '.
                'id_3 DOUBLE PRECISION NOT NULL'.// There is a bug in doctrine that leaves out the UNSIGNED keyword.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_float_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->float('id_1');
            $table->float('id_2')->default(2.2);
            $table->float('id_3')->unsigned();
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id_1 DOUBLE PRECISION NOT NULL, '.
                'id_2 DOUBLE PRECISION DEFAULT \'2.2\' NOT NULL, '.
                'id_3 DOUBLE PRECISION NOT NULL'.// There is a bug in doctrine that leaves out the UNSIGNED keyword.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }
}
