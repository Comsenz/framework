<?php


namespace Discuz\Web\Controller;


use Discuz\Api\JsonApiResponse;
use Discuz\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Parameters;
use Zend\Diactoros\Response\HtmlResponse;

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
        return new HtmlResponse($view);
    }

    abstract public function render(ServerRequestInterface $request, Factory $view);

}
