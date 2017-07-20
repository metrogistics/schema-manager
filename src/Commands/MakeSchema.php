<?php

namespace SchemaManager\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SchemaManager\TableSchemaAdapter;

class MakeSchema extends Command
{
    protected $signature = 'schema-manager:make-schema {name} {--directory=}';
    protected $description = 'Creates a schema definition file for a table.';

    public function handle()
    {
        $name = $this->argument('name');
        $directory = $this->option('directory') ?: database_path('schemas');

        if(!realpath($directory)){
            throw new Exception('Schema directory "'.$directory.'" not found.');
        }

        $class_name = studly_case($name).'TableSchema';
        $filename = realpath($directory).'/'.$class_name.'.php';
        $filesystem = new Filesystem();

        if($filesystem->exists($filename)){
            $overwrite = $this->confirm('A schema file already exists at '.$filename.'. Would you like to overwrite it?');

            if(!$overwrite){
                return;
            }
        }

        $stub = $this->processStub($class_name);

        $filesystem->put($filename, $stub);
    }

    protected function processStub($class_name)
    {
        $adapter_class = TableSchemaAdapter::getAdapterClass(
            config('schema-manager.adapter')
        );

        $name = $this->argument('name');
        $adapter = new $adapter_class;
        $stub = file_get_contents(realpath(__DIR__.'/../stubs/schema.stub'));
        $id_column = $adapter->getIdColumnLogic();

        $stub = str_replace('{id-column}', $id_column, $stub);
        $stub = str_replace('{class-name}', $class_name, $stub);
        $stub = str_replace('{table-name}', $name, $stub);

        return $stub;
    }
}
