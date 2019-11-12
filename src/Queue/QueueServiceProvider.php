<?php

namespace Discuz\Queue;

use Discuz\Console\Event\Configuring;
use Illuminate\Contracts\Debug\ExceptionHandler as ContractsExceptionHandler;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Queue\Connectors\RedisConnector;
use Illuminate\Queue\Failed\NullFailedJobProvider;
use Illuminate\Queue\QueueManager;
use Illuminate\Queue\Worker;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Opis\Closure\SerializableClosure;
use Illuminate\Queue\Console;
use Illuminate\Queue\Listener as QueueListener;
use Discuz\Foundation\ExceptionHandler;

class QueueServiceProvider extends ServiceProvider implements DeferrableProvider
{

    protected $commands = [
        Console\FlushFailedCommand::class,
        Console\ForgetFailedCommand::class,
        Console\ListenCommand::class,
        Console\ListFailedCommand::class,
//        Console\RestartCommand::class,
        Console\RetryCommand::class,
        Console\WorkCommand::class,
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton(ContractsExceptionHandler::class, function($app) {
            return new ExceptionHandler($app['log']);
        });

        $this->registerManager();
        $this->registerConnection();
        $this->registerWorker();
        $this->registerListener();
        $this->registerFailedJobServices();
        $this->registerOpisSecurityKey();
    }
    /**
     * Register the queue manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('queue', function ($app) {
            // Once we have an instance of the queue manager, we will register the various
            // resolvers for the queue connectors. These connectors are responsible for
            // creating the classes that accept queue configs and instantiate queues.
            return tap(new QueueManager($app), function ($manager) {
                $this->registerConnectors($manager);
            });
        });
    }
    /**
     * Register the default queue connection binding.
     *
     * @return void
     */
    protected function registerConnection()
    {
        $this->app->singleton('queue.connection', function ($app) {
            return $app['queue']->connection();
        });
    }
    /**
     * Register the connectors on the queue manager.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    public function registerConnectors($manager)
    {
        foreach (['Redis'] as $connector) {
            $this->{"register{$connector}Connector"}($manager);
        }
    }

    /**
     * Register the Redis queue connector.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    protected function registerRedisConnector($manager)
    {
        $manager->addConnector('redis', function () {
            return new RedisConnector($this->app['redis']);
        });
    }

    /**
     * Register the queue worker.
     *
     * @return void
     */
    protected function registerWorker()
    {
        $this->app->singleton('queue.worker', function () {
            $isDownForMaintenance = function () {
                return $this->app->isDownForMaintenance();
            };
            return new Worker(
                $this->app['queue'],
                $this->app['events'],
                $this->app[ExceptionHandler::class],
                $isDownForMaintenance
            );
        });

        $this->app->alias( 'queue.worker', Worker::class);
    }
    /**
     * Register the queue listener.
     *
     * @return void
     */
    protected function registerListener()
    {
        $this->app->singleton(QueueListener::class, function () {
            return new Listener($this->app->basePath());
        });

        $this->app->alias(Listener::class, 'queue.listener');
    }
    /**
     * Register the failed job services.
     *
     * @return void
     */
    protected function registerFailedJobServices()
    {
        $this->app->singleton('queue.failer', function () {
            return new NullFailedJobProvider;
        });
    }

    /**
     * Configure Opis Closure signing for security.
     *
     * @return void
     */
    protected function registerOpisSecurityKey()
    {
        if (Str::startsWith($key = $this->app->config('key'), 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        SerializableClosure::setSecretKey($key);
    }

    protected function registerCommands()
    {
        $this->app['events']->listen(Configuring::class, function (Configuring $event) {
            foreach ($this->commands as $command) {
                $event->addCommand($command);
            }
        });
    }

    public function boot()
    {
        $this->registerCommands();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'queue', 'queue.worker', 'queue.listener',
            'queue.failer', 'queue.connection',
        ];
    }
}
