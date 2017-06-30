<?php

namespace SchemaManager;

use Illuminate\Support\ServiceProvider;
use SchemaManager\Commands\Compare;
use SchemaManager\Commands\MakeSchema;

class SchemaManagerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/schema-manager.php' => config_path('schema-manager.php'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/schema-manager.php', 'schema-manager'
        );

        $this->app->bind(Manager::class, function(){
            return new Manager($this->app['config']['schema-manager']);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                Compare::class,
                MakeSchema::class,
            ]);
        }
    }
}
