<?php

use Orchestra\Testbench\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Doctrine\DBAL\Platforms\MySqlPlatform;

class TimeTest extends TestCase
{
    use LaravelTrait;

    public function test_create_table_date_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->date('date_1');
            $table->date('date_2')->nullable();
            $table->date('date_3')->default('2017-01-01');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'date_1 DATE NOT NULL, '.
                'date_2 DATE DEFAULT NULL, '.
                'date_3 DATE DEFAULT \'2017-01-01\' NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_datetime_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->dateTime('date_1');
            $table->dateTime('date_2')->nullable();
            $table->dateTime('date_3')->default('2017-01-01T00:00:00');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'date_1 DATETIME NOT NULL, '.
                'date_2 DATETIME DEFAULT NULL, '.
                'date_3 DATETIME DEFAULT \'2017-01-01T00:00:00\' NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_datetimetz_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->dateTimeTz('date_1');
            $table->dateTimeTz('date_2')->nullable();
            $table->dateTimeTz('date_3')->default('2017-01-01T00:00:00-00:00');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'date_1 DATETIME NOT NULL, '.
                'date_2 DATETIME DEFAULT NULL, '.
                'date_3 DATETIME DEFAULT \'2017-01-01T00:00:00-00:00\' NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_timestamp_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->timestamp('date_1');
            $table->timestamp('date_2')->useCurrent();
            $table->timestamp('date_3')->nullable();
            $table->timestamp('date_4')->default('2017-01-01 00:00:00');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'date_1 TIMESTAMP NOT NULL, '.
                // TODO: fix this so that the quotes are not there
                'date_2 TIMESTAMP DEFAULT \'CURRENT_TIMESTAMP\' NOT NULL, '.
                'date_3 TIMESTAMP DEFAULT NULL, '.
                'date_4 TIMESTAMP DEFAULT \'2017-01-01 00:00:00\' NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_timestamps_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->timestamps();
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'created_at TIMESTAMP DEFAULT NULL, '.
                'updated_at TIMESTAMP DEFAULT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_timestamptz_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->timestampTz('date_1');
            $table->timestampTz('date_2')->useCurrent();
            $table->timestampTz('date_3')->nullable();
            $table->timestampTz('date_4')->default('2017-01-01 00:00:00');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'date_1 TIMESTAMP NOT NULL, '.
                // TODO: fix this so that the quotes are not there
                'date_2 TIMESTAMP DEFAULT \'CURRENT_TIMESTAMP\' NOT NULL, '.
                'date_3 TIMESTAMP DEFAULT NULL, '.
                'date_4 TIMESTAMP DEFAULT \'2017-01-01 00:00:00\' NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_timestampstz_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->timestampsTz();
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'created_at TIMESTAMP DEFAULT NULL, '.
                'updated_at TIMESTAMP DEFAULT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_nullabletimestamps_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->nullableTimestamps();
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'created_at TIMESTAMP DEFAULT NULL, '.
                'updated_at TIMESTAMP DEFAULT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_softdeletes_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->softDeletes();
            $table->softDeletes('date_2')->useCurrent();
            $table->softDeletes('date_3')->default('2017-01-01 00:00:00');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'deleted_at TIMESTAMP DEFAULT NULL, '.
                // TODO: fix this so that the quotes are not there
                'date_2 TIMESTAMP DEFAULT \'CURRENT_TIMESTAMP\', '.
                'date_3 TIMESTAMP DEFAULT \'2017-01-01 00:00:00\''.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_time_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->time('date_1');
            $table->time('date_2')->nullable();
            $table->time('date_3')->default('12:00:00');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'date_1 TIME NOT NULL, '.
                'date_2 TIME DEFAULT NULL, '.
                'date_3 TIME DEFAULT \'12:00:00\' NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_create_table_timetz_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->timeTz('date_1');
            $table->timeTz('date_2')->nullable();
            $table->timeTz('date_3')->default('12:00:00');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'date_1 TIME NOT NULL, '.
                'date_2 TIME DEFAULT NULL, '.
                'date_3 TIME DEFAULT \'12:00:00\' NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }
}
