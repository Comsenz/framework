<?php


namespace Discuz\Api;


use Exception;
use Psr\Log\LoggerInterface;
use Throwable;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\ErrorHandler as JsonApiErrorHandler;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;

class ErrorHandler
{

    protected $errorHandler;

    protected $logger;

    public function __construct(JsonApiErrorHandler $errorHandler, LoggerInterface $logger)
    {
        $this->errorHandler = $errorHandler;
        $this->logger = $logger;
    }

    public function handler(Throwable $e) {

        if (! $e instanceof Exception) {
            $e = new Exception($e->getMessage(), $e->getCode(), $e);
        }

        $response = $this->errorHandler->handle($e);

        $document = new Document;
        $document->setErrors($response->getErrors());

        return new JsonApiResponse($document, $response->getStatus());
    }
}
