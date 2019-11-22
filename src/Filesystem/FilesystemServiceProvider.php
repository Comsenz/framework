<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Filesystem;

use Discuz\Contracts\Setting\SettingsRepository;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Filesystem\FilesystemServiceProvider as ServiceProvider;
use League\Flysystem\Filesystem;

class FilesystemServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->make('filesystem')->extend('cos', function ($app, $config) {
            $settings = $app->make(SettingsRepository::class);

            $encrypter = $app->make(Encrypter::class);

            $config['credentials'] = collect($settings->tag('qcloud'))->map(function ($value) use ($encrypter) {
                return $value ? $encrypter->decrypt($value) : null;
            })->toArray();

            return new Filesystem(new CosAdapter($config));
        });
    }
}
