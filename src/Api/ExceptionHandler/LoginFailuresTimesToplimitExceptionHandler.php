<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Api\ExceptionHandler;

use Discuz\Auth\Exception\LoginFailuresTimesToplimitException;
use Exception;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

class LoginFailuresTimesToplimitExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function manages(Exception $e)
    {
        return $e instanceof LoginFailuresTimesToplimitException;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Exception $e)
    {
        $status = 401;

        $error = [
            'status' => (string) $status,
            'code' => 'Login Times Failures Times Toplimit'
        ];

        return new ResponseBag($status, [$error]);
    }
}
