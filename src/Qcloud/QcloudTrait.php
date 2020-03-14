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

    public function describeCaptchaResult($ticket, $randStr, $ip = '')
    {
        return $this->getQcloud()->service('captcha')->describeCaptchaResult($ticket, $randStr, $ip);
    }

    public function checkVersion($params = [])
    {
        return $this->getQcloud()->service('discuzcloud')->checkVersion($params);
    }

    public function report($params = [])
    {
        return $this->getQcloud()->service('discuzcloud')->report($params);
    }

    public function deleteVodMedia($file_id)
    {
        return $this->getQcloud()->service('vod')->deleteMedia($file_id);
    }

    public function transcodeVideo($file_id)
    {
        return $this->getQcloud()->service('vod')->transcodeVideo($file_id);
    }

    private function getQcloud()
    {
        return $this->qcloud ?? $this->qcloud = app('qcloud');
    }
}
