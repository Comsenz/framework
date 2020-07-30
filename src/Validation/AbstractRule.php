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

namespace Discuz\Validation;

use Illuminate\Contracts\Validation\Rule;

/**
 * 自定义验证规则 - Rules
 *
 * 规则对象包含两个方法： passes 和 message
 * passes 方法接收属性值和名称，并根据属性值是否符合规则而返回  true 或 false
 * message 方法应返回验证失败时应使用的验证错误消息
 *
 * @package Discuz\Validation
 */
abstract class AbstractRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return array|string
     */
    public function message()
    {
        return 'validation.error';
    }
}
