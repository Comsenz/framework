<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: AbstractPolicy.php 28830 2019-10-10 15:39 chenkeke $
 */

namespace Discuz\Foundation;

use App\Models\User;
use Discuz\Contracts\Policy\Policy;
use Discuz\Api\Events\GetPermission;
use Discuz\Api\Events\ScopeModelVisibility;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractPolicy implements Policy
{
    /**
     * @var string
     */
    protected $model;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @param Dispatcher $events
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * @param User $actor
     * @param Model $model
     * @param string $ability
     * @return bool|null
     */
    abstract public function canPermission(User $actor, Model $model, $ability);

    /**
     * @param User $actor
     * @param Builder $query
     * @return void
     */
    public function findVisibility(User $actor, Builder $query)
    {

    }

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
     * @return bool|void
     */
    public function getPermission(GetPermission $event)
    {
        if (! $event->model instanceof $this->model) {
            return;
        }

        if (method_exists($this, $event->ability.'Permission')) {
            return call_user_func_array([$this, $event->ability.'Permission'], [$event->actor, $event->model]);
        }

        if (method_exists($this, 'canPermission')) {
            return call_user_func_array([$this, 'canPermission'], [$event->actor, $event->model, $event->ability]);
        }
    }

    /**
     * @param ScopeModelVisibility $event
     * @return void
     */
    public function scopeModelVisibility(ScopeModelVisibility $event)
    {
        if ($event->model instanceof $this->model) {
            if (method_exists($this, $event->ability.'Visibility')) {
                call_user_func_array([$this, $event->ability.'Visibility'], [$event->actor, $event->query]);
            }
        }
    }

    public function getAbility($ability = '')
    {
        $modelName = lcfirst(basename(str_replace('\\', '/', $this->model)));
        if (strpos($ability, $modelName) === 0){
            return $ability;
        }
        return $modelName.'.'.$ability;
    }
}
