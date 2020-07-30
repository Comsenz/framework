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

namespace Discuz\Qcloud;

use Discuz\Contracts\Qcloud\Factory;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Qcloud\Services\BillingService;
use Discuz\Qcloud\Services\CaptchaService;
use Discuz\Qcloud\Services\CmsService;
use Discuz\Qcloud\Services\DiscuzCloudService;
use Discuz\Qcloud\Services\FaceidService;
use Discuz\Qcloud\Services\SmsService;
use Discuz\Qcloud\Services\VodService;
use Discuz\Qcloud\Services\YunsouService;
use Discuz\Qcloud\Services\MsService;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class QcloudManage extends Manager implements Factory
{
    protected $qcloudConfig;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->qcloudConfig = [];

        if ($container->has(SettingsRepository::class)) {
            $settings = $container->make(SettingsRepository::class);

            $this->qcloudConfig = collect($settings->tag('qcloud'))->map(function ($value) {
                return $value ? $value : null;
            });
        }
    }

    public function createBillingDriver()
    {
        return $this->buildService(BillingService::class, $this->qcloudConfig);
    }

    public function createCaptchaDriver()
    {
        return $this->buildService(CaptchaService::class, $this->qcloudConfig);
    }

    public function createVodDriver()
    {
        return $this->buildService(VodService::class, $this->qcloudConfig);
    }

    public function createCmsDriver()
    {
        return $this->buildService(CmsService::class, $this->qcloudConfig);
    }

    public function createSmsDriver()
    {
        $config = $this->container->config('sms');
        $config['gateways']['qcloud'] = [
            'sdk_app_id' => $this->container->make(SettingsRepository::class)->get('qcloud_sms_app_id', 'qcloud', true), // SDK APP ID
            'app_key' => $this->container->make(SettingsRepository::class)->get('qcloud_sms_app_key', 'qcloud'), // APP KEY
            'sign_name' => $this->container->make(SettingsRepository::class)->get('qcloud_sms_sign', 'qcloud'), // 短信签名，如果使用默认签名，该字段可缺省（对应官方文档中的sign）
        ];

        return $this->buildService(SmsService::class, $config);
    }

    public function createDiscuzCloudDriver()
    {
        $config = [
            'base_uri' => 'https://cloud.discuz.chat/api/',
            'timeout'  =>  15
        ];
        return $this->buildService(DiscuzCloudService::class, $config);
    }

    public function createYunsouDriver()
    {
        return $this->buildService(YunsouService::class, $this->qcloudConfig);
    }

    public function createFaceidDriver()
    {
        return $this->buildService(FaceidService::class, $this->qcloudConfig);
    }

    public function createMsDriver()
    {
        return $this->buildService(MsService::class, $this->qcloudConfig);
    }

    /**
     * @param $service
     * @param $config
     * @return mixed
     */
    public function buildService($service, $config)
    {
        return new $service($config);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Qcloud Service was specified.');
    }

    public function service($service)
    {
        return $this->driver($service);
    }
}
