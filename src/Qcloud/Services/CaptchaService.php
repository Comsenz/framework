<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Qcloud\Services;

use TencentCloud\Captcha\V20190722\CaptchaClient;
use TencentCloud\Captcha\V20190722\Models\DescribeCaptchaResultRequest;

class CaptchaService extends AbstractService
{
    const ENDPOINT = 'captcha.tencentcloudapi.com';

    const REGION = '';

    protected $captchaAppId;

    protected $captchaAppSecretKey;

    public function __construct($config)
    {
        parent::__construct($config);

        $this->captchaAppId = (int)$config->get('qcloud_captcha_app_id');
        $this->captchaAppSecretKey = $config->get('qcloud_captcha_secret_key');
    }

    /**
     * Action       是 String  公共参数，本接口取值：DescribeCaptchaResult
     * Version      是 String  公共参数，本接口取值：2019-07-22
     * Region       否 String  公共参数，本接口不需要传递此参数。
     * CaptchaType  是 Integer 验证码类型，9：滑块验证码
     * Ticket       是 String  验证码返回给用户的票据
     * UserIp       是 String  用户操作来源的外网 IP
     * Randstr      是 String  验证票据需要的随机字符串
     * CaptchaAppId 是 Integer 验证码应用ID
     * AppSecretKey 是 String  用于服务器端校验验证码票据的验证密钥，请妥善保密，请勿泄露给第三方
     * BusinessId   否 Integer 业务 ID，网站或应用在多个业务中使用此服务，通过此 ID 区分统计数据
     * SceneId      否 Integer 场景 ID，网站或应用的业务下有多个场景使用此服务，通过此 ID 区分统计数据
     * MacAddress   否 String  mac 地址或设备唯一标识
     * Imei         否 String  手机设备号
     *
     * @param $ticket
     * @param $randStr
     * @param string $ip
     * @return array
     */
    public function describeCaptchaResult($ticket, $randStr, $ip = '')
    {
        $clientRequest = new DescribeCaptchaResultRequest();

        $params = [
            'CaptchaType' => 9,
            'Ticket' => $ticket,
            'UserIp' => $ip,
            'Randstr' => $randStr,
            'CaptchaAppId' => $this->captchaAppId,
            'AppSecretKey' => $this->captchaAppSecretKey,
        ];

        $clientRequest->fromJsonString(json_encode($params));

        return $this->client->DescribeCaptchaResult($clientRequest)->serialize();
    }

    protected function getClient()
    {
        return new CaptchaClient($this->cred, self::REGION, $this->clientProfile);
    }

    protected function setEndpoint()
    {
        return self::ENDPOINT;
    }
}
