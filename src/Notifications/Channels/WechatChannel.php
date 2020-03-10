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

            // wechat post json
            $build = $notification->toWechat($notifiable);
            $build['content'] = json_decode(Arr::get($build, 'content'), true);

            $notificationData = $notification->getTplData(Arr::get($build, 'raw.tpl_id'));
            $templateID = $notificationData->template_id;

            $appID = $this->settings->get('offiaccount_app_id', 'wx_offiaccount');
            $secret = $this->settings->get('offiaccount_app_secret', 'wx_offiaccount');

            if (blank($templateID) || blank($appID) || blank($secret)) {
                throw new RuntimeException('Notification is missing template_config');
            }

            // to user
            $toUser = $notifiable->wechat->mp_openid;

            // redirect
            $url = Arr::pull($build, 'content.redirect_url');

            $app = Factory::officialAccount([
                'app_id' => $appID,
                'secret' => $secret,
            ]);

            // send
            $app->template_message->send([
                'touser' => $toUser,
                'template_id' => $templateID,
                'url' => $url,
                'data' => $build['content']['data'],
            ]);
        }
    }

}
