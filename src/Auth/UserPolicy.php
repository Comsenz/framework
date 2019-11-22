<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Auth;

use App\Models\User;
use Discuz\Foundation\AbstractPolicy;

class UserPolicy extends AbstractPolicy
{
    /**
     * {@inheritdoc}
     */
    protected $model = User::class;

    /**
     * @param string $ability
     *
     * @return null|bool
     */
    public function can(User $actor, $ability)
    {
        if ($actor->hasPermission('user.' . $ability)) {
            return true;
        }
    }
}
