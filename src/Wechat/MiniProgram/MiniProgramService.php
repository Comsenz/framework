<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Wechat\MiniProgram;

use Discuz\Contracts\Setting\SettingsRepository;
use EasyWeChat\MiniProgram\Application as MiniProgramApplication;

class MiniProgramService
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
     * MiniProgramService constructor.
     * @param $easyWechatFactory
     * @param $data
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct($easyWechatFactory, $data)
    {
        $this->settings = app()->make(SettingsRepository::class);

        $this->easyWechatFactory = $easyWechatFactory;

        $this->config = [
            'app_id' => $this->settings->get('miniprogram_app_id', 'wx_miniprogram'),
            'secret' => $this->settings->get('miniprogram_app_secret', 'wx_miniprogram'),
        ];
    }

    public function build($params = []) : MiniProgramApplication
    {
        $this->config = array_merge($this->config, $params);

        return $this->easyWechatFactory::miniProgram($this->config);
    }
}
