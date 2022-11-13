<?php

namespace Finller\Money\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Finller\Money\MoneyServiceProvider;

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

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-money_table.php.stub';
        $migration->up();
        */
    }
}
