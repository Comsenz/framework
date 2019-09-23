<?php


namespace Discuz\Api\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Resource;

abstract class AbstractCreateController extends AbstractResourceController
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return parent::handle($request)->withStatus(201);
    }
}
