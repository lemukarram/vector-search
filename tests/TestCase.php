<?php

namespace LeMukarram\VectorSearch\Tests;

use LeMukarram\VectorSearch\VectorSearchServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            VectorSearchServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('vector-search.default_store', 'mock_store');
        $app['config']->set('vector-search.default_models', [
            'embedding' => 'mock_model',
            'chat' => 'mock_model',
        ]);
        $app['config']->set('vector-search.cache_ttl', 3600);
    }
}
