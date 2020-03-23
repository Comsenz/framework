<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Socialite\Two;

use Discuz\Contracts\Socialite\Provider as ProviderInterface;
use Discuz\Http\DiscuzResponseFactory;
use Discuz\Socialite\Exception\SocialiteException;
use Illuminate\Support\Arr;

class WechatWebProvider extends AbstractProvider implements ProviderInterface
{
    const IDENTIFIER = 'WECHAT_WEB';

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['snsapi_login'];

    private $openId;

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://open.weixin.qq.com/connect/qrconnect', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.weixin.qq.com/sns/oauth2/access_token';
    }

    /**
     * @param string $code
     * @return array|mixed
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->get($this->getTokenUrl(), [
            'query' => $this->getTokenFields($code),
        ]);
        $this->credentialsResponseBody = json_decode($response->getBody(), true);
        if (isset($this->credentialsResponseBody['openid'])) {
            $this->openId = $this->credentialsResponseBody['openid'];
        }
        return $this->credentialsResponseBody;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.weixin.qq.com/sns/userinfo', [
            'query' => [
                'access_token' => $token, // HACK: Tencent use token in Query String, not in Header Authorization
                'openid'       => isset($this->credentialsResponseBody['openid']) ?
                    $this->credentialsResponseBody['openid'] : $this->openId, // HACK: Tencent need id
                'lang'         => 'zh_CN',
            ],
        ]);
        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        if ($user && isset($user['errcode'])) {
            throw new SocialiteException($user['errmsg'], $user['errcode']);
        }

        return (new User())->setRaw($user)->map([
            // HACK: use unionid as user id
            'id'       => $user['openid'],
            // HACK: Tencent scope snsapi_base only return openid
            'unionid' => isset($user['unionid']) ? $user['unionid'] : null,
            'nickname' => isset($user['nickname']) ? $user['nickname'] : null,
            'name'     => null,
            'email'    => null,
            'avatar'   => isset($user['headimgurl']) ? $user['headimgurl'] : null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return [
            'appid' => $this->clientId,
            'secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeFields($state = null)
    {
        $fields = parent::getCodeFields($state);
        unset($fields['client_id']);
        $fields['appid'] = $this->clientId; // HACK: Tencent use appid, not app_id or client_id
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    protected function formatScopes(array $scopes, $scopeSeparator)
    {
        // HACK: unionid is a faker scope for user id
        if (in_array('unionid', $scopes)) {
            unset($scopes[array_search('unionid', $scopes)]);
        }
        return implode($scopeSeparator, $scopes);
    }

}
