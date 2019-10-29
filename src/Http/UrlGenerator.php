<?php


namespace Discuz\Http;

use Discuz\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContracts;
use Illuminate\Support\Arr;

class UrlGenerator implements UrlGeneratorContracts
{

    protected $app;
    protected $routes;
    protected $cachedScheme;
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
        return  collect([$this->formatScheme().$this->formatHost().$this->formatPath(), $this->formatQuery()])->filter()->join('?');
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
        // TODO: Implement to() method.
        return $this->formatScheme().$this->formatHost().$path;
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
        return $this->formatScheme().$this->formatHost().$this->routes->getPath($name, $parameters);
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
        // TODO: Implement action() method.
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

    protected function formatHost() {
        return self::$request->getUri()->getHost();
    }

    protected function formatScheme() {

        if (is_null($this->cachedScheme)) {
            $this->cachedScheme = self::$request->getUri()->getScheme().'://';
        }

        return $this->cachedScheme;
    }

    protected function formatPath() {
        return self::$request->getUri()->getPath();
    }

    protected function formatQuery() {
        return self::$request->getUri()->getQuery();
    }

    public static function setRequest($request) {
        self::$request = $request;
    }
}
