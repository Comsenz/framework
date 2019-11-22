<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Foundation;

use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler as ContractsExceptionHandler;
use Psr\Log\LoggerInterface;

class ExceptionHandler implements ContractsExceptionHandler
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Report or log an exception.
     */
    public function report(Exception $e)
    {
        // TODO: Implement report() method.
        $this->logger->error($e->getMessage());
    }

    /**
     * Determine if the exception should be reported.
     *
     * @return bool
     */
    public function shouldReport(Exception $e)
    {
        // TODO: Implement shouldReport() method.
        $this->logger->error($e->getMessage());
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {
        // TODO: Implement render() method.
    }

    /**
     * Render an exception to the console.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function renderForConsole($output, Exception $e)
    {
        // TODO: Implement renderForConsole() method.
    }
}
