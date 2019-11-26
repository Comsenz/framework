<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Socialite;

use Discuz\Contracts\Socialite\Factory;
use Discuz\Socialite\Two\GithubProvider;
use Discuz\Socialite\Two\WeixinProvider;
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
            'client_id' => '20f568b9a248dbcd8d8e',
            'client_secret' => '582fe4e9fa3218251df6229f73cf73035c27402d',
            'redirect' => 'http://dev.discuss.com/github',
        ];
        return $this->buildProvider(
            GithubProvider::class,
            $config
        );
    }

    protected function createWeixinDriver()
    {
        $config = [
            'client_id' => 'wxba449971e7a27c1c',
            'client_secret' => '4b17fce50aabe26833c8ee201e5923bf',
            'redirect' => 'http://dev.discuss.com/api/oauth/weixin',
        ];
        return $this->buildProvider(
            WeixinProvider::class,
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
            ? $this->container['url']->to($redirect)
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
