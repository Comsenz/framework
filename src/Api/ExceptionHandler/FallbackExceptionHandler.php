<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Api\ExceptionHandler;

use Exception;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

class FallbackExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * @var bool
     */
    private $debug;

    /**
     * @param bool $debug
     */
    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function manages(Exception $e)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Exception $e)
    {
        $status = 500;
        $error = $this->constructError($e, $status);

        return new ResponseBag($status, [$error]);
    }

    /**
     * @param \Exception $e
     * @param $status
     *
     * @return array
     */
    private function constructError(Exception $e, $status)
    {
        return [
            'status' => (string) $status,
            'code' => $e->getMessage() ?: 'no_message',
        ];
    }
}
