<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Api\ExceptionHandler;

use Exception;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

class TencentCloudSDKExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * If the exception handler is able to format a response for the provided exception,
     * then the implementation should return true.
     *
     * @param \Exception $e
     *
     * @return bool
     */
    public function manages(Exception $e)
    {
        return $e instanceof TencentCloudSDKException;
    }

    /**
     * Handle the provided exception.
     *
     * @param \Exception $e
     *
     * @return \Tobscure\JsonApi\Exception\Handler\ResponseBag
     */
    public function handle(Exception $e)
    {
        $status = 500;
        $error = [
            'status' => (string) $status,
            'code' => $e->getErrorCode(),
            'detail' => $e->getMessage()
        ];

        return new ResponseBag($status, [$error]);
    }
}
