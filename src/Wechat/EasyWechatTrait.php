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

/**
 * Trait EasyWechatTrait
 *
 * @package Discuz\Wechat
 * @property EasyWechatManage
 * @method createOffiaccountDriver()
 * @method createMiniProgramDriver()
 */
trait EasyWechatTrait
{
    protected $easyWechatFactory;

    private function getFactory()
    {
        return $this->easyWechatFactory ?? $this->easyWechatFactory = app('easyWechat');
    }

    /**
     * @param array $merge
     * @return \EasyWeChat\OfficialAccount\Application
     */
    public function offiaccount($merge = [])
    {
        return $this->getFactory()->service('offiaccount')->build($merge);
    }

    /**
     * @param array $merge
     * @return \EasyWeChat\MiniProgram\Application
     */
    public function miniProgram($merge = [])
    {
        return $this->getFactory()->service('miniProgram')->build($merge);
    }
}
