<?php


namespace Discuz\Api\ExceptionHandler;


use Discuz\Http\Exception\RouteNotFoundException;
use Exception;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

class RouteNotFoundExceptionHandler implements ExceptionHandlerInterface
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
        return $e instanceof RouteNotFoundException;
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
        $status = 404;
        $error = [
            'status' => (string) $status,
            'code' => 'route_not_found'
        ];

        return new ResponseBag($status, [$error]);
    }
}
