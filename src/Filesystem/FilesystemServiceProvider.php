<?php


namespace Discuz\Filesystem;

use Illuminate\Filesystem\FilesystemServiceProvider as ServiceProvider;
use League\Flysystem\Filesystem;
use Qcloud\Cos\Client;

class FilesystemServiceProvider extends ServiceProvider
{

    public function boot() {

        $this->app->make('filesystem')->extend('cos', function ($app, $config) {
            return new Filesystem(new CosAdapter($config));
        });
    }


}
