<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Notifications;

use Illuminate\Contracts\Bus\Dispatcher as Bus;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Notifications\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Notifications\Factory as FactoryContract;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class ChannelManager extends Manager implements DispatcherContract, FactoryContract
{
    /**
     * The default channel used to deliver messages.
     *
     * @var string
     */
    protected $defaultChannel = 'database';

    /**
     * The locale used when sending notifications.
     *
     * @var string|null
     */
    protected $locale;

    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param \Illuminate\Support\Collection|array|mixed $notifiables
     * @param mixed $notification
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function send($notifiables, $notification)
    {
        return (new NotificationSender(
            $this,
            $this->container->make(Bus::class),
            $this->container->make(Dispatcher::class),
            $this->locale
        )
        )->send($notifiables, $notification);
    }

    /**
     * Send the given notification immediately.
     *
     * @param \Illuminate\Support\Collection|array|mixed $notifiables
     * @param mixed $notification
     * @param array|null $channels
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function sendNow($notifiables, $notification, array $channels = null)
    {
        return (new NotificationSender(
            $this,
            $this->container->make(Bus::class),
            $this->container->make(Dispatcher::class),
            $this->locale
        )
        )->sendNow($notifiables, $notification, $channels);
    }

    /**
     * Get a channel instance.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function channel($name = null)
    {
        return $this->driver($name);
    }

    /**
     * Create an instance of the database driver.
     *
     * @return \Illuminate\Notifications\Channels\DatabaseChannel
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createDatabaseDriver()
    {
        return $this->container->make(Channels\DatabaseChannel::class);
    }

    /**
     * Create an instance of the wechat driver.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createWechatDriver()
    {
        return $this->container->make(Channels\WechatChannel::class);
    }

    /**
     * Create a new driver instance.
     *
     * @param string $driver
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createDriver($driver)
    {
        try {
            return parent::createDriver($driver);
        } catch (InvalidArgumentException $e) {
            if (class_exists($driver)) {
                return $this->container->make($driver);
            }

            throw $e;
        }
    }

    /**
     * Get the default channel driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->defaultChannel;
    }

    /**
     * Get the default channel driver name.
     *
     * @return string
     */
    public function deliversVia()
    {
        return $this->getDefaultDriver();
    }

    /**
     * Set the default channel driver name.
     *
     * @param  string  $channel
     * @return void
     */
    public function deliverVia($channel)
    {
        $this->defaultChannel = $channel;
    }

    /**
     * Set the locale of notifications.
     *
     * @param  string  $locale
     * @return $this
     */
    public function locale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
