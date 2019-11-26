<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Qcloud\Services;

use Overtrue\EasySms\EasySms;

class SmsService
{
    protected $sms;

    public function __construct($config)
    {
        $this->sms = new EasySms($config);
    }

    public function send($to, $message, array $gateways = [])
    {
        return $this->sms->send($to, $message, $gateways);
    }
}
