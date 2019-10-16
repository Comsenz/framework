<?php

namespace Discuz\Contracts\Setting;

interface SettingRepository
{
    public function all();

    public function get($key, $default = null);

    public function set($key, $value);

    public function delete($keyLike);
}
