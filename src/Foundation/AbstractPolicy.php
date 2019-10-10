<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: AbstractPolicy.php 28830 2019-10-10 15:39 chenkeke $
 */

namespace Discuz\Foundation;

use Discuz\Api\Events\GetPermission;
use Discuz\Api\Events\ScopeModelVisibility;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractPolicy implements Policy
{
    /**
     * @var string
     */
    protected $model;

    /**
     * @param Model $actor
     * @param Model $model
     * @param string $ability
     * @return bool
     */
    abstract public function canPermission(Model $actor, Model $model, $ability): bool;

    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(GetPermission::class, [$this, 'getPermission']);
        $events->listen(ScopeModelVisibility::class, [$this, 'scopeModelVisibility']);
    }

    /**
     * @param GetPermission $event
     * @return bool
     */
    public function getPermission(GetPermission $event)
    {
        if (! $event->model instanceof $this->model) {
            return false;
        }

        if (method_exists($this, $event->ability.'Permission'))
        {
            return call_user_func_array([$this, $event->ability.'Permission'], [$event->actor, $event->model]);
        }

        return call_user_func_array([$this, 'canPermission'], [$event->actor, $event->model, $event->ability]);
    }

    /**
     * @param ScopeModelVisibility $event
     * @return void
     */
    public function scopeModelVisibility(ScopeModelVisibility $event)
    {
        if ($event->model instanceof $this->model) {
            if (method_exists($this, $event->ability.'Visibility'))
            {
                call_user_func_array([$this, $event->ability.'Visibility'], [$event->actor, $event->query]);
            }
        }
    }
}