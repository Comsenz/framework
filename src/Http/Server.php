<?php

namespace Discuz\Http;

use Discuz\Foundation\Exceptions\Handler;
use Discuz\Foundation\SiteInterface;
use ErrorException;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Zend\HttpHandlerRunner\RequestHandlerRunner;
use Zend\Stratigility\Middleware\ErrorResponseGenerator;

class Server
{
    /**
     * @var string
     */
    private static $reservedMemory;

    protected $site;

    public function __construct(SiteInterface $site)
    {
        $this->site = $site;

        $this->bootstrap();
    }

    public function listen() {

        $app = $this->site->bootApp();

        $runner = new RequestHandlerRunner(
            $app->getRequestHandler(),
            new SapiEmitter,
            [ServerRequestFactory::class, 'fromGlobals'],
            function (Throwable $e) {
                $generator = new ErrorResponseGenerator;
                return $generator($e, new ServerRequest, new Response);
            }
        );

        $runner->run();
    }

    protected function bootstrap() {
        self::$reservedMemory = str_repeat('x', 10240);

        error_reporting(-1);

        set_error_handler([$this, 'handleError']);

        set_exception_handler([$this, 'handleException']);

        register_shutdown_function([$this, 'handleShutdown']);

    }

    /**
     * Convert PHP errors to ErrorException instances.
     *
     * @param  int  $level
     * @param  string  $message
     * @param  string  $file
     * @param  int  $line
     * @param  array  $context
     * @return void
     *
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Handle an uncaught exception from the application.
     *
     * Note: Most exceptions can be handled via the try / catch block in
     * the HTTP and Console kernels. But, fatal error exceptions must
     * be handled differently since they are not normal exceptions.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function handleException($e)
    {
        if (! $e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }

        try {
            self::$reservedMemory = null;
            $this->getExceptionHandler()->report($e);
        } catch (Exception $e) {
            //
        }
//        if ($this->app->runningInConsole()) {
//            $this->renderForConsole($e);
//        } else {
            $this->renderHttpResponse($e);
//        }
    }

    public function handleShutdown() {
        dd(3);
    }

    /**
     * Get an instance of the exception handler.
     *
     * @return \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected function getExceptionHandler()
    {
        return new Handler;
    }

    /**
     * Render an exception as an HTTP response and send it.
     *
     * @param  \Exception  $e
     * @return void
     */
    protected function renderHttpResponse(Exception $e)
    {
        $this->getExceptionHandler()->render((ServerRequestFactory::fromGlobals()), $e);
    }
}
