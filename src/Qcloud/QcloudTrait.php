<?php


namespace Discuz\Qcloud;


trait QcloudTrait
{
    protected $qcloud;

    public function smsSend($to, $message, array $gateways = []) {
        return $this->getQcloud()->service('sms')->send($to, $message, $gateways);
    }

    public function imageModeration($param) {
        return $this->getQcloud()->service('cms')->ImageModeration($param);
    }

    public function textModeration($content = '') {
        return $this->getQcloud()->service('cms')->textModeration($content);
    }

    private function getQcloud() {
        return $this->qcloud ?? $this->qcloud = app('qcloud');
    }
}
