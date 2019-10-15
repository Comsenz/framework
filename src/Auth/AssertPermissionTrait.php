<?php


namespace Discuz\Auth;


use App\Models\User;
use Discuz\Auth\Exception\PermissionDeniedException;

trait AssertPermissionTrait
{

    /**
     * @param $condition
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
     * @param $ability
     * @param array $arguments
     * @throws PermissionDeniedException
     */
    protected function assertCan(User $actor, $ability, $arguments = []) {
        $this->assertPermission($actor->can($ability, $arguments));
    }

    /**
     * @param User $actor
     * @throws PermissionDeniedException
     */
    protected function assertAdmin(User $actor) {
        $this->assertCan($actor, 'administrate');
    }
}
