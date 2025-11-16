<?php

namespace LeMukarram\VectorSearch;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use LeMukarram\VectorSearch\Core\AiModelManager;
use LeMukarram\VectorSearch\Core\VectorStoreManager;

class VectorSearchServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge the config file
        $this->mergeConfigFrom(
            __DIR__.'/../config/vector-search.php', 'vector-search'
        );

        // Register the AiModelManager as a singleton
        $this->app->singleton(AiModelManager::class, function (Container $app) {
            return new AiModelManager($app);
        });

        // Register the VectorStoreManager as a singleton
        $this->app->singleton(VectorStoreManager::class, function (Container $app) {
            return new VectorStoreManager($app);
        });

        // Register the main 'VectorSearch' facade class as a singleton
        // This is what the Facade will resolve to.
        $this->app->singleton('vector-search', function (Container $app) {
            return new VectorSearch(
                $app->make(AiModelManager::class),
                $app->make(VectorStoreManager::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Allow users to publish the config file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/vector-search.php' => config_path('vector-search.php'),
            ], 'vector-search-config');
        }
    }
}