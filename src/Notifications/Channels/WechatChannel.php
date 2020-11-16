<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Discuz\Notifications\Channels;

use Discuz\Contracts\Setting\SettingsRepository;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notification;
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
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws GuzzleException
     */
    public function send($notifiable, Notification $notification)
    {
        if (!empty($notifiable->wechat) && !empty($notifiable->wechat->mp_openid)) {

            // wechat post json
            $build = $notification->toWechat($notifiable);

            // 替换掉内容中的换行符
            $content = str_replace(PHP_EOL, '', Arr::get($build, 'content'));
            $build['content'] = json_decode($content, true);

            // get Wechat Template ID
            $notificationData = $notification->getTplModel('wechat');
            $templateID = $notificationData->template_id;

            $appID = $this->settings->get('offiaccount_app_id', 'wx_offiaccount');
            $secret = $this->settings->get('offiaccount_app_secret', 'wx_offiaccount');

            if (blank($templateID) || blank($appID) || blank($secret)) {
                throw new RuntimeException('notification_is_missing_template_config');
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
