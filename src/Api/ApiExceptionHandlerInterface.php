<?php


namespace Discuz\Api;


use Exception;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

interface ApiExceptionHandlerInterface
{
    public function handle(Exception $exception) : ResponseBag;

}
