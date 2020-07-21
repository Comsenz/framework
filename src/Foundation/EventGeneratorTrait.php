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

namespace Discuz\Foundation;

trait EventGeneratorTrait
{
    /**
     * 暂存将发生的事件.
     *
     * @var array
     */
    protected $pendingEvents = [];

    /**
     * 添加新事件.
     *
     * @param mixed $event
     */
    public function raise($event)
    {
        $this->pendingEvents[] = $event;
    }

    /**
     * 获取并清空暂存的事件.
     *
     * @return array
     */
    public function releaseEvents()
    {
        $events = $this->pendingEvents;
        $this->pendingEvents = [];
        return $events;
    }
}
