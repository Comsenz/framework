<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Http;

use Illuminate\Http\File;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
        $headers['Location'] = $uri;
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
        $response = static::addHeader($response);

        return $response;
    }

    private static function addHeader(ResponseInterface $response): ResponseInterface
    {
        $site_url      = app()->config('site_url');
        $cross         = app()->config('cross');
        $cross_status  = Arr::get($cross, 'status');
        $cross_headers = [];

        if (is_bool($cross_status) && $cross_status) {
            $request       = app(ServerRequestInterface::class);
            $origin        = Arr::get($request->getServerParams(), 'HTTP_ORIGIN') ?? '';
            $cross_origins = (array) Arr::get($cross, 'headers.Access-Control-Allow-Origin');
            array_push($cross_origins, $site_url);

            if (in_array($origin, $cross_origins)) {
                $cross_headers = Arr::get($cross, 'headers');
                if (is_array($cross_headers)) {
                    $cross_headers['Access-Control-Allow-Origin'] = $origin;
                } else {
                    $cross_headers = [];
                }
            }
        }

        foreach ($cross_headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response;
    }
}
