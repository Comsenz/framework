<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: ScopeVisibilityTrait.php 28830 2019-10-10 14:54 chenkeke $
 */

namespace Discuz\Database;

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
    public function scopeWhereVisibleTo(Builder $query, $actor)
    {
        static::$dispatcher->dispatch(
            new ScopeModelVisibility($actor, $query, 'find')
        );
    }
}