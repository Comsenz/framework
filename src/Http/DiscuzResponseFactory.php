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
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class DiscuzResponseFactory
{
    public static function JsonApiResponse(Document $document, int $code = 200, array $headers = []): ResponseInterface
    {
        $headers['content-type'] = 'application/vnd.api+json';
        $headers = array_merge($headers, static::getCrossHeaders());

        return new Response(self::createBody((string)$document), $code, $headers);
    }

    public static function JsonResponse(array $payload, int $code = 200, array $headers = []): ResponseInterface
    {
        $headers = array_merge($headers, static::getCrossHeaders());
        return new Response\JsonResponse($payload, $code, $headers);
    }

    public static function HtmlResponse(string $payload, int $code = 200, array $headers = []): ResponseInterface
    {
        $headers = array_merge($headers, static::getCrossHeaders());
        return new Response\HtmlResponse($payload, $code, $headers);
    }

    public static function FileResponse(string $file, int $code = 200, array $headers = []): ResponseInterface
    {
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = (new File($file))->getMimeType() ?: 'application/octet-stream';
        }
        $mode = 'r';
        $resource = @\fopen($file, $mode);
        if (false === $resource) {
            if ('' === $mode || false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'])) {
                throw new \InvalidArgumentException('The mode ' . $mode . ' is invalid.');
            }

            throw new \RuntimeException('The file ' . $file . ' cannot be opened.');
        }

        return new Response(self::createBody($resource), $code, $headers);
    }

    public static function FileStreamResponse(string $file, int $code = 200, array $headers = []): ResponseInterface
    {
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/octet-stream';
        }
        return new Response(self::createBody($file), $code, $headers);
    }

    public static function EmptyResponse(int $code = 204, array $headers = []): ResponseInterface
    {
        $headers = array_merge($headers, static::getCrossHeaders());
        return new Response\EmptyResponse($code, $headers);
    }

    public static function XmlResponse(string $xml, int $code = 200, array $headers = []): ResponseInterface
    {
        $headers = array_merge($headers, static::getCrossHeaders());
        return new Response\XmlResponse($xml, $code, $headers);
    }

    public static function RedirectResponse(string $uri, int $code = 302, array $headers = []): ResponseInterface
    {
        return new Response\RedirectResponse($uri, $code, $headers);
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

    protected static function createBody(string $string = '') {
        $body = new Stream('php://temp', 'wb+');
        $body->write($string);
        $body->rewind();
        return $body;
    }
}
