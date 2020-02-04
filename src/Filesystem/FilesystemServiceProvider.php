<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Filesystem;

use App\Api\Controller\Users\ListUsersController;
use App\Api\Serializer\AttachmentSerializer;
use App\Tools\AttachmentUploadTool;
use App\Tools\ImageUploadTool;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Foundation\Application;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Filesystem\FilesystemServiceProvider as ServiceProvider;
use Illuminate\Support\Arr;
use League\Flysystem\Filesystem;
use Illuminate\Contracts\Filesystem\Filesystem as ContractsFilesystem;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $settings = $this->app->make(SettingsRepository::class);
        $qcloud = $settings->tag('qcloud');

        $this->app->make('filesystem')->extend('cos', function ($app, $config) use ($qcloud) {

            $config = array_merge($config, $app->config('filesystems.disks.cos'));

            $config['region'] = Arr::get($qcloud, 'qcloud_cos_bucket_area');
            $config['bucket'] = Arr::get($qcloud, 'qcloud_cos_bucket_name');

            $config['credentials'] = [
                'secretId'  => Arr::get($qcloud, 'qcloud_secret_id'),  //"云 API 密钥 SecretId";
                'secretKey' => Arr::get($qcloud, 'qcloud_secret_key'), //"云 API 密钥 SecretKey";
                'token' => ''
            ];

            return new Filesystem(new CosAdapter($config));
        });

        if(Arr::get($qcloud, 'qcloud_cos', false)) {
            $this->app->when([AttachmentUploadTool::class, ImageUploadTool::class, AttachmentSerializer::class])->needs(ContractsFilesystem::class)->give(function (Application $app) {
                return $app->make(Factory::class)->disk('attachment');
            });
        }

    }
}
