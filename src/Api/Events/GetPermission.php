<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: GetPermission.php 28830 2019-10-10 15:42 chenkeke $
 */

namespace Discuz\Api\Events;

use Illuminate\Database\Eloquent\Model;

class GetPermission
{
    /**
     * @var Model
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
     * @param Model $actor
     * @param string $ability
     * @param mixed $model
     */
    public function __construct(Model $actor, $ability, $model)
    {
        $this->actor = $actor;
        $this->ability = $ability;
        $this->model = $model;
    }
}