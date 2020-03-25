<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\HeaderUtils;

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param string|null $abstract
     * @param array $parameters
     * @return mixed|\Illuminate\Contracts\Foundation\Application
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }
        return Container::getInstance()->make($abstract, $parameters);
    }
}


if (! function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param string $path
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function base_path($path = '')
    {
        return app()->basePath($path);
    }
}

if (! function_exists('app_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param string $path
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function app_path($path = '')
    {
        return app()->path($path);
    }
}

if (! function_exists('storage_path')) {
    /**
     * Get the path to the storage folder.
     *
     * @param string $path
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function storage_path($path = '')
    {
        return app('path.storage').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('resource_path')) {
    /**
     * Get the path to the storage folder.
     *
     * @param string $path
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function resource_path($path = '')
    {
        return app('path.resources').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('public_path')) {
    /**
     * Get the path to the public folder.
     *
     * @param string $path
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function public_path($path = '')
    {
        return app()->make('path.public').($path ? DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR) : $path);
    }
}

if (! function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array|string|null $key
     * @param mixed $default
     * @return mixed|\Illuminate\Config\Repository
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }
        if (is_array($key)) {
            return app('config')->set($key);
        }
        return app('config')->get($key, $default);
    }
}

if (! function_exists('trans')) {
    /**
     * Translate the given message.
     *
     * @param string|null $key
     * @param array $replace
     * @param string|null $locale
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    function trans($key = null, $replace = [], $locale = null)
    {
        if (is_null($key)) {
            return app('translator');
        }

        return app('translator')->get($key, $replace, $locale);
    }
}

if (! function_exists('ip')) {
    /**
     * Get Client IP.
     *
     * @param array $server
     * @return string
     */
    function ip($server)
    {
        $ip = '';
        if(Arr::get($server,'HTTP_CLIENT_IP') && strcasecmp(Arr::get($server,'HTTP_CLIENT_IP'), 'unknown')) {
            $ip = Arr::get($server,'HTTP_CLIENT_IP');
        } elseif(Arr::get($server,'HTTP_X_FORWARDED_FOR') && strcasecmp(Arr::get($server,'HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = Arr::get($server,'HTTP_X_FORWARDED_FOR');
        } elseif(Arr::get($server,'REMOTE_ADDR') && strcasecmp(Arr::get($server,'REMOTE_ADDR'), 'unknown')) {
            $ip = Arr::get($server,'REMOTE_ADDR');
        } elseif(Arr::has($server,'REMOTE_ADDR') && strcasecmp(Arr::get($server,'REMOTE_ADDR'), 'unknown')) {
            $ip = Arr::get($server, 'REMOTE_ADDR');
        }
        return $ip;
    }
}
