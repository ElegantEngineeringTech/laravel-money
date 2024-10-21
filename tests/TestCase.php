<?php

namespace Elegantly\Money\Tests;

use Elegantly\Money\MoneyServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            MoneyServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('tests', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('price_default_currency')->nullable();
            $table->integer('price')->nullable();
            $table->string('currency')->nullable();

            $table->timestamps();
        });
    }
}
