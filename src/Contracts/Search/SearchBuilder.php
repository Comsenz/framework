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

interface SearchBuilder
{
    /**
     * 定义查询条件的方法
     * 方法名格式：[$method]
     * 无返回值
     * 例： public function name($actor, $query, $content){}
     */

    /**
     * 定义关联模型的方法
     * 方法名格式：[$method]
     * 无返回值
     * 例： public function name($actor, $query){}
     */
}
