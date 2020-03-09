<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Api\Controller;

use Discuz\Http\DiscuzResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractDeleteController implements RequestHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->delete($request);

        return DiscuzResponseFactory::EmptyResponse(204);
    }

    /**
     * Delete the resource.
     *
     * @param ServerRequestInterface $request
     */
    abstract protected function delete(ServerRequestInterface $request);
}
