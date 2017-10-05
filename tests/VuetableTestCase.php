<?php

namespace Vuetable\Tests;

use Orchestra\Testbench\TestCase;

class VuetableTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return ['Vuetable\VuetableServiceProvider'];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Vuetable' => 'Vuetable\VuetableFacade'
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    public function migrate()
    {
        $migrations = [
            \Vuetable\Tests\Migrations\UsersMigration::class,
            \Vuetable\Tests\Migrations\RolesMigration::class,
            \Vuetable\Tests\Migrations\CarsMigration::class,
        ];

        foreach ($migrations as $migration) {
            (new $migration)->up();
        }
    }
}
