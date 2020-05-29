<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Notifications\Messages;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class DatabaseMessage
{
    public $data;

    protected $notifiable;

    protected $tplId;

    protected $tplData;

    public function template($data)
    {
        $build =  [
            'title' => $this->getTitle(),
            'content' => $this->getContent($data),
            'raw' => Arr::get($data, 'raw'),
        ];

        Arr::set($build, 'raw.tpl_id', $this->getTplId());

        return $build;
    }

    public function notifiable($notifiable)
    {
        $this->notifiable = $notifiable;
        return $this;
    }

    public function getTplId()
    {
        return $this->tplId;
    }

    public function setTplId($id)
    {
        return $this->tplId = $id;
    }

    public function setTplData($tplData)
    {
        $this->tplData = $tplData;
    }

    protected function getTitle()
    {
        $replaceVars = $this->titleReplaceVars();

        return str_replace($this->getVars(), $replaceVars, $this->tplData->title);
    }

    protected function getContent($data)
    {
        $replaceVars = array_map(function ($var) {
            return is_string($var) ? htmlspecialchars($var) : $var;
        }, $this->contentReplaceVars($data));

        return str_replace($this->getVars(), $replaceVars, $this->tplData->content);
    }

    protected function getVars()
    {
        return array_keys(unserialize($this->tplData->vars));
    }

    /**
     * 修改展示的字符长度 并 过滤代码
     *
     * @param $str
     * @return string
     */
    public function strWords($str)
    {
        return Str::limit($str, 60, '...');
    }

    abstract protected function titleReplaceVars();

    abstract protected function contentReplaceVars($data);
}
