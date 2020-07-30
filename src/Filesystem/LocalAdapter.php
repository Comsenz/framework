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

namespace Discuz\Filesystem;

use League\Flysystem\Adapter\Local;
use Discuz\Http\UrlGenerator;
use League\Flysystem\Adapter\CanOverwriteFiles;

/**
 * Class LocalAdapter
 * @package Discuz\Filesystem
 */
class LocalAdapter extends Local implements CanOverwriteFiles
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Illuminate\Contracts\Foundation\Application|mixed
     */
    protected $url;

    /**
     * LocalAdapter constructor.
     *
     * @param array $config
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;

        parent::__construct($this->config['root']);

        $this->url = app(UrlGenerator::class);
    }

    /**
     * 获取本地 图片/附件 Url地址
     *
     * @param $path
     * @return mixed
     */
    public function getUrl($path)
    {
        return $this->url->to(str_replace('public', '/storage', $path));
    }
}
