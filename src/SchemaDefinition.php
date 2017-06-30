<?php

namespace SchemaManager;

abstract class SchemaDefinition
{
    protected $table_name;

    abstract public function schema($table);

    public function getTableName()
    {
        return $this->table_name;
    }

    public function getSchema()
    {
        return function($table){
            return $this->schema($table);
        };
    }
}
