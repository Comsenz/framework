<?php


namespace Discuz\Api\ExceptionHandler;


use Exception;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;

class ApiExceptionHandler implements ExceptionHandlerInterface
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
        return true;
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
        return $e->handle($e);
    }
}
