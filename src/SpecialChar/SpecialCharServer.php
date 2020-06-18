<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\SpecialChar;

/**
 * Interface SpecialCharServer
 * 过滤内容 防止Xss注入
 *
 * @package Discuz\SpecialChar
 */
interface SpecialCharServer
{
    /**
     * @param $string
     * @param null $config
     * @return string
     */
    public function purify($string, $config = null);
}
