<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
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
