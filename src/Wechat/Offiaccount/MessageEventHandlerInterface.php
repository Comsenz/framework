<?php


namespace Discuz\Wechat\Offiaccount;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;

/**
 * Class MessageEventHandlerInterface
 * 注册多个消息处理器
 *
 * @package Discuz\Wechat\Offiaccount
 */
abstract class MessageEventHandlerInterface implements EventHandlerInterface
{
    /**
     * @param mixed $payload
     */
    abstract public function handle($payload = null);
}
