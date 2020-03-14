<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Foundation;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler as ContractsExceptionHandler;
use Psr\Log\LoggerInterface;
use Throwable;

class ExceptionHandler implements ContractsExceptionHandler
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Report or log an exception.
     *
     * @param Throwable $e
     * @return void
     */
    public function report(Throwable $e)
    {
        // TODO: Implement report() method.
        $this->logger->error($e->getMessage());
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param Throwable $e
     * @return void
     */
    public function shouldReport(Throwable $e)
    {
        // TODO: Implement shouldReport() method.
        $this->logger->error($e->getMessage());
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Throwable $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        // TODO: Implement render() method.
    }

    /**
     * Render an exception to the console.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param Throwable $e
     * @return void
     */
    public function renderForConsole($output, Throwable $e)
    {
        // TODO: Implement renderForConsole() method.
    }
}
