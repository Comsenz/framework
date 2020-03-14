<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Filesystem;

use Discuz\Contracts\Setting\SettingsRepository;
use Illuminate\Filesystem\FilesystemServiceProvider as ServiceProvider;
use Illuminate\Support\Arr;
use League\Flysystem\Filesystem;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $this->app->make('filesystem')->extend('cos', function ($app, $config) {
            $settings = $this->app->make(SettingsRepository::class);
            $qcloud = $settings->tag('qcloud');

            $config = array_merge($config, $app->config('filesystems.disks.cos'));

            $config['region'] = Arr::get($qcloud, 'qcloud_cos_bucket_area');
            $config['bucket'] = Arr::get($qcloud, 'qcloud_cos_bucket_name');
            $config['ciurl'] = Arr::get($qcloud, 'qcloud_ci_url', '');

            $config['credentials'] = [
                'secretId'  => Arr::get($qcloud, 'qcloud_secret_id'),  //"云 API 密钥 SecretId";
                'secretKey' => Arr::get($qcloud, 'qcloud_secret_key'), //"云 API 密钥 SecretKey";
                'token' => ''
            ];

            return new Filesystem(new CosAdapter($config));
        });
    }
}
