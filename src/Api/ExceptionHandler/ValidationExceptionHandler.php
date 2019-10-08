<?php


namespace Discuz\Api\ExceptionHandler;

use Discuz\Api\ApiExceptionHandlerInterface;
use Exception;
use Illuminate\Validation\Factory;
use Illuminate\Validation\Validator;
use Throwable;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

class ValidationExceptionHandler extends Exception implements ApiExceptionHandlerInterface
{

    protected $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
        parent::__construct(implode("\n", $validator->getMessageBag()->all()));
    }

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
}
