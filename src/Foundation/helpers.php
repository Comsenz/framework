<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

use Illuminate\Container\Container;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param null|string $abstract
     *
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    function app($abstract = null, array $parameters = [])
    {
        if (null === $abstract) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param string $path
     *
     * @return string
     */
    function base_path($path = '')
    {
        return app()->basePath($path);
    }
}

if (!function_exists('app_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param string $path
     *
     * @return string
     */
    function app_path($path = '')
    {
        return app()->path($path);
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get the path to the storage folder.
     *
     * @param string $path
     *
     * @return string
     */
    function storage_path($path = '')
    {
        return app('path.storage') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('resource_path')) {
    /**
     * Get the path to the storage folder.
     *
     * @param string $path
     *
     * @return string
     */
    function resource_path($path = '')
    {
        return app('path.resources') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param null|array|string $key
     * @param mixed             $default
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    function config($key = null, $default = null)
    {
        if (null === $key) {
            return app('config');
        }
        if (is_array($key)) {
            return app('config')->set($key);
        }

        return app('config')->get($key, $default);
    }
}
