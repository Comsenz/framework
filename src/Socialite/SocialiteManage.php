<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Socialite;

use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Contracts\Socialite\Factory;
use Discuz\Http\UrlGenerator;
use Discuz\Socialite\Two\GithubProvider;
use Discuz\Socialite\Two\QQProvider;
use Discuz\Socialite\Two\QQWebProvider;
use Discuz\Socialite\Two\WechatQyProvider;
use Discuz\Socialite\Two\WechatProvider;
use Discuz\Socialite\Two\WechatWebProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SocialiteManage extends Manager implements Factory
{
    protected $request;

    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return Two\AbstractProvider
     */
    protected function createGithubDriver()
    {
        $config = [
            'client_id' => '',
            'client_secret' => '',
            'redirect' => '',
        ];
        return $this->buildProvider(
            GithubProvider::class,
            $config
        );
    }

    protected function createWechatDriver()
    {
        // 公众号接口(微信H5) 配置
        $config = [
            'client_id' => $this->container->make(SettingsRepository::class)->get('offiaccount_app_id', 'wx_offiaccount'),
            'client_secret' => $this->container->make(SettingsRepository::class)->get('offiaccount_app_secret', 'wx_offiaccount'),
            'redirect' => $this->container->make(UrlGenerator::class)->to('/wx-sign-up-bd')
        ];

        if ($redirect = Arr::get($this->request->getQueryParams(), 'redirect')) {
            $config['redirect'] = $redirect;
        }

        return $this->buildProvider(
            WechatProvider::class,
            $config
        );
    }

    protected function createWechatWebDriver()
    {
        // 微信PC登录
        $config = [
            'client_id' => $this->container->make(SettingsRepository::class)->get('oplatform_app_id', 'wx_oplatform'),
            'client_secret' => $this->container->make(SettingsRepository::class)->get('oplatform_app_secret', 'wx_oplatform'),
            'redirect' => $this->container->make(UrlGenerator::class)->to('/wx-sign-up-bd')
        ];

        if ($sessionId = $this->request->getAttribute('sessionId')) {
            $config['redirect'] = $config['redirect'].'?'.http_build_query(['sessionId' => $sessionId]);
        }

        return $this->buildProvider(
            WechatWebProvider::class,
            $config
        );
    }

    /**
     * 企业微信登录
     * @return Two\AbstractProvider
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createWechatQyDriver()
    {
        $config = [
            'client_id' => $this->container->make(SettingsRepository::class)->get('qy_oplatform_app_id', 'qy_wx_oplatform'),
            'client_secret' => $this->container->make(SettingsRepository::class)->get('qy_oplatform_app_secret', 'qy_wx_oplatform'),
            'redirect' => $this->container->make(UrlGenerator::class)->to('/qy-wx-sign-up-bd'),
        ];
        if ($sessionId = $this->request->getAttribute('sessionId')) {
            $config['redirect'] = $config['redirect'].'?'.http_build_query(['sessionId' => $sessionId]);
        }
        $config['guzzle'] = ['agentid' => $this->container->make(SettingsRepository::class)->get('qy_oplatform_agent_id', 'qy_wx_oplatform')];
        return $this->buildProvider(
            WechatQyProvider::class,
            $config
        );
    }

    /**
     * qq登录
     * @return Two\AbstractProvider
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createQQDriver()
    {
        $config = [
            'client_id' => $this->container->make(SettingsRepository::class)->get('qq_oplatform_app_id', 'qq_oplatform'),
            'client_secret' => $this->container->make(SettingsRepository::class)->get('qq_oplatform_app_key', 'qq_oplatform'),
            'redirect'  => $this->container->make(UrlGenerator::class)->to('/qq-sign-up-bd')
        ];
        if ($sessionId = $this->request->getAttribute('sessionId')) {
            $config['redirect'] = $config['redirect'].'?'.http_build_query(['sessionId' => $sessionId]);
        }
        $config['guzzle'] = [
            'display' => $this->request->getAttribute('display'),
            'redirect_user' => $this->container->make(UrlGenerator::class)->to('/qq-sign-up-user')
            ];
        return $this->buildProvider(
            QQProvider::class,
            $config
        );
    }


    /**
     * Build an OAuth 2 provider instance.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return \Discuz\Socialite\Two\AbstractProvider
     */
    public function buildProvider($provider, $config)
    {
        return new $provider(
            $this->request,
            $config['client_id'],
            $config['client_secret'],
            $this->formatRedirectUrl($config),
            Arr::get($config, 'guzzle', [])
        );
    }

    /**
     * Format the server configuration.
     *
     * @param  array  $config
     * @return array
     */
    public function formatConfig(array $config)
    {
        return array_merge([
            'identifier' => $config['client_id'],
            'secret' => $config['client_secret'],
            'callback_uri' => $this->formatRedirectUrl($config),
        ], $config);
    }

    /**
     * Format the callback URL, resolving a relative URI if needed.
     *
     * @param  array  $config
     * @return string
     */
    protected function formatRedirectUrl(array $config)
    {
        $redirect = value($config['redirect']);
        return Str::startsWith($redirect, '/')
            ? $this->container->make(UrlGenerator::class)->to($redirect)
            : $redirect;
    }

    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Socialite driver was specified.');
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }
}
