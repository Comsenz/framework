<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Http;

use Illuminate\Http\File;
use InvalidArgumentException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

class DiscuzResponseFactory
{
    const DEFAULT_JSON_FLAGS = 79;

    protected static $psr17Factory;

    public static function JsonApiResponse($payload, int $code = 200, array $headers = []): ResponseInterface
    {
        $headers['content-type'] = 'application/vnd.api+json';
        $payload = is_array($payload) ? json_encode($payload) : $payload;
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
        return static::createResponse(null, $code, $headers);
    }

    public static function XmlResponse(string $xml, int $code = 200, array $headers = []): ResponseInterface
    {
        $headers['Content-Type'] = 'application/xml; charset=utf-8';
        return static::createResponse(static::getPsr17Factory()->createStream($xml), $code, $headers);
    }

    public static function RedirectResponse(string $uri, int $code = 302, array $headers = []): ResponseInterface
    {
        if (! is_string($uri)) {
            throw new InvalidArgumentException(sprintf(
                'Uri provided to %s MUST be a string or Psr\Http\Message\UriInterface instance; received "%s"',
                __CLASS__,
                (is_object($uri) ? get_class($uri) : gettype($uri))
            ));
        }
        $headers['location'] = $uri;
        return static::createResponse(null, $code, $headers);
    }

    protected static function getPsr17Factory()
    {
        return static::$psr17Factory ?: new Psr17Factory();
    }

    protected static function createResponse($stream, int $code, array $headers)
    {
        $response = static::getPsr17Factory()->createResponse($code);
        if (!is_null($stream)) {
            $response = $response->withBody($stream);
        }
        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }
        return $response;
    }
}
