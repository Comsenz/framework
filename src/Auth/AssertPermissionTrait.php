<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Auth;

use App\Models\User;
use Discuz\Auth\Exception\NotAuthenticatedException;
use Discuz\Auth\Exception\PermissionDeniedException;

trait AssertPermissionTrait
{
    /**
     * @param bool $condition
     * @throws PermissionDeniedException
     */
    protected function assertPermission($condition)
    {
        if (! $condition) {
            throw new PermissionDeniedException;
        }
    }

    /**
     * @param User $actor
     * @throws NotAuthenticatedException
     */
    protected function assertRegistered(User $actor)
    {
        if ($actor->isGuest()) {
            throw new NotAuthenticatedException;
        }
    }

    /**
     * @param User $actor
     * @param string $ability
     * @param mixed $arguments
     * @throws PermissionDeniedException
     */
    protected function assertCan(User $actor, $ability, $arguments = [])
    {
        $this->assertPermission(
            $actor->can($ability, $arguments)
        );
    }

    /**
     * @param User $actor
     * @throws PermissionDeniedException
     */
    protected function assertAdmin(User $actor)
    {
        $this->assertPermission(
            $actor->isAdmin()
        );
    }
}
