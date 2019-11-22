<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Api\ExceptionHandler;

use Exception;
use Illuminate\Validation\ValidationException;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

class ValidationExceptionHandler implements ExceptionHandlerInterface
{
    public function handle(Exception $e): ResponseBag
    {
        $errors = $this->buildErrors($e->validator->getMessageBag()->messages(), '/data/attributes');

        return new ResponseBag(422, $errors);
    }

    /**
     * If the exception handler is able to format a response for the provided exception,
     * then the implementation should return true.
     *
     * @return bool
     */
    public function manages(Exception $e)
    {
        return $e instanceof ValidationException;
    }

    private function buildErrors(array $messages, $pointer)
    {
        return array_map(function ($path, $detail) use ($pointer) {
            return [
                'status' => '422',
                'code' => 'validation_error',
                'detail' => $detail,
                'source' => ['pointer' => $pointer . '/' . $path],
            ];
        }, array_keys($messages), $messages);
    }
}
