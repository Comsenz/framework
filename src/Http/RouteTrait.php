<?php


namespace Discuz\Http;


use Discuz\Http\Middleware\DispatchRoute;
use Zend\Stratigility\MiddlewarePipe;

trait RouteTrait
{
    protected function populateRoutes(RouteCollection $route)
    {
        require $this->app->basePath('routes/'.$this->prefixPath.'.php');
    }

}
