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

namespace Discuz\Wechat\Offiaccount;

use App\Models\WechatOffiaccountReply;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MessageEventHandlerInterface
 *
 * 注册多个消息处理器
 *
 * @package Discuz\Wechat\Offiaccount
 */
abstract class MessageEventHandlerInterface implements EventHandlerInterface
{
    /**
     * @var
     */
    protected $reply = null;

    /**
     * 必须要实现 EasyWechat
     *
     * @var $easyWechat
     */
    protected $easyWechat;

    /**
     * @param mixed $payload
     */
    abstract public function handle($payload = null);

    /**
     * @param $name
     * @param $arguments
     * @return Image|News|Text|Video|Voice|string
     */
    public function __call($name, $arguments)
    {
        // 检测模型是否设值
        if (is_null($this->reply)) {
            return $this->error('数据库错误：40002');
        }

        // 检测 EasyWechat 是否设值
        if (empty($this->easyWechat)) {
            return $this->error('数据库错误：40001');
        }

        switch ($name) {
            case 'Text':
                return $this->textMessages();
            case 'Image':
                return $this->imageMessages();
            case 'Video':
                return $this->videoMessages();
            case 'Voice':
                return $this->voiceMessages();
            case 'News':
                return $this->newsMessages();
            case 'Error':
            default:
                return $this->error($arguments);
        }
    }

    public function textMessages($content = ''): Text
    {
        if (!empty($content)) {
            return new Text($content);
        }

        // 文本消息
        return new Text($this->reply->content);
    }

    public function imageMessages(): Image
    {
        // 图片消息
        return new Image($this->reply->media_id);
    }

    public function videoMessages()
    {
        $mediaId = $this->reply->media_id;

        $response = $this->easyWechat->material->get($mediaId);

        if (is_array($response) && array_key_exists('title', $response)) {
            // 视频消息
            return new Video($mediaId, [
                'title' => $response['title'],
                'description' => $response['description'],
                'down_url' => $response['down_url']
            ]);
        }

        return $this->textMessages($this->error());
    }

    public function voiceMessages(): Voice
    {
        return new Voice($this->reply->media_id); // 声音消息
    }

    public function newsMessages()
    {
        $mediaId = $this->reply->media_id;
        $response = $this->easyWechat->material->get($mediaId);

        // 只允许一条图文消息
        if (is_array($response) && array_key_exists('news_item', $response)) {
            $news = $response['news_item'];
            $item = array_shift($news);

            // 图文消息
            $items = [
                new NewsItem([
                    'title' => $item['title'],
                    'description' => $item['digest'],
                    'url' => $item['url'],
                    'image' => $item['thumb_url'],
                ]),
            ];

            return new News($items);
        }

        return $this->textMessages($this->error());
    }

    public function error($argSting = '')
    {
        return $argSting;
    }

    /**
     * 异步调试 Debug 方法
     * @param $data
     * @param string $param
     */
    protected function wechatDebugLog($data, $param = '')
    {
        $initLog = [
            'param' => $param,
            'path' => 'logs/wechatDebug.log',
            'append' => true
        ];

        // 判断是否是一个数组
        if (is_array($data)) {
            $write = var_export($data, true);
            $dataType = 'array';
        } elseif ($data instanceof WechatOffiaccountReply || $data instanceof Collection) {
            $write = var_export($data->toArray(), true);
            $dataType = 'object->toArray()';
        } else {
            $write = json_encode($data);
            $dataType = 'gettype(' . gettype($data) . ')';

            // object 打印单独处理
            if ($dataType == 'gettype(object)') {
                $this->writeObjectPath($initLog, $dataType);
                $initLog['path'] = 'logs/wechatDebugResourceObject.log'; // 单独写入
                $initLog['append'] = false;
                $write = var_export($data, true);
            }
        }

        $this->writeLog($write, $dataType, $initLog);
    }

    /**
     * @param $write
     * @param $dataType
     * @param array $params 数据参数、日志路径、是否叠加写入
     */
    protected function writeLog($write, $dataType, $params)
    {
        // 拼接信息格式
        $log = '>>>>>>>>>>>>========================================<<<<<<<<<<<<' . PHP_EOL;
        $log .= '[ 捕获时间 ]：' . date('Y-m-d H:s:i') . PHP_EOL;
        $log .= '[ 数据参数 ]：' . json_encode($params['param']) . PHP_EOL;
        $log .= '[ 数据类型 ]：' . $dataType . PHP_EOL;
        $log .= '[ 数据内容 ]：' . $write . PHP_EOL;

        $path = storage_path($params['path']);

        // 是否追加
        if ($params['append']) {
            file_put_contents($path, $log, FILE_APPEND);
        } else {
            file_put_contents($path, $log);
        }
    }

    protected function writeObjectPath($originInitLog, $dataType)
    {
        $source = '{ 对象打印值文件路径 => ' . storage_path($originInitLog['path']) . ' }';

        $this->writeLog($source, $dataType, [
            'param' => $originInitLog['param'],
            'path' => $originInitLog['path'],
            'append' => $originInitLog['append']
        ]);
    }
}
