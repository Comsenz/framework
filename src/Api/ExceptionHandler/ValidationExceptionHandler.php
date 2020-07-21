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

namespace Discuz\Api\ExceptionHandler;

use Exception;
use Illuminate\Validation\ValidationException;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

class ValidationExceptionHandler implements ExceptionHandlerInterface
{
    private function buildErrors(array $messages, $pointer)
    {
        return array_map(function ($path, $detail) use ($pointer) {
            return [
                'status' => '422',
                'code' => 'validation_error',
                'detail' => $detail,
                'source' => ['pointer' => $pointer.'/'.$path]
            ];
        }, array_keys($messages), $messages);
    }

    public function handle(Exception $e): ResponseBag
    {
        $errors = $this->buildErrors($e->validator->getMessageBag()->messages(), '/data/attributes');

        return new ResponseBag(422, $errors);
    }

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
        return $e instanceof ValidationException;
    }
}
