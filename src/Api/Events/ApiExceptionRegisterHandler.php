<?php


namespace Discuz\Api\Events;


use Tobscure\JsonApi\ErrorHandler;

class ApiExceptionRegisterHandler
{
    public $apiErrorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->apiErrorHandler = $errorHandler;
    }

}
