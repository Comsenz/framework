<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Api;

use Exception;
use Psr\Log\LoggerInterface;
use Throwable;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\ErrorHandler as JsonApiErrorHandler;

class ErrorHandler
{
    protected $errorHandler;

    protected $logger;

    public function __construct(JsonApiErrorHandler $errorHandler, LoggerInterface $logger)
    {
        $this->errorHandler = $errorHandler;
        $this->logger = $logger;
    }

    public function handler(Throwable $e)
    {
        if (! $e instanceof Exception) {
            $e = new Exception($e->getMessage(), $e->getCode(), $e);
        }

        $response = $this->errorHandler->handle($e);

        $document = new Document;
        $document->setErrors($response->getErrors());

        return new JsonApiResponse($document, $response->getStatus());
    }
}
