<?php


namespace Discuz\Wechat\Offiaccount;

use App\Models\WechatOffiaccountReply;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use EasyWeChat\OfficialAccount\Application;
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
     * @var Application
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
        switch ($name) {
            case 'Text': return $this->textMessages();
            case 'Image': return $this->imageMessages();
            case 'Video': return $this->videoMessages();
            case 'Voice': return $this->voiceMessages();
            case 'News': return $this->newsMessages();
            case 'Error':
            default: return $this->error($arguments);
        }
    }

    public function textMessages($content = '') : Text
    {
        if (!empty($content)) {
            return new Text($content);
        }

        // 文本消息
        return new Text($this->reply->content);
    }

    public function imageMessages() : Image
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

    public function voiceMessages() : Voice
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
    public function wechatDebugLog($data, $param = '')
    {
        // 判断是否是一个数组
        if (is_array($data)) {
            $write = var_export($data, true);
            $dataType = 'array';
        } elseif ($data instanceof WechatOffiaccountReply || $data instanceof Collection) {
            $write = var_export($data->toArray(), true);
            $dataType = 'object';
        } else {
            $write = json_encode($data);
            $dataType = 'gettype(' . gettype($data) . ')';
        }

        $write = $write . PHP_EOL;

        // 拼接信息格式
        $log = '>>>>>>>>>>>>========================================<<<<<<<<<<<<' . PHP_EOL;
        $log .= '[ 捕获时间 ]：' . date('Y-m-d H:s:i') . PHP_EOL;
        $log .= '[ 数据参数 ]：' . json_encode($param) . PHP_EOL;
        $log .= '[ 数据类型 ]：' . $dataType . PHP_EOL;

        $path = storage_path('logs/wechatDebug.log');

        file_put_contents($path, $log, FILE_APPEND);
        file_put_contents($path, $write, FILE_APPEND);
    }
}
