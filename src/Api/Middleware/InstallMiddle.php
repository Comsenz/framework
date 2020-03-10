<?php


namespace Discuz\Api\Middleware;


use Discuz\Http\DiscuzResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class InstallMiddle implements MiddlewareInterface
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
                    'code' => 'not install',
                    'detail' => [
                        'installUrl' => $this->url->route('install.index')
                    ]
                ]
            ]
        ], $status);
    }
}
