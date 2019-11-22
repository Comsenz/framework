<?php

declare(strict_types=1);

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Foundation;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

abstract class AbstractRepository
{
    /**
     * Scope a query to only include records that are visible to a user.
     *
     * @param User $actor
     *
     * @return Builder
     */
    protected function scopeVisibleTo(Builder $query, User $actor = null)
    {
        if (null !== $actor) {
            $query->whereVisibleTo($actor);
        }

        return $query;
    }
}
