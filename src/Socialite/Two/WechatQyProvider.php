<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */
namespace Discuz\Socialite\Two;

use Discuz\Contracts\Socialite\Provider as ProviderInterface;
use Discuz\Http\DiscuzResponseFactory;
use Discuz\Socialite\Exception\InvalidStateException;
use Discuz\Socialite\Exception\SocialiteException;
use Illuminate\Support\Arr;
use TencentCloud\Cdb\V20170320\Models\VerifyRootAccountRequest;

class WechatQyProvider extends AbstractProvider implements ProviderInterface
{

    const IDENTIFIER = 'QY_WECHAT';

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['snsapi_userinfo'];

    private $openId;

    public $accessToken;

    /**
     * @inheritDoc
     */
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://open.weixin.qq.com/connect/oauth2/authorize', $state);
    }

    /**
     * @inheritDoc
     */
    protected function getTokenUrl()
    {
        return 'https://qyapi.weixin.qq.com/cgi-bin/gettoken';
    }

    protected function getUserInfoUrl() {
        return'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo';
    }

    protected function getUserInfoDetailUrl() {
        return 'https://qyapi.weixin.qq.com/cgi-bin/user/get';
    }




    /**
     * @inheritDoc
     */
    protected function getUserByToken($token)
    {
        // TODO: Implement getUserByToken() method.
    }




    /**
     * get userId or openId
     * @param $token
     * @param $code
     */
    protected function  getUserByTokenAndCode($token, $code)
    {

    }


    protected function getAccessTokenFields()
    {
        return [
            'corpid' => $this->clientId,
            'corpsecret' => $this->clientSecret,
        ];
    }

    protected function getUserFields($token, $code) {
        return [
            'access_token' => $token,
            'code' => $code,
        ];
    }

    protected function getUserDeatilFields($token, $userId) {
        return [
            'access_token' => $token,
            'userid' => $userId
        ];
    }



    protected function getCodeFields($state = null)
    {
        $guzzle = $this->guzzle;
        $fields = [
            'appid' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => $this->formatScopes($this->getScopes(), $this->scopeSeparator),
            'response_type' => 'code',
            'agentid' => $guzzle['agentid'],
        ];
        if ($this->usesState()) {
            $fields['state'] = $state;
        }
        return array_merge($fields, $this->parameters);
    }


    /**
     * get qy wechat access_token
     * @return mixed
     */
    public function getAccessToken() {
        $response = $this->getHttpClient()->get($this->getTokenUrl(), [
            'query' => $this->getAccessTokenFields(),
        ]);
        $this->credentialsResponseBody = json_decode($response->getBody(), true);

        if (isset($this->credentialsResponseBody['errcode']) && $this->credentialsResponseBody['errcode'] == 0) {
            $this->accessToken = $this->credentialsResponseBody['access_token'];
        }
        return $this->credentialsResponseBody;
    }


    /**
     * @param $token
     * @param $code
     * @return mixed
     */
    public function getWechatQyUser($token, $code) {
        $response = $this->getHttpClient()->get($this->getUserInfoUrl(), [
            'query' => $this->getUserFields($token, $code)
        ]);
        $this->credentialsResponseBody = json_decode($response->getBody(), true);

        return $this->credentialsResponseBody;
    }


    public function getWechatQyUserDetail($token, $userId) {
        $response = $this->getHttpClient()->get($this->getUserInfoDetailUrl(), [
            'query' => $this->getUserDeatilFields($token, $userId)
        ]);
        $this->credentialsResponseBody = json_decode($response->getBody(), true);
        return $this->credentialsResponseBody;
    }

    public function convertOpenIdToUserId($token, $openId) {
        $url = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_userid?access_token='.$token;
        $response = $this->getHttpClient()->post($url, [
            'json' => ['openid' => $openId]
        ]);
        $this->credentialsResponseBody = json_decode($response->getBody(), true);

        return $this->credentialsResponseBody;
    }


    /**
     * {@inheritdoc}
     */
    public function user()
    {
        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
        }
        $response = $this->getAccessToken();
        $token = Arr::get($response, 'access_token');

        $userInfo = $this->getWechatQyUser($token, $this->getCode());
        if(isset($userInfo['OpenId'])) {
            $userIdResponse = $this->convertOpenIdToUserId($token, $userInfo['OpenId']);
            $userId = (isset($userIdResponse['errcode']) && $userIdResponse['errcode'] == 0) ? $userIdResponse['userid'] : '';
        } else {
            $userId = $userInfo['UserId'];
        }

        $user = $this->mapUserToObject($this->getWechatQyUserDetail($token, $userId));
        return $user->setToken($token)
            ->setRefreshToken($token)
            ->setExpiresIn(Arr::get($response, 'expires_in'));
    }

    /**
     * @param array $user
     * @return User|mixed
     * @throws SocialiteException
     */
    protected function mapUserToObject(array $user)
    {
        if (!$user || !isset($user['errcode']) || $user['errcode'] !== 0) {
            throw new SocialiteException($user['errmsg'], $user['errcode']);
        }

        return (new User())->setRaw($user)->map([
            'id'       => $this->clientId.'_'.$user['userid'],
            'nickname' => isset($user['name']) ? $user['name'] : null,
            'avatar'   => isset($user['avatar']) ? $user['avatar'] : null,
            'name'     => null,
            'email'    => isset($user['email']) ? $user['email'] : null,
            'sex'      => isset($user['gender']) ? $user['gender'] : null
        ]);
    }


}
