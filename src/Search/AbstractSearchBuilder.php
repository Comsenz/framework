<?php

declare(strict_types=1);

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Search;

use Discuz\Contracts\Search\SearchBuilder;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;

abstract class AbstractSearchBuilder implements SearchBuilder
{
    protected $event;

    public function __construct(EventDispatcher $events)
    {
        $this->event = $events;
    }

    /*
     * 定义查询条件的方法
     * 方法名格式：[$method]
     * 无返回值
     * 例： public function name($actor, $query, $content){}
     */

    /*
     * 定义关联模型的方法
     * 方法名格式：[$method]
     * 无返回值
     * 例： public function user($actor, $query){}
     */
}
