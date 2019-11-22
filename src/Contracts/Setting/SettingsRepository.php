<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Contracts\Setting;

interface SettingsRepository
{
    public function all();

    public function get($key, $tag = '', $default = null);

    public function set($key, $value, $tag = 'default');

    public function delete($key, $tag = 'default');

    public function tag($tag = 'default');
}
