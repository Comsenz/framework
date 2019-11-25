<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Qcloud;

trait QcloudTrait
{
    protected $qcloud;

    public function smsSend($to, $message, array $gateways = [])
    {
        return $this->getQcloud()->service('sms')->send($to, $message, $gateways);
    }

    public function imageModeration($param)
    {
        return $this->getQcloud()->service('cms')->ImageModeration($param);
    }

    public function textModeration($content = '')
    {
        return $this->getQcloud()->service('cms')->textModeration($content);
    }

    public function describeAccountBalance()
    {
        return $this->getQcloud()->service('billing')->DescribeAccountBalance();
    }

    public function checkVersion($params = [])
    {
        return $this->getQcloud()->service('checkversion')->checkVersion($params);
    }

    private function getQcloud()
    {
        return $this->qcloud ?? $this->qcloud = app('qcloud');
    }
}
