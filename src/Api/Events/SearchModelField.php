<?php

declare(strict_types=1);

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
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
     * @var string
     */
    public $content;

    /**
     * @var mixed
     */
    public $model;

    /**
     * @param string $field
     * @param string $content
     */
    public function __construct(Model $actor, Builder $query, $field, $content = '')
    {
        $this->actor = $actor;
        $this->query = $query;
        $this->field = $field;
        $this->content = $content;
        $this->model = $query->getModel();
    }
}
