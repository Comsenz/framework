<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Discuz\Http;

use Discuz\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContracts;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class UrlGenerator implements UrlGeneratorContracts
{
    protected $app;

    protected $routes;

    protected $cachedScheme;

    /**
     * @var ServerRequestInterface
     */
    protected static $request;

    public function __construct(Application $app, RouteCollection $routes)
    {
        $this->app = $app;
        $this->routes = $routes;
    }

    /**
     * Get the current URL for the request.
     *
     * @return string
     */
    public function current()
    {
        return  collect([$this->formatHost().$this->formatPath(), $this->formatQuery()])->filter()->join('?');
    }

    /**
     * Get the URL for the previous request.
     *
     * @param mixed $fallback
     * @return string
     */
    public function previous($fallback = false)
    {
        // TODO: Implement previous() method.
    }

    /**
     * Generate an absolute URL to the given path.
     *
     * @param string $path
     * @param mixed $extra
     * @param bool|null $secure
     * @return string
     */
    public function to($path, $extra = [], $secure = null)
    {
        return $this->formatHost().$path;
    }

    /**
     * Generate a secure, absolute URL to the given path.
     *
     * @param string $path
     * @param array $parameters
     * @return string
     */
    public function secure($path, $parameters = [])
    {
        // TODO: Implement secure() method.
    }

    /**
     * Generate the URL to an application asset.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    public function asset($path, $secure = null)
    {
        // TODO: Implement asset() method.
    }

    /**
     * Get the URL to a named route.
     *
     * @param string $name
     * @param mixed $parameters
     * @param bool $absolute
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function route($name, $parameters = [], $absolute = true)
    {
        return $this->formatHost().$this->routes->getPath($name, $parameters);
    }

    /**
     * Get the URL to a controller action.
     *
     * @param string|array $action
     * @param mixed $parameters
     * @param bool $absolute
     * @return string
     */
    public function action($action, $parameters = [], $absolute = true)
    {
        return rtrim($this->to($action), '/') . '?' . Arr::query($parameters);
    }

    /**
     * Set the root controller namespace.
     *
     * @param string $rootNamespace
     * @return \Illuminate\Contracts\Routing\UrlGenerator
     */
    public function setRootControllerNamespace($rootNamespace)
    {
        // TODO: Implement setRootControllerNamespace() method.
    }

    protected function formatHost()
    {
        $port = self::$request->getUri()->getPort();
        return self::$request->getUri()->getScheme() . '://' . self::$request->getUri()->getHost().(in_array($port, [80, 443, null]) ? '' : ':'.$port);
    }

    protected function formatScheme()
    {
        if (is_null($this->cachedScheme)) {
            $this->cachedScheme = self::$request->getUri()->getScheme().'://';
        }

        return $this->cachedScheme;
    }

    public function formatPath()
    {
        return self::$request->getUri()->getPath();
    }

    protected function formatQuery()
    {
        return self::$request->getUri()->getQuery();
    }

    public static function setRequest($request)
    {
        self::$request = $request;
    }
}
