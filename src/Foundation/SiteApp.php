<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Foundation;

use Discuz\Api\ApiServiceProvider;
use Discuz\Auth\AuthServiceProvider;
use Discuz\Cache\CacheServiceProvider;
use Discuz\Database\DatabaseServiceProvider;
use Discuz\Database\MigrationServiceProvider;
use Discuz\Filesystem\FilesystemServiceProvider;
use Discuz\Http\Exception\NotConfig;
use Discuz\Http\HttpServiceProvider;
use Discuz\Qcloud\QcloudServiceProvider;
use Discuz\Queue\QueueServiceProvider;
use Discuz\Search\SearchServiceProvider;
use Discuz\Socialite\SocialiteServiceProvider;
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

class SiteApp
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function siteBoot()
    {
        $this->app->instance('env', 'production');
        $this->app->instance('discuz.config', $this->loadConfig());
        $this->app->instance('config', $this->getIlluminateConfig());

        $this->registerBaseEnv();
        $this->registerLogger();

        $this->app->register(HttpServiceProvider::class);
        $this->app->register(DatabaseServiceProvider::class);
        $this->app->register(MigrationServiceProvider::class);
        $this->app->register(FilesystemServiceProvider::class);
        $this->app->register(EncryptionServiceProvider::class);
        $this->app->register(CacheServiceProvider::class);
        $this->app->register(RedisServiceProvider::class);
        $this->app->register(ApiServiceProvider::class);
        $this->app->register(WebServiceProvider::class);
        $this->app->register(BusServiceProvider::class);
        $this->app->register(ValidationServiceProvider::class);
        $this->app->register(HashServiceProvider::class);
        $this->app->register(TranslationServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(SearchServiceProvider::class);
        $this->app->register(QcloudServiceProvider::class);
        $this->app->register(QueueServiceProvider::class);
        $this->app->register(SocialiteServiceProvider::class);

        $this->registerServiceProvider();

        $this->app->registerConfiguredProviders();

        $this->app->boot();

        return $this->app;
    }

    protected function registerServiceProvider()
    {
    }

    private function loadConfig()
    {
        if(file_exists($path = $this->app->basePath('config/config.php'))) {
            return include $path;
        }

        return [];
    }

    private function getIlluminateConfig()
    {

        $discuzConfig = [
            'queue' => $this->app->config('queue'),
            'filesystems' => $this->app->config('filesystems'),
            'app' => [
                'key' => $this->app->config('key'),
                'cipher' => $this->app->config('cipher'),
                'locale' => $this->app->config('locale'),
                'fallback_locale' => $this->app->config('fallback_locale'),
            ]
        ];

        if($this->app->config('cache')) {
            $discuzConfig['cache'] = $this->app->config('cache');
        }

        $config = new ConfigRepository(
            array_merge(
            [
                    'database' => [
                        'default' => 'mysql',
                        'migrations' => 'migrations',
                        'redis' => $this->app->config('redis'),
                        'connections' => [
                            'mysql' => $this->app->config('database'),
                            'discuz_mysql' => $this->app->config('database')
                        ]
                    ],
                    'cache' => [
                        'default' => 'file', //如果配置的 redis 可用， 会自动切换为redis

                        'stores' => [
                            'file' => [
                                'driver' => 'file',
                                'path' => storage_path('cache/data'),
                            ],
                            'redis' => [
                                'driver' => 'redis',
                                'connection' => 'cache',
                            ],
                        ],

                        'prefix' => 'discuz_cache',

                    ],
                    'view' => [
                        'paths' => [
                            resource_path('views'),
                        ],
                        'compiled' => realpath(storage_path('views')),
                    ]
                ],
                $discuzConfig
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

    protected function registerBaseEnv()
    {
        date_default_timezone_set($this->app->config('timezone', 'UTC'));
    }
}
