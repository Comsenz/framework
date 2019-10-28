<?php


namespace Discuz\Qcloud\Services;


use Overtrue\EasySms\EasySms;

class SmsService
{
    protected $sms;

    public function __construct($config)
    {
        $this->sms = new EasySms($config);
    }

    public function send($to, $message, array $gateways = []) {
        return $this->sms->send($to, $message, $gateways);
    }
}
