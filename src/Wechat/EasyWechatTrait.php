<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Wechat;

/**
 * Trait EasyWechatTrait
 *
 * @package Discuz\Wechat
 * @property \Discuz\Wechat\EasyWechatManage
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
     * @return mixed
     */
    public function offiaccount($merge = [])
    {
        return $this->getFactory()->service('offiaccount')->build($merge);
    }

    public function miniProgram($merge = [])
    {
        return $this->getFactory()->service('miniProgram')->build($merge);
    }
}
