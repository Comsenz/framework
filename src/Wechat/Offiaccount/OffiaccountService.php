<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Wechat\Offiaccount;

use Discuz\Contracts\Setting\SettingsRepository;
use EasyWeChat\OfficialAccount\Application as OfficialAccountApplication;

class OffiaccountService
{
    /**
     * @var $config
     */
    protected $config;

    /**
     * @var mixed
     */
    protected $settings;

    /**
     * @var mixed
     */
    protected $easyWechatFactory;

    /**
     * OffiaccountService constructor.
     * @param $easyWechatFactory
     * @param $data
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct($easyWechatFactory, $data)
    {
        $this->settings = app()->make(SettingsRepository::class);

        $this->easyWechatFactory = $easyWechatFactory;

        $this->config = [
            'app_id' => $this->settings->get('offiaccount_app_id', 'wx_offiaccount'),
            'secret' => $this->settings->get('offiaccount_app_secret', 'wx_offiaccount'),
        ];
    }

    public function build($params = []) : OfficialAccountApplication
    {
        $this->config = array_merge($this->config, $params);

        return $this->easyWechatFactory::officialAccount($this->config);
    }
}
