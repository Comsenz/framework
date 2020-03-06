<?php

namespace Discuz\Notifications\Channels;

use Discuz\Contracts\Setting\SettingsRepository;
use Illuminate\Notifications\Notification;
use EasyWeChat\Factory;
use Illuminate\Support\Arr;
use RuntimeException;

/**
 * 微信通知 - 驱动
 *
 * Class WechatChannel
 * @package Discuz\Notifications\Channels
 */
class WechatChannel
{
    protected $settings;

    /**
     * WechatChannel constructor.
     * @param SettingsRepository $settings
     */
    public function __construct(SettingsRepository $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Send the given notification.
     *
     * @param $notifiable
     * @param Notification $notification
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send($notifiable, Notification $notification)
    {
        if (!empty($notifiable->wechat)) {

            $templateID = $this->settings->get('offiaccount_template_id', 'wx_offiaccount');
            $appID = $this->settings->get('offiaccount_app_id', 'wx_offiaccount');
            $secret = $this->settings->get('offiaccount_app_secret', 'wx_offiaccount');

            if (blank($templateID) || blank($appID) || blank($secret)) {
                throw new RuntimeException('Notification is missing template_config');
            }

            // to user
            $toUser = $notifiable->wechat->mp_openid;

            // wechat post json
            $message = $notification->toWechat($notifiable);
            $message['content'] = json_decode($message['content'], true);

            // redirect
            $url = Arr::pull($message, 'content.redirect_url');

            $app = Factory::officialAccount([
                'app_id' => $appID,
                'secret' => $secret,
            ]);

            // send
            $app->template_message->send([
                'touser' => $toUser,
                'template_id' => $templateID,
                'url' => $url,
                'data' => $message['content']['data'],
            ]);
        }
    }

}
