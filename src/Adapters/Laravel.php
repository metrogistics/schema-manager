<?php

namespace SchemaManager\Adapters;

use Closure;
use Doctrine\DBAL\Schema\Table;
use SchemaManager\TableSchemaAdapter;
use Illuminate\Database\Schema\Blueprint;
use SchemaManager\Exceptions\UnknownBlueprintCommand;
use SchemaManager\Exceptions\MethodNotImplementedException;

class Laravel extends TableSchemaAdapter
{
    public function defineTableSchema(Table $table, Closure $callback)
    {
        $Blueprint = new Blueprint($table->getName());

        $callback($Blueprint);

        $this->applyColumns($table, $Blueprint->getColumns());
        $this->applyCommands($table, $Blueprint->getCommands());

        if($Blueprint->temporary){
            $table->addOption('temporary', true);
        }

        if($Blueprint->engine){
            $table->addOption('engine', $Blueprint->engine);
        }

        if($Blueprint->charset){
            $table->addOption('charset', $Blueprint->charset);
        }

        if($Blueprint->collation){
            $table->addOption('collate', $Blueprint->collation);
        }

        // dd($table);
        // dump($Blueprint);
    }

    public function getSchema($connection)
    {
        return $connection->getDoctrineConnection()->getSchemaManager()->createSchema();
    }

    public function getPlatform($connection)
    {
        return $connection->getDoctrineConnection()->getDriver()->getDatabasePlatform();
    }

    public function runSqlOn($connection, $statements)
    {
        foreach((array) $statements as $statement){
            $connection->unprepared($statement);
        }
    }

    protected function applyColumns(Table $table, $columns)
    {
        foreach($columns as $column){
            $attributes = $column->getAttributes();
            $column_name = array_pull($attributes, 'name');
            $column_type = array_pull($attributes, 'type');
            $unique = array_pull($attributes, 'unique', false);
            $index = array_pull($attributes, 'index', false);
            $nullable = array_pull($attributes, 'nullable', false);

            if(isset($attributes['storedAs'])){
                throw new MethodNotImplementedException('storedAs');
            }

            if(isset($attributes['virtualAs'])){
                throw new MethodNotImplementedException('virtualAs');
            }

            switch($column_type){
                case 'binary':
                    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#id103
                    $column_type = 'blob';
                    $attributes['length'] = 65535;
                    break;
                case 'tinyInteger':
                    $column_type = 'tinyInt';
                    break;
                case 'smallInteger':
                    $column_type = 'smallint';
                    break;
                case 'mediumInteger':
                case 'mediumInt':
                    $column_type = 'integer';
                    break;
                case 'bigInteger':
                    $column_type = 'bigint';
                    break;
                case 'decimal':
                    $attributes['precision'] = array_pull($attributes, 'total');
                    $attributes['scale'] = array_pull($attributes, 'places');
                    break;
                case 'double':
                    $column_type = 'float';
                    break;
                case 'enum':
                    throw new MethodNotImplementedException('Enum');
                    break;
                case 'text':
                    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#id105
                    $column_type = 'text';
                    $attributes['length'] = 65535;
                    break;
                case 'mediumText':
                    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#id105
                    $column_type = 'text';
                    $attributes['length'] = 16777215;
                    break;
                case 'longText':
                    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#id105
                    $column_type = 'text';
                    $attributes['length'] = 4294967295;
                    break;
                case 'ipAddress':
                    $column_type = 'string';
                    $attributes['length'] = 45;
                    break;
                case 'macAddress':
                    $column_type = 'string';
                    $attributes['length'] = 17;
                    break;
                case 'json':
                case 'jsonb':
                    $column_type = 'json';
                    $attributes['default'] = null;
                    break;
                case 'uuid':
                    $attributes['length'] = 36;
                    $column_type = 'char';
                    break;
                case 'dateTime':
                    $column_type = 'datetime';
                    break;
                case 'dateTimeTz':
                    $column_type = 'datetimetz';
                    break;
                case 'timeTz':
                    $column_type = 'time';
                    break;
                case 'timestampTz':
                case 'timestamp':
                    $column_type = 'timestamp';

                    if(isset($attributes['useCurrent']) && $attributes['useCurrent']){
                        $attributes['default'] = 'CURRENT_TIMESTAMP';
                    }
                    break;
            }

            $column = $table->addColumn($column_name, $column_type, $attributes);

            if($nullable){
                $column->setNotnull(false);
            }

            if($index){
                $index_name = $table->getName().'_'.$column_name.'_index';
                $table->addIndex([$column_name], $index_name);
            }

            if($unique){
                $index_name = $table->getName().'_'.$column_name.'_unique';
                $table->addUniqueIndex([$column_name], $index_name);
            }
        }
    }

    protected function applyCommands(Table $table, $commands)
    {
        foreach($commands as $command){
            $method = 'run'.ucfirst($command->name).'Command';

            if(method_exists($this, $method)){
                $this->$method($table, $command);
            }else{
                throw new UnknownBlueprintCommand($command->name);
            }
        }
    }

    protected function runIndexCommand(Table $table, $blueprint_command)
    {
        $table->addIndex($blueprint_command->columns, $blueprint_command->index);
    }

    protected function runPrimaryCommand(Table $table, $blueprint_command)
    {
        $table->setPrimaryKey($blueprint_command->columns, $blueprint_command->index);
    }

    protected function runUniqueCommand(Table $table, $blueprint_command)
    {
        $table->addUniqueIndex($blueprint_command->columns, $blueprint_command->index);
    }

    protected function runForeignCommand(Table $table, $blueprint_command)
    {
        $options = $blueprint_command->getAttributes();
        $columns = array_pull($options, 'columns');
        $index_name = array_pull($options, 'index');
        $foreign_table = array_pull($options, 'on');
        $foreign_column = (array) array_pull($options, 'references');

        $table->addNamedForeignKeyConstraint(
            $index_name,
            $foreign_table,
            $columns,
            $foreign_column,
            $options
        );

        // Doctrine will do this if we don't, so let's specify a nice name
        $table->addIndex(
            $blueprint_command->columns,
            $table->getName().'_'.implode('_', $blueprint_command->columns).'_index'
        );
    }

    protected function runDropColumnCommand(Table $table, $blueprint_command)
    {
        foreach($blueprint_command->columns as $column_name){
            $table->dropColumn($column_name);
        }
    }

    public function getIdColumnLogic()
    {
        return '$table->increments(\'id\');';
    }

    public function getBaseTableClass()
    {
        return Blueprint::class;
    }
}
