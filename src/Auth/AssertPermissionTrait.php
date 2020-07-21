<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
