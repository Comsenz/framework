<?php

declare(strict_types=1);

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Contracts\Policy;

use Discuz\Api\Events\GetPermission;
use Discuz\Api\Events\ScopeModelVisibility;
use Illuminate\Contracts\Events\Dispatcher;

interface Policy
{
    public function subscribe(Dispatcher $events);

    /**
     * @return bool|void
     */
    public function getPermission(GetPermission $event);

    public function scopeModelVisibility(ScopeModelVisibility $event);
}
