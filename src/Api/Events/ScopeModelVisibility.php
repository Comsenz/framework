<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: ScopeModelVisibility.php 28830 2019-10-10 15:45 chenkeke $
 */

namespace Discuz\Api\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ScopeModelVisibility
{
    /**
     * @var Model
     */
    public $actor;

    /**
     * @var Builder
     */
    public $query;

    /**
     * @var string
     */
    public $ability;

    /**
     * @var mixed
     */
    public $model;

    /**
     * @param Builder $query
     * @param Model $actor
     * @param string $ability
     */
    public function __construct(Model $actor, Builder $query, $ability)
    {
        $this->actor = $actor;
        $this->query = $query;
        $this->ability = $ability;
        $this->model = $query->getModel();
    }
}
