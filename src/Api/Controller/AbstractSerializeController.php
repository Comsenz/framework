<?php

namespace Discuz\Api\Controller;

use Discuz\Api\JsonApiResponse;
use Discuz\Contracts\Search\Searcher;
use Discuz\Foundation\Application;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\ElementInterface;
use Tobscure\JsonApi\Exception\InvalidParameterException;
use Tobscure\JsonApi\Parameters;
use Tobscure\JsonApi\SerializerInterface;

abstract class AbstractSerializeController implements RequestHandlerInterface
{
    protected $app;

    /**
     * 命令集调用工具类.
     *
     * @var BusDispatcher
     */
    protected $bus;

    /**
     * 搜索驱动类.
     *
     * @var Searcher
     */
    protected $searcher;

    /**
     * The name of the serializer class to output results with.
     *
     * @var string
     */
    public $serializer;

    /**
     * The relationships that are included by default.
     *
     * @var array
     */
    public $include = [];

    /**
     * The relationships that are available to be included.
     *
     * @var array
     */
    public $optionalInclude = [];

    /**
     * The maximum number of records that can be requested.
     *
     * @var int
     */
    public $maxLimit = 50;

    /**
     * The number of records included by default.
     *
     * @var int
     */
    public $limit = 20;

    public function __construct(Application $app, BusDispatcher $bus, Searcher $searcher)
    {
        $this->app = $app;

        $this->bus = $bus;

        $this->searcher = $searcher;
    }

    /**
     * {@inheritdoc}
     * @throws InvalidParameterException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $document = new Document();

        $include = $this->extractInclude($request);

        $data = $this->data($request, $document);

        $serializer = $this->app->make($this->serializer);
        $serializer->setRequest($request);

        $element = $this->createElement($data, $serializer)->with($include);

        $document->setData($element);

        return new JsonApiResponse($document);
    }

    /**
     * Get the data to be serialized and assigned to the response document.
     *
     * @param ServerRequestInterface $request
     * @param Document $document
     * @return mixed
     */
    abstract public function data(ServerRequestInterface $request, Document $document);

    /**
     * Create a PHP JSON-API Element for output in the document.
     *
     * @param mixed $data
     * @param SerializerInterface $serializer
     * @return ElementInterface
     */
    abstract public function createElement($data, $serializer);

    /**
     * @param ServerRequestInterface $request
     * @return array
     * @throws InvalidParameterException
     */
    protected function extractInclude(ServerRequestInterface $request)
    {
        $available = array_merge($this->include, $this->optionalInclude);

        return $this->buildParameters($request)->getInclude($available) ?: $this->include;
    }

    /**
     * @param ServerRequestInterface $request
     * @return int
     * @throws InvalidParameterException
     */
    protected function extractOffset(ServerRequestInterface $request)
    {
        return $this->buildParameters($request)->getOffset($this->extractLimit($request)) ?: 0;
    }

    /**
     * @param ServerRequestInterface $request
     * @return int
     */
    protected function extractLimit(ServerRequestInterface $request)
    {
        return $this->buildParameters($request)->getLimit($this->maxLimit) ?: $this->limit;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Parameters
     */
    protected function buildParameters(ServerRequestInterface $request)
    {
        return new Parameters($request->getQueryParams());
    }
}
