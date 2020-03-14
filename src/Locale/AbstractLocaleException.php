<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
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
