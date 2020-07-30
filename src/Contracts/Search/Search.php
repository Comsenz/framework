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

namespace Discuz\Contracts\Search;

interface Search
{
    /**
     *
     * @return model
     */
    public function getQuery();

    /**
     *
     * @return array
     */
    public function getIncludes();

    /**
     *
     * @return array
     */
    public function getFields();

    /**
     *
     * @return mixed
     */
    public function getFilter();

    /**
     *
     * @return int
     */
    public function getOffset();

    /**
     *
     * @return int|null
     */
    public function getLimit();

    /**
     *
     * @return array
     */
    public function getSort();
}
