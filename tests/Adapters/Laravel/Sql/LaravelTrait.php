<?php

use SchemaManager\Manager;
use Illuminate\Database\Schema\Blueprint;

trait LaravelTrait
{
    public function setUp()
    {
        parent::setUp();
        $this->manager = $this->newManager();
    }

    protected function newManager()
    {
        return new Manager(['adapter' => 'laravel']);
    }

    protected function getTestSchema()
    {
        $manager = $this->newManager()->addTable('users', function(Blueprint $table){
            $table->increments('id');
            $table->string('first_name', 24);
            $table->string('middle_name', 24);
            $table->string('last_name', 24);
            $table->string('email')->unique();
            $table->string('phone', 10)->index();
        });

        return $manager->getSchemaObject();
    }
}
