<?php


namespace Discuz\Api\Controller;


use Discuz\Api\JsonApiResponse;
use Discuz\Foundation\Application;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Parameters;

abstract class AbstractSerializeController implements RequestHandlerInterface
{
    protected $app;

    public $serializer;

    public $include = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }


    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Tobscure\JsonApi\Exception\InvalidParameterException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // TODO: Implement handle() method.
        $document = new Document();
        $data = $this->data($request, $document);

        $serializer = $this->app->make($this->serializer);

        $element = $this->createElement($data, $serializer)->with($this->extractIncludes($request));

        $document->setData($element);
        return new JsonApiResponse($document);
    }

    abstract public function data(ServerRequestInterface $request, Document $document);

    abstract public function createElement($data, $serializer);

    protected function buildParameters(ServerRequestInterface $request) {
        return $this->app->make(Parameters::class, ['input' => $request->getQueryParams()]);
    }

    protected function extractIncludes(ServerRequestInterface $request) {
        return $this->buildParameters($request)->getInclude($this->include);
    }
}
