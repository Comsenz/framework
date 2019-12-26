<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Api\ExceptionHandler;

use Discuz\Auth\Exception\LoginFailedException;
use Exception;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

class LoginFailedExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function manages(Exception $e)
    {
        return $e instanceof LoginFailedException;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Exception $e)
    {
        $status = $e->getCode();

        $error = [
            'status' => (string) $status,
            'code' => 'login_failed',
            'detail' => ''
        ];

        if(is_numeric($e->getMessage())){
            $error['detail'] = ['count' => $e->getMessage()];
        }

        return new ResponseBag($status, [$error]);
    }
}
