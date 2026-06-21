<?php

namespace Shearerline\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Shearerline\ShearerlineServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ShearerlineServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Shearerline' => \Shearerline\Facades\Shearerline::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
