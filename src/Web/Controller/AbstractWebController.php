<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Web\Controller;

use Discuz\Foundation\Application;
use Discuz\Http\DiscuzResponseFactory;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractWebController implements RequestHandlerInterface
{
    protected $app;

    protected $view;

    public function __construct(Application $app, Factory $view)
    {
        $this->app = $app;
        $this->view = $view;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Tobscure\JsonApi\Exception\InvalidParameterException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $view = $this->render($request, $this->view);
        if ($view instanceof Renderable) {
            $view = $view->render();
        }
        return DiscuzResponseFactory::HtmlResponse($view);
    }

    abstract public function render(ServerRequestInterface $request, Factory $view);
}
