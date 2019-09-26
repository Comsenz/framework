<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      1: EventGeneratorTrait.php 28830 2019-09-25 16:55 chenkeke $
 */

namespace Discuz\Foundation;

trait EventGeneratorTrait
{
    /**
     * 暂存将发生的事件.
     *
     * @var array
     */
    protected $pendingEvents = [];

    /**
     * 添加新事件.
     *
     * @param mixed $event
     */
    public function raise($event)
    {
        $this->pendingEvents[] = $event;
    }

    /**
     * 获取并清空暂存的事件.
     *
     * @return array
     */
    public function releaseEvents()
    {
        $events = $this->pendingEvents;
        $this->pendingEvents = [];
        return $events;
    }
}