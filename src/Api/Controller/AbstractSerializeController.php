<?php


namespace Discuz\Api\Controller;


use Discuz\Api\JsonApiResponse;
use Discuz\Contracts\Search\Searcher;
use Discuz\Foundation\Application;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Parameters;

abstract class AbstractSerializeController implements RequestHandlerInterface
{
    protected $app;

    /**
     * 命令集调用工具类.
     *
     * @var Dispatcher
     */
    protected $bus;

    /**
     * 搜索驱动类.
     *
     * @var Dispatcher
     */
    protected $searcher;

    /**
     * The name of the serializer class to output results with.
     *
     * @var string
     */
    public $serializer;

    public function __construct(Application $app, BusDispatcher $bus, Searcher $searcher)
    {
        $this->app = $app;

        $this->bus = $bus;

        $this->searcher = $searcher;
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

        $element = $this->createElement($data, $serializer)
            ->with($this->searcher->getIncludes());

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

    abstract public function createElement($data, $serializer);

}
