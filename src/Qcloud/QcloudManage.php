<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Qcloud;

use Discuz\Contracts\Qcloud\Factory;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Http\UrlGenerator;
use Discuz\Qcloud\Services\BillingService;
use Discuz\Qcloud\Services\CheckVersionService;
use Discuz\Qcloud\Services\CmsService;
use Discuz\Qcloud\Services\SmsService;
use Discuz\Qcloud\Services\YunsouService;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class QcloudManage extends Manager implements Factory
{
    protected $qcloudConfig;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $settings = $container->make(SettingsRepository::class);

        $this->qcloudConfig = collect($settings->tag('qcloud'))->map(function ($value) {
            return $value ? $value : null;
        });
    }

    public function createBillingDriver()
    {
        return $this->buildService(BillingService::class, $this->qcloudConfig);
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

    public function createCheckVersionDriver()
    {
        $config = [
            'base_uri' => $this->container->make(UrlGenerator::class)->to('/api/'),
            'timeout'  =>  2
        ];
        return $this->buildService(CheckVersionService::class, $config);
    }

    public function createYunsouDriver()
    {
        return $this->buildService(YunsouService::class, $this->qcloudConfig);
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
