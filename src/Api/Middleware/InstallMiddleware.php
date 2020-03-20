<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Api\Middleware;

use Discuz\Http\DiscuzResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class InstallMiddleware implements MiddlewareInterface
{
    protected $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $status = 500;

        return DiscuzResponseFactory::JsonApiResponse([
            'errors' => [
                [
                    'status' => $status,
                    'code' => 'not_install',
                    'detail' => [
                        'installUrl' => $this->url->route('install.index')
                    ]
                ]
            ]
        ], $status);
    }
}