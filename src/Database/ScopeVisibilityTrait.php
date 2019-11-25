<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Database;

use App\Models\User;
use Discuz\Api\Events\ScopeModelVisibility;
use Illuminate\Database\Eloquent\Builder;

trait ScopeVisibilityTrait
{
    /**
     * Scope a query to only include records that are visible to a user.
     *
     * @param Builder $query
     * @param User $actor
     */
    public function scopeWhereVisibleTo(Builder $query, User $actor)
    {
        static::$dispatcher->dispatch(
            new ScopeModelVisibility($query, $actor, 'view')
        );
    }
}
