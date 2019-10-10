<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: AbstractRepository.php 28830 2019-10-10 16:24 chenkeke $
 */

namespace Discuz\Foundation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

abstract class AbstractRepository
{
    /**
     * Scope a query to only include records that are visible to a user.
     *
     * @param Builder $query
     * @param Model $actor
     * @return Builder
     */
    protected function scopeVisibleTo(Builder $query, Model $actor = null)
    {
        if ($actor !== null) {
            $query->whereVisibleTo($actor);
        }
        return $query;
    }
}