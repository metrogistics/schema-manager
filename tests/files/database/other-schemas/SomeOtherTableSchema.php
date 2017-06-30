<?php

use SchemaManager\SchemaDefinition;

class SomeOtherTableSchema extends SchemaDefinition
{
    protected $table_name = 'some_other_table';

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
