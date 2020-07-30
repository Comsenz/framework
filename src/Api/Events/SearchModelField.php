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
    public function __construct(Model $actor, Builder $query, $field, $content = '')
    {
        $this->actor = $actor;
        $this->query = $query;
        $this->field = $field;
        $this->content = $content;
        $this->model = $query->getModel();
    }
}
