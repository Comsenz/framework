<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Contracts\Socialite;

interface Factory
{
    public function driver($driver = null);
}
