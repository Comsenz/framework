<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Api\Controller;

use Discuz\Api\JsonApiResponse;
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

    /**
     * The fields that are available to be sorted by.
     *
     * @var array
     */
    public $sortFields = [];

    /**
     * The default sort field and order to user.
     *
     * @var null|array
     */
    public $sort;
    protected $app;

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $document = new Document();

        $data = $this->data($request, $document);

        $serializer = app()->make($this->serializer);
        $serializer->setRequest($request);

        $element = $this->createElement($data, $serializer)
            ->with($this->extractInclude($request))
        ;

        $document->setData($element);

        return new JsonApiResponse($document);
    }

    /**
     * Get the data to be serialized and assigned to the response document.
     *
     * @return mixed
     */
    abstract protected function data(ServerRequestInterface $request, Document $document);

    /**
     * Create a PHP JSON-API Element for output in the document.
     *
     * @param mixed $data
     *
     * @return ElementInterface
     */
    abstract protected function createElement($data, SerializerInterface $serializer);

    /**
     * @throws InvalidParameterException
     *
     * @return array
     */
    protected function extractInclude(ServerRequestInterface $request)
    {
        $available = array_merge($this->include, $this->optionalInclude);

        return $this->buildParameters($request)->getInclude($available) ?: $this->include;
    }

    /**
     * @return array
     */
    protected function extractFields(ServerRequestInterface $request)
    {
        return $this->buildParameters($request)->getFields();
    }

    /**
     * @throws InvalidParameterException
     *
     * @return null|array
     */
    protected function extractSort(ServerRequestInterface $request)
    {
        return $this->buildParameters($request)->getSort($this->sortFields) ?: $this->sort;
    }

    /**
     * @throws InvalidParameterException
     *
     * @return int
     */
    protected function extractOffset(ServerRequestInterface $request)
    {
        return $this->buildParameters($request)->getOffset($this->extractLimit($request)) ?: 0;
    }

    /**
     * @return int
     */
    protected function extractLimit(ServerRequestInterface $request)
    {
        return $this->buildParameters($request)->getLimit($this->maxLimit) ?: $this->limit;
    }

    /**
     * @return array
     */
    protected function extractFilter(ServerRequestInterface $request)
    {
        return $this->buildParameters($request)->getFilter() ?: [];
    }

    /**
     * @return Parameters
     */
    protected function buildParameters(ServerRequestInterface $request)
    {
        return new Parameters($request->getQueryParams());
    }
}
