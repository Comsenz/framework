<?php


namespace Discuz\Cache;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\RedisStore;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Factory as FactoryContracts;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class CacheManager extends Manager implements FactoryContracts
{

    protected $stores = [];


    /**
     * @param array $config
     * @return Repository
     */
    protected function createRedisDriver(array $config)
    {
        $connection = $config['connection'] ?? 'default';

        return $this->repository(new RedisStore($this->container['redis'], $this->getPrefix($config), $connection));
    }


    public function createFileDriver($config) {
        return $this->repository(new FileStore($this->container['files'], $config['path']));
    }

    public function driver($driver = null)
    {
        return $this->store($driver);
    }

    /**
     * Attempt to get the store from the local cache.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function get($name)
    {
        return $this->stores[$name] ?? $this->resolve($name);
    }

    /**
     * Resolve the given store.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Cache\Repository
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = Arr::get($this->container->config('cache'), 'stores');
        $config = $config[$name];

        if (is_null($config)) {
            throw new InvalidArgumentException("Cache store [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        } else {
            $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

            if (method_exists($this, $driverMethod)) {
                return $this->{$driverMethod}($config);
            } else {
                throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
            }
        }
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return Arr::get($this->container->config('cache'), 'default');
    }

    /**
     * Get a cache store instance by name.
     *
     * @param string|null $name
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function store($name = null)
    {
        $name = $name ?? $this->getDefaultDriver();
        return $this->stores[$name] = $this->get($name);
    }

    /**
     * Create a new cache repository with the given implementation.
     *
     * @param  \Illuminate\Contracts\Cache\Store  $store
     * @return \Illuminate\Cache\Repository
     */
    public function repository(Store $store)
    {
        $repository = new Repository($store);

        if ($this->container->bound(DispatcherContract::class)) {
            $repository->setEventDispatcher(
                $this->container[DispatcherContract::class]
            );
        }

        return $repository;
    }
}
