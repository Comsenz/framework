<?php


namespace Discuz\Http\Middleware;


use Discuz\Foundation\Application;
use Discuz\Web\WebServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class WebBaseServiceProviderMiddleware implements MiddlewareInterface
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $this->loadWebBaseServiceProvider();

        return $handler->handle($request);
    }

    protected function loadWebBaseServiceProvider() {
        $this->app->register(DatabaseServiceProvider::class);
    }
}
