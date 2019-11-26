<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Foundation;

use Illuminate\Contracts\Events\Dispatcher;

trait EventsDispatchTrait
{
    /**
     * 事件类.
     *
     * @var Dispatcher
     */
    protected $events;

    /**
     * 分发并清空某一个实体的所有暂存事件.
     *
     * @param object $entity
     * @param User $actor
     */
    public function dispatchEventsFor($entity, $actor = null)
    {
        foreach ($entity->releaseEvents() as $event) {
            isset($actor) && $event->actor = $actor;
            $this->events->dispatch($event);
        }
    }
}
