<?php


namespace Discuz\Foundation;


use Discuz\Event\ConfigureMiddleware;
use Discuz\Http\Middleware\DispatchRoute;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Stratigility\MiddlewarePipe;

class SiteApp implements AppInterface
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getRequestHandler() : RequestHandlerInterface
    {
        // TODO: Implement getRequestHandler() method.

        $pipe = new MiddlewarePipe();

//        $this->app->make('events')->dispatch(ConfigureMiddleware::class, $pipe);

        $pipe->pipe($this->app->make('discuz.web.middleware'));
        return $pipe;
    }
}
