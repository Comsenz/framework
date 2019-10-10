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
     * @var Builder
     */
    public $query;

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
     * @param Builder $query
     * @param Model $actor
     * @param string $ability
     */
    public function __construct(Builder $query, Model $actor, $ability)
    {
        $this->query = $query;
        $this->actor = $actor;
        $this->ability = $ability;
        $this->model = $query->getModel();
    }
}
