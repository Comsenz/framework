<?php


namespace Discuz\Database;


use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->singleton(Manager::class, function ($app) {
            $manager = new Manager($app);

            $config = $app->config('database');
            $manager->addConnection($config, 'discuz.master');

            return $manager;
        });

        $this->app->singleton(ConnectionResolverInterface::class, function ($app) {
            $manager = $app->make(Manager::class);
            $manager->setAsGlobal();
            $manager->bootEloquent();

            $dbManager = $manager->getDatabaseManager();
            $dbManager->setDefaultConnection('discuz.master');

            return $dbManager;
        });

        $this->app->alias(ConnectionResolverInterface::class, 'db');

        $this->app->singleton(ConnectionInterface::class, function ($app) {
            $resolver = $app->make(ConnectionResolverInterface::class);

            return $resolver->connection();
        });

        $this->app->alias(ConnectionInterface::class, 'db.connection');
        $this->app->alias(ConnectionInterface::class, 'discuz.db');
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        Model::setConnectionResolver($this->app->make(ConnectionResolverInterface::class));
        Model::setEventDispatcher($this->app->make('events'));
    }
}

