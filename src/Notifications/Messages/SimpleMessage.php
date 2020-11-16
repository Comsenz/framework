<?php

namespace Discuz\Notifications\Messages;

use App\Models\NotificationTpl;
use Illuminate\Support\Str;

abstract class SimpleMessage
{
    /**
     * @var NotificationTpl Collection first
     */
    protected $firstData;

    protected $filterSpecialChar = true;

    protected function getContent($data)
    {
        $replaceVars = array_map(function ($var) {
            if (is_string($var) && $this->filterSpecialChar) {
                $var = htmlspecialchars($var);
            }
            return $var;
        }, $this->contentReplaceVars($data));

        return str_replace($this->getVars(), $replaceVars, $this->firstData->content);
    }

    protected function getVars()
    {
        return array_keys(unserialize($this->firstData->vars));
    }

    protected function getTitle()
    {
        $replaceVars = $this->titleReplaceVars();

        return str_replace($this->getVars(), $replaceVars, $this->firstData->title);
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

    abstract public function setData(...$parameters);

    abstract protected function titleReplaceVars();

    abstract protected function contentReplaceVars($data);
}
