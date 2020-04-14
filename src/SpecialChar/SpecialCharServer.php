<?php


namespace Discuz\SpecialChar;

/**
 * Interface SpecialCharServer
 * 过滤内容 防止Xss注入
 *
 * @package Discuz\SpecialChar
 */
interface SpecialCharServer
{
    public function purify($string);
}
