<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: ScopeModelVisibility.php 28830 2019-10-10 15:45 chenkeke $
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
