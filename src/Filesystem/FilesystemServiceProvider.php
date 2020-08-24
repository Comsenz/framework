<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Discuz\Filesystem;

use Discuz\Contracts\Setting\SettingsRepository;
use GuzzleHttp\Client;
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
        $this->app->make('filesystem')->extend('local', function ($app, $config) {
            return new Filesystem(new LocalAdapter($config));
        });

        $this->app->make('filesystem')->extend('cos', function ($app, $config) {
            $settings = $this->app->make(SettingsRepository::class);
            $qcloud = $settings->tag('qcloud');

             $server = $app['request']->getServerParams();

             $container = Arr::get($server, 'KUBERNETES_SERVICE_HOST');

            if(!is_null($container) && !Arr::get($qcloud, 'qcloud_cos')) {
                $data = $this->getTmpSecret($app);
                $qcloud['qcloud_secret_id'] = Arr::get($data, 'TmpSecretId');
                $qcloud['qcloud_secret_key'] = Arr::get($data, 'TmpSecretKey');
            }

            $config = array_merge($config, $app->config('filesystems.disks.cos'));

            $config['region'] = Arr::get($qcloud, 'qcloud_cos_bucket_area');
            $config['bucket'] = Arr::get($qcloud, 'qcloud_cos_bucket_name');
            $config['cdn'] = Arr::get($qcloud, 'qcloud_cos_cdn_url', '');

            $config['credentials'] = [
                'secretId'  => Arr::get($qcloud, 'qcloud_secret_id'),  //"云 API 密钥 SecretId";
                'secretKey' => Arr::get($qcloud, 'qcloud_secret_key'), //"云 API 密钥 SecretKey";
                'token' => ''
            ];

            return new Filesystem(new CosAdapter($config));
        });
    }


    private function getTmpSecret($app) {
        $data = $app['cache']->get('tmp.secret');

        if(!is_null($data)) {
            return $data;
        }

        $client =  new Client();
        $response = $client->request('GET', 'http://metadata.tencentyun.com/meta-data/cam/securitycredentials/TCB_QcsRole');
        $data = json_decode($response);

        $app['cache']->put('tmp.secret', $data, $data['ExpiredTime'] - 10);

        return $data;
    }
}
