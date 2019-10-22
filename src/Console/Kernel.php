<?php

namespace Discuz\Console;

use Discuz\Api\ApiServiceProvider;
use Discuz\Auth\AuthServiceProvider;
use Discuz\Database\DatabaseServiceProvider;
use Discuz\Database\MigrationServiceProvider;
use Discuz\Filesystem\FilesystemServiceProvider;
use Discuz\Http\HttpServiceProvider;
use Discuz\Web\WebServiceProvider;
use Illuminate\Bus\BusServiceProvider;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Hashing\HashServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Discuz\Foundation\Application;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use ReflectionClass;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;

class Kernel implements KernelContract
{
    protected $app;

    protected $disco;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function run() {

        $this->siteBoot();

        $console = $this->getDisco();

        $this->load($console);

        exit($console->run());
    }

    protected function getName() {
        return <<<EOF
 _____   _                           _____   _                 
(____ \ (_)                         (____ \ (_)                
 _   \ \ _  ___  ____ _   _ _____    _   \ \ _  ___  ____ ___  
| |   | | |/___)/ ___) | | (___  )  | |   | | |/___)/ ___) _ \ 
| |__/ /| |___ ( (___| |_| |/ __/   | |__/ /| |___ ( (__| |_| |
|_____/ |_(___/ \____)\____(_____)  |_____/ |_(___/ \____)___/ 
EOF;
    }

    /**
     * Handle an incoming console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     * @return int
     */
    public function handle($input, $output = null)
    {
        // TODO: Implement handle() method.
    }

    /**
     * Run an Artisan console command by name.
     *
     * @param string $command
     * @param array $parameters
     * @param \Symfony\Component\Console\Output\OutputInterface|null $outputBuffer
     * @return int
     */
    public function call($command, array $parameters = [], $outputBuffer = null)
    {
        // TODO: Implement call() method.
    }

    /**
     * Queue an Artisan console command by name.
     *
     * @param string $command
     * @param array $parameters
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function queue($command, array $parameters = [])
    {
        // TODO: Implement queue() method.
    }

    /**
     * Get all of the commands registered with the console.
     *
     * @return array
     */
    public function all()
    {
        // TODO: Implement all() method.
    }

    /**
     * Get the output for the last run command.
     *
     * @return string
     */
    public function output()
    {
        // TODO: Implement output() method.
    }

    /**
     * Terminate the application.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param int $status
     * @return void
     */
    public function terminate($input, $status)
    {
        // TODO: Implement terminate() method.
    }

    public function getDisco(): ConsoleApplication {
        return $this->disco ?? $this->disco = new ConsoleApplication($this->getName(), Application::VERSION);
    }


    /**
     * @param ConsoleApplication $console
     * @throws \ReflectionException
     */
    protected function load(ConsoleApplication $console)
    {
        $paths = app_path('Console/Commands');
        $paths = array_unique(Arr::wrap($paths));
        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });
        if (empty($paths)) {
            return;
        }
        $namespace = $this->app->getNamespace();
        foreach ((new Finder)->in($paths)->files() as $command) {
            $command = $namespace.str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($command->getPathname(), realpath(app_path()).DIRECTORY_SEPARATOR)
                );
            if (is_subclass_of($command, Command::class) &&
                ! (new ReflectionClass($command))->isAbstract()) {
                $console->add($this->app->make($command));
            }
        }
    }

    protected function siteBoot() {

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
        $this->app->register(ApiServiceProvider::class);
        $this->app->register(WebServiceProvider::class);
        $this->app->register(BusServiceProvider::class);
        $this->app->register(ValidationServiceProvider::class);
        $this->app->register(HashServiceProvider::class);
        $this->app->register(TranslationServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);

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
