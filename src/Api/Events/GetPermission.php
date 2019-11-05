<?php

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: GetPermission.php xxx 2019-10-10 15:42 chenkeke $
 */

namespace Discuz\Api\Events;

use App\Models\User;

class GetPermission
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $ability;

    /**
     * @var mixed
     */
    public $model;

    /**
     * @param User $actor
     * @param string $ability
     * @param mixed $model
     */
    public function __construct(User $actor, $ability, $model)
    {
        $this->actor = $actor;
        $this->ability = $ability;
        $this->model = $model;
    }
}
