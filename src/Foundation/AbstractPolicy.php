<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Foundation;

use Discuz\Api\Events\GetPermission;
use Discuz\Api\Events\ScopeModelVisibility;
use Discuz\Contracts\Policy\Policy;
use Illuminate\Contracts\Events\Dispatcher;

abstract class AbstractPolicy implements Policy
{
    /**
     * @var string
     */
    protected $model;

    public function subscribe(Dispatcher $events)
    {
        $events->listen(GetPermission::class, [$this, 'getPermission']);
        $events->listen(ScopeModelVisibility::class, [$this, 'scopeModelVisibility']);
    }

    /**
     * @return bool|void
     */
    public function getPermission(GetPermission $event)
    {
        if (!$event->model instanceof $this->model) {
            return;
        }

        if (method_exists($this, $event->ability)) {
            $result = \call_user_func_array([$this, $event->ability], [$event->actor, $event->model]);

            if (null !== $result) {
                return $result;
            }
        }

        if (method_exists($this, 'can')) {
            return \call_user_func_array([$this, 'can'], [$event->actor, $event->ability, $event->model]);
        }
    }

    public function scopeModelVisibility(ScopeModelVisibility $event)
    {
        if ($event->query->getModel() instanceof $this->model) {
            if ('view' === substr($event->ability, 0, 4)) {
                $method = 'find' . substr($event->ability, 4);

                if (method_exists($this, $method)) {
                    \call_user_func_array([$this, $method], [$event->actor, $event->query]);
                }
            } elseif (method_exists($this, 'findWithPermission')) {
                \call_user_func_array([$this, 'findWithPermission'], [$event->actor, $event->query, $event->ability]);
            }
        }
    }
}
