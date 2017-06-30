<?php

use Orchestra\Testbench\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use SchemaManager\Exceptions\MethodNotImplementedException;

class TableTest extends TestCase
{
    use LaravelTrait;

    public function test_create_table_sql()
    {
        $statements = $this->getTestSchema()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id INT UNSIGNED AUTO_INCREMENT NOT NULL, '.
                'first_name VARCHAR(24) NOT NULL, '.
                'middle_name VARCHAR(24) NOT NULL, '.
                'last_name VARCHAR(24) NOT NULL, '.
                'email VARCHAR(255) NOT NULL, '.
                'phone VARCHAR(10) NOT NULL, '.
                'UNIQUE INDEX users_email_unique (email), '.
                'INDEX users_phone_index (phone)'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);

        // test custom engine type, charset, and collation:
        $statements = $this->newManager()->addTable('users', function(Blueprint $table){
            $table->engine = 'MyISAM';
            $table->charset = 'ascii';
            $table->collation = 'latin1_german1_ci';
            $table->increments('id');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id INT UNSIGNED AUTO_INCREMENT NOT NULL'.
            ') DEFAULT CHARACTER SET ascii COLLATE latin1_german1_ci ENGINE = MyISAM'
        ], $statements);
    }

    public function test_create_temporary_table_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->temporary();

            $table->increments('id');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TEMPORARY TABLE users ('.
                'id INT UNSIGNED AUTO_INCREMENT NOT NULL'.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }

    public function test_drop_columns_table_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->increments('id');

            // dropColumn not needed. Undefined columns will be dropped automatically
            // $table->dropColumn('first_name');
            // $table->dropColumn('middle_name', 'last_name');
            // $table->dropColumn(['email', 'phone']);
        })->compareToSchema($this->getTestSchema())->toSql(new MySqlPlatform);

        $this->assertEquals([
            'DROP INDEX users_email_unique ON users',
            'DROP INDEX users_phone_index ON users',
            'ALTER TABLE users '.
                'DROP first_name, '.
                'DROP middle_name, '.
                'DROP last_name, '.
                'DROP email, '.
                'DROP phone'
        ], $statements);
    }

    public function test_rename_columns_table_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->increments('id');
            $table->string('first_name', 24);
            $table->string('last_name', 24);
            $table->string('email')->unique();

            //can rename column IF only thing changed is the name.
            $table->string('cell_phone', 10)->index();
            $table->string('m_initial', 24);

            //this field doesn't match existing field. gets added instead
            $table->string('another_fields', 24);
        })->compareToSchema($this->getTestSchema())->toSql(new MySqlPlatform);

        $this->assertEquals([
            'DROP INDEX users_phone_index ON users',
            'ALTER TABLE users '.
                'ADD another_fields VARCHAR(24) NOT NULL, '.
                'CHANGE phone cell_phone VARCHAR(10) NOT NULL, '.
                'CHANGE middle_name m_initial VARCHAR(24) NOT NULL',
            'CREATE INDEX users_cell_phone_index ON users (cell_phone)'
        ], $statements);
    }

    public function test_change_primary_key_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->integer('id');
            $table->string('first_name', 24);
            $table->string('middle_name', 24);
            $table->string('last_name', 24);
            $table->string('email')->unique();
            $table->string('phone', 10)->index();

            $table->primary('last_name');
        })->compareToSchema($this->getTestSchema())->toSql(new MySqlPlatform);

        $this->assertEquals([
            'ALTER TABLE users CHANGE id id INT NOT NULL',
            'ALTER TABLE users ADD PRIMARY KEY (last_name)'
        ], $statements);
    }

    public function test_change_unique_indexes_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->increments('id');
            $table->string('first_name', 24)->unique();
            $table->string('middle_name', 24);
            $table->string('last_name', 24);
            $table->string('email');
            $table->string('phone', 10)->index();

            $table->unique(['first_name', 'last_name']);
        })->compareToSchema($this->getTestSchema())->toSql(new MySqlPlatform);

        $this->assertEquals([
            'DROP INDEX users_email_unique ON users',
            'CREATE UNIQUE INDEX users_first_name_unique ON users (first_name)',
            'CREATE UNIQUE INDEX users_first_name_last_name_unique ON users (first_name, last_name)'
        ], $statements);
    }

    public function test_change_standard_indexes_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->increments('id');
            $table->string('first_name', 24);
            $table->string('middle_name', 24)->index();
            $table->string('last_name', 24);
            $table->string('email')->unique();
            $table->string('phone', 10);

            $table->index(['first_name', 'last_name']);
        })->compareToSchema($this->getTestSchema())->toSql(new MySqlPlatform);

        $this->assertEquals([
            'DROP INDEX users_phone_index ON users',
            'CREATE INDEX users_middle_name_index ON users (middle_name)',
            'CREATE INDEX users_first_name_last_name_index ON users (first_name, last_name)'
        ], $statements);
    }

    public function test_change_foreign_keys_sql()
    {
        // test the addition of a foreign key
        $add_fk_schema = $this->manager->addTable('users', function(Blueprint $table){
            $table->increments('id');
            $table->string('first_name', 24);
            $table->string('middle_name', 24);
            $table->string('last_name', 24);
            $table->string('email')->unique();
            $table->string('phone', 10)->index();

            $table->string('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        $statements = $add_fk_schema->compareToSchema($this->getTestSchema())->toSql(new MySqlPlatform);

        $this->assertEquals([
            'ALTER TABLE users ADD role_id VARCHAR(255) NOT NULL',
            'ALTER TABLE users ADD CONSTRAINT users_role_id_foreign FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE',
            'CREATE INDEX users_role_id_index ON users (role_id)'
        ], $statements);

        // test the removal of a foreign key
        $remove_fk_schema = $this->newManager()->addTable('users', function(Blueprint $table){
            $table->increments('id');
            $table->string('first_name', 24);
            $table->string('middle_name', 24);
            $table->string('last_name', 24);
            $table->string('email')->unique();
            $table->string('phone', 10)->index();
            $table->string('role_id');
        });

        $statements = $remove_fk_schema->compareToSchema($add_fk_schema->getSchemaObject())->toSql(new MySqlPlatform);

        $this->assertEquals([
            'ALTER TABLE users DROP FOREIGN KEY users_role_id_foreign',
            'DROP INDEX users_role_id_index ON users',
        ], $statements);
    }

    public function test_storedas_throws_exception()
    {
        $this->expectException(MethodNotImplementedException::class);

        $this->manager->addTable('users', function(Blueprint $table){
            $table->string('first_name')->storedAs('generated_column');
        });
    }

    public function test_virtualas_throws_exception()
    {
        $this->expectException(MethodNotImplementedException::class);

        $this->manager->addTable('users', function(Blueprint $table){
            $table->string('first_name')->virtualAs('generated_column');
        });
    }

    public function test_create_comments_sql()
    {
        $statements = $this->manager->addTable('users', function(Blueprint $table){
            $table->increments('id')->comment('The ID.');
        })->getSchemaObject()->toSql(new MySqlPlatform);

        $this->assertEquals([
            'CREATE TABLE users ('.
                'id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT \'The ID.\''.
            ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        ], $statements);
    }
}
