<?php

namespace SchemaManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SchemaManager\Manager;

class Compare extends Command
{
    protected $signature = 'schema-manager:compare {--connection=} {--directory=} {--R|run} {--D|include-deletes}';
    protected $description = 'Compares your database against your definition files.';

    public function handle()
    {
        $connection = $this->option('connection') ?: config('database.default');
        $directory = $this->option('directory') ?: database_path('schemas');
        $run = $this->option('run');
        $include_deletes = $this->option('include-deletes');
        $statements = [];

        $manager = app(Manager::class);

        $table_files = $this->getTables($directory);

        foreach($table_files as $table_file){
            $class_name = preg_replace('/\.php$/', '', $table_file->getFilename());
            $file_path = $table_file->getPathname();

            require_once($file_path);

            $Class = new $class_name;
            $manager->addTable($Class->getTableName(), $Class->getSchema());
        }

        $connection = app('db')->connection($connection);
        $statements = $manager->getSqlFor($connection, $include_deletes);

        if(!$run){
            $this->info('Preview');
            foreach($statements as $statement){
                $this->line($statement);
            }

            return;
        }

        $manager->performMigrationOnDatabase($connection, $include_deletes);

        $this->info('Schema synced.');
    }

    protected function getTables($directory)
    {
        $filesystem = new Filesystem();

        return array_values(
            array_filter($filesystem->allFiles($directory), function($file){
                return preg_match('/Schema\.php$/', $file);
            })
        );
    }
}
