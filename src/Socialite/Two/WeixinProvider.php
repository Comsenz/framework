<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Socialite\Two;

use Discuz\Contracts\Socialite\Provider as ProviderInterface;
use Discuz\Socialite\Exception\SocialiteException;

class WeixinProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * @var string
     */
    protected $openId;

    protected $scopes = ['snsapi_userinfo'];

    /**
     * @param string $code
     *
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

    protected function getCodeFields($state = null)
    {
        return [
            'appid' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'response_type' => 'code',
            'scope' => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'state' => $state,
        ];
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param string $state
     *
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://open.weixin.qq.com/connect/oauth2/authorize', $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return 'https://api.weixin.qq.com/sns/oauth2/access_token';
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param string $token
     *
     * @return array
     */
    protected function getUserByToken($token)
    {
        if (\in_array('snsapi_base', $this->scopes, true)) {
            $user = ['openid' => $this->openId];
        } else {
            $response = $this->getHttpClient()->get('https://api.weixin.qq.com/sns/userinfo', [
                'query' => [
                    'access_token' => $token,
                    'openid' => $this->openId,
                    'lang' => 'zh_CN',
                ],
            ]);
            $user = json_decode($response->getBody(), true);
        }

        return $user;
    }

    /**
     * @throws SocialiteException
     *
     * @return mixed|User
     */
    protected function mapUserToObject(array $user)
    {
        if ($user && isset($user['errcode'])) {
            throw new SocialiteException($user['errmsg'], $user['errcode']);
        }

        return (new User())->setRaw($user)->map([
            'id' => $user['openid'],
            'unionid' => isset($user['unionid']) ? $user['unionid'] : null,
            'nickname' => isset($user['nickname']) ? $user['nickname'] : null,
            'avatar' => isset($user['headimgurl']) ? $user['headimgurl'] : null,
            'name' => null,
            'email' => null,
        ]);
    }

    /**
     * {@inheritdoc}.
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
}
