<?php


/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Api\Events;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ScopeModelVisibility
{
    /**
     * @var Builder
     */
    public $query;

    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $ability;

    /**
     * @param Builder $query
     * @param User $actor
     * @param string $ability
     */
    public function __construct(Builder $query, User $actor, $ability)
    {
        $this->query = $query;
        $this->actor = $actor;
        $this->ability = $ability;
    }
}
