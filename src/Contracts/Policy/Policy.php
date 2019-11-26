<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Contracts\Policy;

use Discuz\Api\Events\GetPermission;
use Discuz\Api\Events\ScopeModelVisibility;
use Illuminate\Contracts\Events\Dispatcher;

interface Policy
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events);

    /**
     * @param GetPermission $event
     * @return bool|void
     */
    public function getPermission(GetPermission $event);

    /**
     * @param ScopeModelVisibility $event
     */
    public function scopeModelVisibility(ScopeModelVisibility$event);
}
