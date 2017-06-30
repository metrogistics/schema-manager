<?php

use SchemaManager\Manager;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\DB;
use SchemaManager\Commands\Compare;
use SchemaManager\Facades\SchemaManager;
use SchemaManager\SchemaManagerServiceProvider;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Exception\RuntimeException;

class LaravelTest extends TestCase
{
    public function test_ioc_binding_returns_new_manager()
    {
        $manager = app(Manager::class);
        $this->assertInstanceOf(Manager::class, $manager);
        $this->assertCount(0, $manager->getSchemaObject()->getTables());

        $manager->addTable('users', function($table){
            $table->addColumn('id', 'integer');
        });

        $this->assertCount(1, $manager->getSchemaObject()->getTables());


        $new_manager = app(Manager::class);
        $this->assertInstanceOf(Manager::class, $new_manager);
        $this->assertCount(0, $new_manager->getSchemaObject()->getTables());
    }

    public function test_ioc_binding_receives_config_file()
    {
        $manager = app(Manager::class);

        $this->assertEquals('doctrine', $manager->getConfig()['adapter']);
    }

    public function test_facade_returns_correct_object()
    {
        $this->assertInstanceOf(Manager::class, SchemaManager::getFacadeRoot());
    }

    public function test_makeschema_command_requires_filename()
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('schema-manager:make-schema');
    }

    public function test_makeschema_command_throws_exception_if_directory_not_found()
    {
        $this->expectException(Exception::class);
        $this->artisan('schema-manager:make-schema', ['name' => 'users']);
    }

    public function test_makeschema_command_creates_file_doctrine_adapter()
    {
        $this->app->useDatabasePath(__DIR__.'/files/temp');
        $this->artisan('schema-manager:make-schema', ['name' => 'users']);

        $file = __DIR__.'/files/temp/schemas/UsersTableSchema.php';

        $this->assertFileExists($file);
        $this->assertSameAsControl('test-doctrine-user-table', file_get_contents($file));


        if(file_exists($file)){
            unlink($file);
        }
    }

    public function test_makeschema_command_creates_file_laravel_adapter()
    {
        config(['schema-manager.adapter' => 'laravel']);

        $this->app->useDatabasePath(__DIR__.'/files/temp');
        $this->artisan('schema-manager:make-schema', ['name' => 'users']);

        $file = __DIR__.'/files/temp/schemas/UsersTableSchema.php';

        $this->assertFileExists($file);
        $this->assertSameAsControl('test-laravel-user-table', file_get_contents($file));

        if(file_exists($file)){
            unlink($file);
        }
    }

    public function test_makeschema_command_can_specify_directory()
    {
        $this->app->useDatabasePath(__DIR__.'/files/temp');
        $this->artisan('schema-manager:make-schema', [
            'name' => 'users',
            '--directory' => database_path('other-directory')
        ]);

        $file = __DIR__.'/files/temp/other-directory/UsersTableSchema.php';

        $this->assertFileExists($file);
        $this->assertSameAsControl('test-doctrine-user-table', file_get_contents($file));

        if(file_exists($file)){
            unlink($file);
        }
    }

    public function test_compare_command_returns_preview_sql()
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        $output = $this->runCompareCommand();

        $this->assertSameAsControl('test-user-table-create-sql', $output->fetch());
    }

    public function test_compare_command_use_other_connection_returns_preview_sql()
    {
        config([
            'database.default' => 'mysql',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        $output = $this->runCompareCommand([
            '--connection' => 'sqlite'
        ]);

        $this->assertSameAsControl('test-user-table-create-sql', $output->fetch());
    }

    public function test_compare_command_use_other_directory_returns_preview_sql()
    {
        $this->app->useDatabasePath(__DIR__.'/files/database');

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        $output = $this->runCompareCommand([
            '--directory' => database_path('other-schemas')
        ]);

        $this->assertSameAsControl('test-some-other-table-create-sql', $output->fetch());
    }

    public function test_compare_command_run()
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        $output = $this->runCompareCommand(['-R' => true]);

        $this->assertEquals('Schema synced.'.PHP_EOL, $output->fetch());

        $tables = DB::connection('sqlite')->table('sqlite_master')->where('type', 'table')->get();

        $this->assertCount(1, $tables->all());
        $this->assertEquals('users', $tables->first()->tbl_name);
    }

    protected function runCompareCommand($input = [], $adapter = 'laravel')
    {
        config([
            'schema-manager.adapter' => $adapter
        ]);

        $this->app->useDatabasePath(__DIR__.'/files/database');

        $command = new Compare();
        $command->setLaravel($this->app);
        $output = new BufferedOutput();

        $command->run(
            new ArrayInput($input),
            $output
        );

        return $output;
    }

    protected function getPackageProviders($app)
    {
        return [SchemaManagerServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'SchemaManager' => SchemaManager::class
        ];
    }

    protected function runCommand($command, $input = [])
    {
        return $command->run(new \Symfony\Component\Console\Input\ArrayInput($input), new \Symfony\Component\Console\Output\NullOutput);
    }

    protected function assertSameAsControl($control_file, $value)
    {
        $control_file = __DIR__.'/files/database/controls/'.$control_file.'.ctrl';
        $control_text = file_get_contents($control_file);

        $this->assertEquals($control_text, $value);
    }
}
