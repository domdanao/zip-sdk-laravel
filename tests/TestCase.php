<?php

namespace Domdanao\ZipSdkLaravel\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Domdanao\ZipSdkLaravel\ZipSdkServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ZipSdkServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set up Zip config for testing
        $app['config']->set('zip-sdk.api_server', 'https://api.sandbox.zip.co');
        $app['config']->set('zip-sdk.public_key', 'test_public_key');
        $app['config']->set('zip-sdk.secret_key', 'test_secret_key');
    }
}
