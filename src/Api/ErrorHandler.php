<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Discuz\Api;

use Discuz\Http\DiscuzResponseFactory;
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

        $this->logger->error($e);
        $response = $this->errorHandler->handle($e);

        $document = new Document;
        $document->setErrors($response->getErrors());

        return DiscuzResponseFactory::JsonApiResponse($document, $response->getStatus());
    }
}
