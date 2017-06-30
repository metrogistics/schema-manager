<?php

use SchemaManager\SchemaDefinition;

class UsersTableSchema extends SchemaDefinition
{
    protected $table_name = 'users';

    public function schema($table)
    {
        $table->increments('id');
        $table->string('name');
        $table->string('email');
        $table->string('password');
        $table->timestamps();

        $table->unique('email');
    }
}
