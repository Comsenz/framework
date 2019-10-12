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
     * @param string $ability
     */
    public function scopeWhereVisibleTo(Builder $query, $actor, $ability = 'find')
    {
        static::$dispatcher->dispatch(
            new ScopeModelVisibility($actor, $query, $ability)
        );
    }
}