<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
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
