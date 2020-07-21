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

namespace Discuz\Locale;

use Exception;

/**
 * Class AbstractLocaleException
 *
 * @package Discuz\Locale
 */
abstract class AbstractLocaleException extends Exception
{
    /**
     * 附加错误组
     *
     * @var array
     */
    protected $detail = [];

    protected $message;

    public function __construct($message = '', $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * 错误数组
     *
     * @return array
     */
    abstract protected function getDetail() : array ;

    /**
     * 错误信息
     *
     * @return string
     */
    abstract protected function getMessageInfo() : string ;

    /**
     * 获取文件名
     *
     * @return mixed
     */
    public function getLocaleName()
    {
        $arr = explode('_', $this->message);
        return array_shift($arr);
    }
}
