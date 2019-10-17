<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: SearchModelField.php 28830 2019-10-16 11:38 chenkeke $
 */

namespace Discuz\Api\Events;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SearchModelField
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
    public $field;

    /**
     * @var String
     */
    public $content;

    /**
     * @var mixed
     */
    public $model;

    /**
     * @param Model $actor
     * @param Builder $query
     * @param String $field
     * @param String $content
     */
    public function __construct(Model $actor, Builder $query, $field, $content = "")
    {
        $this->actor = $actor;
        $this->query = $query;
        $this->field = $field;
        $this->content = $content;
        $this->model = $query->getModel();
    }
}