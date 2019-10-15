<?php


namespace Discuz\Api\Controller;

use Discuz\Foundation\Application;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Resource;
use Zend\Diactoros\Response\EmptyResponse;

abstract class AbstractDeleteController extends AbstractResourceController
{

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->delete($request);
        return new EmptyResponse(204);
    }

    /**
     * Delete the resource.
     *
     * @param ServerRequestInterface $request
     */
    abstract public function delete(ServerRequestInterface $request);
}
