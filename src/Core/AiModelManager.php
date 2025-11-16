<?php

namespace LeMukarram\VectorSearch\Core;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use LeMukarram\VectorSearch\AiModels\AiModel;
use LeMukarram\VectorSearch\AiModels\Drivers\DeepSeekDriver;
use LeMukarram\VectorSearch\AiModels\Drivers\GeminiDriver;
use LeMukarram\VectorSearch\AiModels\Drivers\OpenAiDriver;

class AiModelManager
{
    protected $app;
    protected $models = [];
    protected $customCreators = [];

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Get an AI model instance.
     */
    public function model(string $name = null): AiModel
    {
        $name = $name ?? $this->getDefaultChatDriver(); // Use chat default, can be embedding too
        if (isset($this->models[$name])) {
            return $this->models[$name];
        }
        return $this->models[$name] = $this->resolve($name);
    }

    /**
     * Get the default embedding driver.
     */
    public function embeddingDriver(string $name = null): \LeMukarram\VectorSearch\Contracts\AiEmbeddingDriver
    {
        $name = $name ?? $this->app['config']['vector-search.default_models.embedding'];
        return $this->model($name)->embeddingDriver;
    }

    /**
     * Get the default chat driver.
     */
    public function chatDriver(string $name = null): \LeMukarram\VectorSearch\Contracts\AiChatDriver
    {
        $name = $name ?? $this->app['config']['vector-search.default_models.chat'];
        return $this->model($name)->chatDriver;
    }

    protected function resolve(string $name): AiModel
    {
        $config = $this->getConfig($name);
        if (isset($this->customCreators[$name])) {
            return $this->callCustomCreator($name, $config);
        }

        $method = 'create' . ucfirst(strtolower($name)) . 'Driver';
        if (method_exists($this, $method)) {
            return $this->{$method}($config);
        }

        throw new InvalidArgumentException("AI model driver [{$name}] not supported.");
    }

    protected function callCustomCreator(string $name, array $config): AiModel
    {
        return $this->customCreators[$name]($this->app, $config);
    }

    public function extend(string $name, \Closure $callback): self
    {
        $this->customCreators[$name] = $callback;
        return $this;
    }

    // --- BUILT-IN DRIVER CREATORS ---

    protected function createOpenaiDriver(array $config): AiModel
    {
        $driver = new OpenAiDriver($config);
        return new AiModel(embeddingDriver: $driver, chatDriver: $driver);
    }

    protected function createGeminiDriver(array $config): AiModel
    {
        $driver = new GeminiDriver($config);
        return new AiModel(embeddingDriver: $driver, chatDriver: $driver);
    }

    protected function createDeepseekDriver(array $config): AiModel
    {
        $driver = new DeepSeekDriver($config);
        return new AiModel(embeddingDriver: $driver, chatDriver: $driver);
    }

    // --- HELPERS ---

    public function getDefaultChatDriver(): string
    {
        return $this->app['config']['vector-search.default_models.chat'];
    }

    protected function getConfig(string $name): array
    {
        $config = $this->app['config']["vector-search.models.{$name}"];
        if (is_null($config)) {
            throw new InvalidArgumentException("Config for AI model [{$name}] not found.");
        }
        return $config;
    }

    public function __call($method, $parameters)
    {
        return $this->model()->{$method}(...$parameters);
    }
}