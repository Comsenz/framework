<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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

    public static function FileStreamResponse(string $file, int $code = 200, array $headers = []): ResponseInterface
    {
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/octet-stream';
        }

        return static::createResponse(static::getPsr17Factory()->createStream($file), $code, $headers);
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
        $headers = array_merge($headers, static::getCrossHeaders());
        foreach ($headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }
        return $response;
    }

    protected static function getCrossHeaders()
    {
        $crossConfig = app()->config('cross');
        $cross_headers = [];
        if (Arr::get($crossConfig, 'status')) {
            $request       = app(ServerRequestInterface::class);
            $origin        = Arr::get($request->getServerParams(), 'HTTP_ORIGIN') ?? '';
            $cross_origins = Arr::get($crossConfig, 'headers.Access-Control-Allow-Origin');

            $port = $request->getUri()->getPort();
            $siteUrl = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost().(in_array($port, [80, 443, null]) ? '' : ':'.$port);

            array_push($cross_origins, $siteUrl);
            array_push($cross_origins, $origin);

            if (in_array($origin, $cross_origins)) {
                $cross_headers = Arr::get($crossConfig, 'headers');
                if (is_array($cross_headers)) {
                    $cross_headers['Access-Control-Allow-Origin'] = $origin ?: $siteUrl;
                }
            }
        }
        return $cross_headers;
    }
}
