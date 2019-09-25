<?php


namespace Discuz\Http\Middleware;


use Discuz\Foundation\Application;
use Illuminate\View\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use Zend\Diactoros\Response\HtmlResponse;

class HandleErrorsWithView implements MiddlewareInterface
{
    protected $log;
    protected $view;

    public function __construct(LoggerInterface $log, Factory $view)
    {
        $this->log = $log;
        $this->view = $view;
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
        // TODO: Implement process() method.
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            return $this->showExceptions($e);
        }
    }

    protected function showExceptions(Throwable $error)
    {

        $code = $error->getCode();

        $name = 'errors.'.$code;

        if(!$this->view->exists($name)) {
            $name = 'errors.500';
            $code = 500;
            $this->log->error($error);
        }

        $view = $this->view->make($name);

        return new HtmlResponse($view->render(), $code);


    }
}
