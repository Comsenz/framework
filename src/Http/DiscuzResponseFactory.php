<?php


namespace Discuz\Http;


use Illuminate\Http\File;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class DiscuzResponseFactory
{
    const DEFAULT_JSON_FLAGS = 79;

    protected static $psr17Factory;

    public static function JsonApiResponse(string $payload, int $code = 200, array $headers = []): ResponseInterface
    {
        $headers['content-type'] = 'application/vnd.api+json';
        return static::createResponse(static::getPsr17Factory()->createStream($payload), $code, $headers);
    }

    public static function JsonResponse(array $payload, int $code = 200, array $headers = []): ResponseInterface
    {
        $headers['content-type'] = 'application/json';
        return static::createResponse(static::getPsr17Factory()->createStream(json_encode($payload, static::DEFAULT_JSON_FLAGS)), $code, $headers);
    }

    public static function HtmlResponse(string $payload, int $code = 200, array $headers = []): ResponseInterface
    {
        $headers['content-type'] = 'text/html; charset=utf-8';
        return static::createResponse(static::getPsr17Factory()->createStream($payload), $code, $headers);
    }

    public static function FileResponse(string $file, int $code = 200, array $headers = []): ResponseInterface
    {
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = (new File($file))->getMimeType() ?: 'application/octet-stream';
        }

        return static::createResponse(static::getPsr17Factory()->createStreamFromFile($file), $code, $headers);
    }

    public static function EmptyResponse(int $code = 204, array $headers = []): ResponseInterface
    {
        return static::createResponse(static::getPsr17Factory()->createStream(), $code, $headers);
    }

    public static function XmlResponse(string $xml, int $code = 200, array $headers = []): ResponseInterface
    {
        $headers['Content-Type'] = 'application/xml; charset=utf-8';
        return static::createResponse(static::getPsr17Factory()->createStream($xml), $code, $headers);
    }

    protected static function getPsr17Factory() {
        return static::$psr17Factory ?: new Psr17Factory();
    }

    protected static function createResponse(StreamInterface $stream, int $code, array $headers)
    {
        $response = static::getPsr17Factory()->createResponse($code)->withBody($stream);
        foreach($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }
        return $response;
    }
}
