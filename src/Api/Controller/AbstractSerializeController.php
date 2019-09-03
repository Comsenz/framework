<?php


namespace Discuz\Api\Controller;


use Discuz\Api\JsonApiResponse;
use Discuz\Foundation\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tobscure\JsonApi\Document;

abstract class AbstractSerializeController implements RequestHandlerInterface
{
    protected $app;

    public $serializer;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // TODO: Implement handle() method.
        $document = new Document();
        $data = $this->data($request, $document);

        $serializer = $this->app->make($this->serializer);

        $element = $this->createElement($data, $serializer);

        $document->setData($element);
        return new JsonApiResponse($document);
    }

    abstract public function data(ServerRequestInterface $request, Document $document);

    abstract public function createElement($data, $serializer);
}
