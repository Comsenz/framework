<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: SearchBuilder.php 28830 2019-10-16 11:14 chenkeke $
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