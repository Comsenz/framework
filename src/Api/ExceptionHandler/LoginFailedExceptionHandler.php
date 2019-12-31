<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Api\ExceptionHandler;

use Discuz\Auth\Exception\LoginFailedException;
use Exception;
use Illuminate\Support\Str;
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
        ];

        if(is_numeric($e->getMessage())){
            $error['detail'] = [Str::replaceFirst(':values',$e->getMessage(),app('translator')->get('login.residue_degree'))];
        }

        return new ResponseBag($status, [$error]);
    }
}
