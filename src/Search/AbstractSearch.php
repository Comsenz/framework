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

namespace Discuz\Search;

use Discuz\Contracts\Search\Search;
use Tobscure\JsonApi\Exception\InvalidParameterException;
use Tobscure\JsonApi\Parameters;

abstract class AbstractSearch implements Search
{
    protected $actor;

    protected $query;

    protected $parameter;

    protected $filter = [];

    protected $includes = [];

    private $finalIncludes = [];

    private $finalOffset = 0;

    protected $defaultLimit = 10;

    protected $maxLimit = 50;

    private $finalLimit = 0;

    protected $sort = [];

    protected $defaultSort = [];

    private $finalSort = [];

    protected $fields = [];

    protected $defaultFields = [];

    private $finalFields = [];

    public function __construct($actor, $inputs, $query)
    {
        $this->parameter = new Parameters($inputs);

        $this->actor = $actor;

        $this->query = $query;
    }

    /**
     *
     * @return model
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     *
     * @return model
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     *
     * @return array
     */
    public function getIncludes()
    {
        if (empty($this->finalIncludes)) {
            $this->finalIncludes = $this->includes ?: [];
        }
        return $this->finalIncludes;
    }

    /**
     *
     * @return array
     */
    public function getFields()
    {
        if (empty($this->finalFields)) {
            $this->finalFields = $this->parameter->getFields()?:$this->defaultFields;
        }
        return $this->finalFields;
    }

    /**
     *
     * @return mixed
     */
    public function getFilter()
    {
        if (empty($this->filter)) {
            $this->filter = $this->parameter->getFilter()?:[];
        }
        return $this->filter;
    }

    /**
     *
     * @return int
     * @throws InvalidParameterException
     */
    public function getOffset()
    {
        if (empty($this->finalOffset)) {
            $this->finalOffset = $this->parameter->getOffset($this->getLimit());
        }
        return $this->finalOffset;
    }

    /**
     *
     * @return int
     */
    public function getLimit()
    {
        if (empty($this->finalLimit)) {
            $this->finalLimit = $this->parameter->getLimit($this->maxLimit)?:$this->defaultLimit;
        }
        return $this->finalLimit;
    }

    /**
     *
     * @throws InvalidParameterException
     */
    public function getSort()
    {
        if (empty($this->finalSort)) {
            $this->finalSort = $this->parameter->getSort($this->sort)?:$this->defaultSort;
        }
        return $this->finalSort;
    }
}
