<?php


namespace Discuz\Api\ExceptionHandler;


use Exception;
use Qcloud\Cos\Exception\ServiceResponseException;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

class ServiceResponseExceptionHandler implements ExceptionHandlerInterface
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
        return $e instanceof ServiceResponseException;
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
            'code' => $e->getCode(),
            'detail' => $e->__toString()
        ];

        return new ResponseBag($status, $error);
    }
}