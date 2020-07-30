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
