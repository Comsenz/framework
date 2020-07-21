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

namespace Discuz\Wechat;

use Discuz\Contracts\Qcloud\Factory;
use Discuz\Wechat\MiniProgram\MiniProgramService;
use Discuz\Wechat\Offiaccount\OffiaccountService;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;
use InvalidArgumentException;
use EasyWeChat\Factory as EasyWechatFactory;

class EasyWechatManage extends Manager implements Factory
{
    /**
     * @var EasyWechatFactory
     */
    protected $easyWechatFactory;

    /**
     * @var mixed
     */
    protected $settings;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->easyWechatFactory = new EasyWechatFactory;
    }

    public function createOffiaccountDriver()
    {
        return $this->buildService(OffiaccountService::class, $this->easyWechatFactory);
    }

    public function createMiniProgramDriver()
    {
        return $this->buildService(MiniProgramService::class, $this->easyWechatFactory);
    }

    /**
     * @param $service
     * @param $factory
     * @param array $data
     * @return mixed
     */
    public function buildService($service, $factory, $data = [])
    {
        return new $service($factory, $data);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No EasyWechat Service was specified.');
    }

    public function service($service)
    {
        return $this->driver($service);
    }
}
