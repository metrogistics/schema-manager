<?php

namespace SchemaManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SchemaManager\Manager;

class Compare extends Command
{
    protected $signature = 'schema-manager:compare '.
        '{--connection= : The database connection to use.} '.
        '{--directory= : The directory to look for schema files.} '.
        '{--R|run : Whether or not to run the migrations. Displays a preview by default.} '.
        '{--D|include-deletes : Whether or not to include drop table statements. Excludes by default.} '.
        '{--P|plain-display : Display the statements as a table or plain text.}';
    protected $description = 'Compares your database against your definition files.';

    public function handle()
    {
        $connection = $this->option('connection') ?: config('database.default');
        $directory = $this->option('directory') ?: database_path('schemas');
        $run = $this->option('run');
        $include_deletes = $this->option('include-deletes');
        $plain_display = $this->option('plain-display');
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
            $this->showPreview($statements, !$plain_display);

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

    protected function showPreview($statements, $show_as_table = true)
    {
        $statements = (array) $statements;

        if(count($statements) === 0){
            $this->info('No changes needed.');

            return;
        }

        $alters = 0;
        $creates = 0;
        $drops = 0;

        foreach($statements as $statement){
            if(starts_with($statement, 'ALTER TABLE')){
                $alters++;
            }

            if(starts_with($statement, 'CREATE TABLE')){
                $creates++;
            }

            if(starts_with($statement, 'DROP TABLE')){
                $drops++;
            }
        }

        if($show_as_table){
            // Turn the array of statments into an array of arrays for the table display
            // ['statement_1', 'statement_2'] -> [['statement_1'], ['statement_2']]
            $statements = array_map(function($statement){
                return [$statement];
            }, $statements);

            $this->table(['Statements'], $statements);
        }else{
            $this->info('Statements:');

            foreach($statements as $statement){
                $this->line($statement);
            }
        }

        $this->table(
            ['Alters', 'Creates', 'Drops'],
            [[$alters, $creates, $drops]]
        );
    }
}
