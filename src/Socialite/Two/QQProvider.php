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

class QQProvider extends AbstractProvider implements ProviderInterface
{

    const IDENTIFIER = 'QQ';

    /**
     * @var string
     */
    protected $openId;

    protected $scopes = ['get_user_info'];

    /**
     * get code
     * @inheritDoc
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://graph.qq.com/oauth2.0/authorize', $state);
    }

    /**
     * get access_token  redirect url
     * @inheritDoc
     */
    protected function getAccessTokenUrl($code)
    {
        return $this->buildTokenUrlFromBase('https://graph.qq.com/oauth2.0/token', $code);
    }

    protected function buildTokenUrlFromBase($url, $code)
    {
        return $url.'?'.http_build_query($this->getTokenFields($code), '', '&', $this->encodingType);
    }

    /**
     *
     * get open_id url
     * @return string
     */
    protected function getOpenIdUrl() {
        return 'https://graph.qq.com/oauth2.0/me';
    }

    /**
     * get user info url
     * @return string
     */
    protected function getUserInfoUrl() {
        return 'https://graph.qq.com/user/get_user_info';
    }

    protected function getCodeFields($state = null)
    {
        $guzzle = $this->guzzle;
        return [
            'response_type' => 'code',
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'scope'         => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'state'         => $state,
            'display'       => $guzzle['display']
        ];
    }

    protected function getTokenFields($code)
    {
        return [
            'grant_type' => 'authorization_code',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code'          => $code,
            'redirect_uri'  => $this->redirectUrl,
        ];
    }



    protected function getUserInfoFileds($token, $openId) {
        return [
            'access_token'          => $token,
            'oauth_consumer_key'    => $this->clientId,
            'openid'                => $openId
        ];
    }

    /**
     * 授权code跳转
     * @return \Psr\Http\Message\ResponseInterface|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect()
    {
        $state = null;
        if ($this->usesState() && $session = $this->request->getAttribute('session')) {
            $state = $this->getState();
            $token = $session::generate(static::IDENTIFIER, compact('state'));
            $token->save();
            $sessionId = http_build_query(['sessionId' => $token->token]);
            $this->redirectUrl = $this->redirectUrl.(strpos($this->redirectUrl, '?') ? '&'.$sessionId : '?'.$sessionId);
        }

        if($display = $this->request->getAttribute('display')) {
            $displayStr = http_build_query(['display' => $display]);
            $this->redirectUrl = $this->redirectUrl.(strpos( $this->redirectUrl, '?') ? '&'.$displayStr : '?'.$displayStr);
        }
        if($redirectUrl = $this->request->getAttribute('redirect')) {
            $this->redirectUrl($redirectUrl);
        }
        return DiscuzResponseFactory::RedirectResponse($this->getAuthUrl($state));
    }


    /**
     * get access_token
     * @return array
     */
    public function getAccessToken()
    {
        //判断state
        if ($this->hasInvalidState()) {
            throw new InvalidStateException();
        }
        $code = $this->request->getAttribute('code');
        //拼接URL
        $token_url = $this->getAccessTokenUrl($code);
        $response = file_get_contents($token_url);
        $arr = explode('&', $response);
        $msg = [];
        foreach ($arr as $item) {
            $oneline = explode('=', $item);
            $msg[$oneline[0]] = $oneline[1];
        }
        return $msg;
    }


    /**
     * 授权结束后跳转至获取用户信息接口
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function redirectUser($accessToken)
    {
        $state = null;
        $guzzle = $this->guzzle;
        $this->redirectUrl = $guzzle['redirect_user'];
        if ($this->usesState() && $session = $this->request->getAttribute('session')) {
            $state = $this->getState();
            $token = $session::generate(static::IDENTIFIER, compact('state'));
            $token->save();
            $sessionId = http_build_query(['sessionId' => $token->token]);
            $this->redirectUrl = $guzzle['redirect_user'].(strpos('?', $guzzle['redirect_user']) ? '&'.$sessionId : '?'.$sessionId);
        }
        return DiscuzResponseFactory::RedirectResponse($this->redirectUrl.'&access_token='.$accessToken.'&state='.$state);
    }



    protected function getUserByToken($token)
    {
        // TODO: Implement getUserByToken() method.
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        if ($this->hasInvalidState()) {
            throw new InvalidStateException();
        }
        $token = $this->request->getAttribute('access_token');
        $refreshToken = $this->request->getAttribute('refresh_token');
        $expiresIn = $this->request->getAttribute('expires_in');

        $openIdResponse = $this->getOpenId($token);
        $this->openId = $openIdResponse['openid'];
        $user = $this->mapUserToObject($this->getUserInfo($token));
        return $user->setToken($token)
            ->setRefreshToken($refreshToken)
            ->setExpiresIn($expiresIn);
    }

    /**
     * get openid by access_token
     * @param $token
     * @return mixed
     */
    public function getOpenId($token) {
        $graph_url = $this->getOpenIdUrl()."?access_token=".$token;
        $str  = file_get_contents($graph_url);
        if (strpos($str, "callback") !== false)
        {
            $lpos = strpos($str, "(");
            $rpos = strrpos($str, ")");
            $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
        }
        $user = json_decode($str, true);
        return $user;
    }

    /**
     * get user info by access_token and openid
     * @param $token
     * @return mixed
     */
    public function getUserInfo($token) {
        $response = $this->getHttpClient()->get($this->getUserInfoUrl(), [
            'query' => $this->getUserInfoFileds($token, $this->openId)
        ]);
        $this->credentialsResponseBody = json_decode($response->getBody(), true);
        return $this->credentialsResponseBody;
    }

    /**
     * @inheritDoc
     */
    protected function mapUserToObject(array $user)
    {
        if (!$user || !isset($user['ret']) || $user['ret'] !== 0) {
            throw new SocialiteException($user['msg'], $user['msg']);
        }
        return (new User())->setRaw($user)->map([
            'id'       => $this->openId,
            'nickname' => isset($user['nickname']) ? $user['nickname'] : null,
            'avatar'   => isset($user['figureurl_qq_1']) ? $user['figureurl_qq_1'] : null,
            'name'     => null,
            'email'    => null,
            'sex'      => isset($user['gender']) && $user['gender'] == '女' ? 2 : 1
        ]);
    }


    protected function getTokenUrl()
    {
        // TODO: Implement getTokenUrl() method.
    }
}
