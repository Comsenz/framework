<?php

namespace Discuz\Http;

use Discuz\Api\ApiServiceProvider;
use Discuz\Auth\AuthServiceProvider;
use Discuz\Cache\CacheServiceProvider;
use Discuz\Censor\CensorServiceProvider;
use Discuz\Database\DatabaseServiceProvider;
use Discuz\Filesystem\FilesystemServiceProvider;
use Discuz\Foundation\SiteApp;
use Discuz\Qcloud\QcloudServiceProvider;
use Discuz\Search\SearchServiceProvider;
use Discuz\Foundation\Application;
use Discuz\Http\Middleware\RequestHandler;
use Discuz\Web\WebServiceProvider;
use Illuminate\Bus\BusServiceProvider;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Hashing\HashServiceProvider;
use Illuminate\Redis\RedisServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Throwable;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Zend\HttpHandlerRunner\RequestHandlerRunner;
use Zend\Stratigility\Middleware\ErrorResponseGenerator;
use Zend\Stratigility\MiddlewarePipe;

class Server extends SiteApp
{

    public function listen()
    {
        $this->siteBoot();

        $pipe = new MiddlewarePipe();

        $pipe->pipe(new RequestHandler([
            '/api' => 'discuz.api.middleware',
            '/' => 'discuz.web.middleware'
        ], $this->app));


        $runner = new RequestHandlerRunner(
            $pipe,
            new SapiEmitter,
            [ServerRequestFactory::class, 'fromGlobals'],
            function (Throwable $e) {
                $generator = new ErrorResponseGenerator;
                return $generator($e, new ServerRequest, new Response);
            }
        );

        $runner->run();
    }

}
