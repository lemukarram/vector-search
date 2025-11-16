<?php

namespace LeMukarram\VectorSearch\Core;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use LeMukarram\VectorSearch\Contracts\VectorStoreDriver;
use LeMukarram\VectorSearch\VectorStores\Drivers\ChromaDriver;
use LeMukarram\VectorSearch\VectorStores\Drivers\PineconeDriver;
use LeMukarram\VectorSearch\VectorStores\Drivers\UpstashDriver;

/**
 * This is the exact lazy-loading manager we designed.
 */
class VectorStoreManager
{
    protected $app;
    protected $stores = [];
    protected $customCreators = [];

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function store(string $name = null): VectorStoreDriver
    {
        $name = $name ?? $this->getDefaultDriver();
        if (isset($this->stores[$name])) {
            return $this->stores[$name];
        }
        return $this->stores[$name] = $this->resolve($name);
    }

    protected function resolve(string $name): VectorStoreDriver
    {
        $config = $this->getConfig($name);
        if (isset($this->customCreators[$name])) {
            return $this->callCustomCreator($name, $config);
        }

        $method = 'create' . ucfirst(strtolower($name)) . 'Driver';
        if (method_exists($this, $method)) {
            return $this->{$method}($config);
        }

        throw new InvalidArgumentException("Vector store driver [{$name}] not supported.");
    }

    protected function callCustomCreator(string $name, array $config): VectorStoreDriver
    {
        return $this->customCreators[$name]($this->app, $config);
    }

    public function extend(string $name, \Closure $callback): self
    {
        $this->customCreators[$name] = $callback;
        return $this;
    }

    // --- BUILT-IN DRIVER CREATORS ---

    protected function createUpstashDriver(array $config): VectorStoreDriver
    {
        return new UpstashDriver($config);
    }

    protected function createChromaDriver(array $config): VectorStoreDriver
    {
        return new ChromaDriver($config);
    }

    protected function createPineconeDriver(array $config): VectorStoreDriver
    {
        return new PineconeDriver($config);
    }

    // --- HELPERS ---

    public function getDefaultDriver(): string
    {
        return $this->app['config']['vector-search.default_store'];
    }

    protected function getConfig(string $name): array
    {
        $config = $this->app['config']["vector-search.stores.{$name}"];
        if (is_null($config)) {
            throw new InvalidArgumentException("Config for vector store [{$name}] not found.");
        }
        return $config;
    }

    public function __call($method, $parameters)
    {
        return $this->store()->{$method}(...$parameters);
    }
}