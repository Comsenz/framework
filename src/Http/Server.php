<?php

namespace Discuz\Http;

use Discuz\Api\ApiServiceProvider;
use Discuz\Auth\AuthServiceProvider;
use Discuz\Censor\CensorServiceProvider;
use Discuz\Database\DatabaseServiceProvider;
use Discuz\Filesystem\FilesystemServiceProvider;
use Discuz\Search\SearchServiceProvider;
use Discuz\Foundation\Application;
use Discuz\Http\Middleware\RequestHandler;
use Discuz\Web\WebServiceProvider;
use Illuminate\Bus\BusServiceProvider;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Hashing\HashServiceProvider;
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

class Server
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

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

    protected function siteBoot() {

        $this->app->instance('env', 'production');
        $this->app->instance('discuz.config', $this->loadConfig());
        $this->app->instance('config', $this->getIlluminateConfig());

        $this->registerBaseEnv();
        $this->registerLogger();

        $this->app->register(HttpServiceProvider::class);
        $this->app->register(DatabaseServiceProvider::class);
        $this->app->register(FilesystemServiceProvider::class);
        $this->app->register(EncryptionServiceProvider::class);
        $this->app->register(CacheServiceProvider::class);
        $this->app->register(ApiServiceProvider::class);
        $this->app->register(WebServiceProvider::class);
        $this->app->register(BusServiceProvider::class);
        $this->app->register(ValidationServiceProvider::class);
        $this->app->register(HashServiceProvider::class);
        $this->app->register(TranslationServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(CensorServiceProvider::class);
        $this->app->register(SearchServiceProvider::class);

        $this->app->registerConfiguredProviders();

        $this->app->boot();
    }

    private function loadConfig() {
        return include $this->app->basePath('config/config.php');
    }

    private function getIlluminateConfig() {
        $config = new ConfigRepository(array_merge([
                'view' => [
                        'paths' => [
                            resource_path('views'),
                        ],
                        'compiled' => realpath(storage_path('views')),
                    ]
                ], [
                    'cache' => $this->app->config('cache'),
                    'filesystems' => $this->app->config('filesystems'),
                    'app' => [
                        'key' => $this->app->config('key'),
                        'cipher' => $this->app->config('cipher'),
                        'locale' => $this->app->config('locale'),
                        'fallback_locale' => $this->app->config('fallback_locale'),
                    ]
                ]
            )
        );

        return $config;
    }

    private function registerLogger()
    {
        $logPath = storage_path('logs/discuss.log');
        $handler = new RotatingFileHandler($logPath, Logger::INFO);
        $handler->setFormatter(new LineFormatter(null, null, true, true));

        $this->app->instance('log', new Logger($this->app->environment(), [$handler]));
        $this->app->alias('log', LoggerInterface::class);
    }

    protected function registerBaseEnv() {
        date_default_timezone_set($this->app->config('timezone', 'UTC'));
    }

}
