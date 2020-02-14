<?php

namespace Discuz\Notifications\Messages;

use App\Models\NotificationTpl;
use Illuminate\Support\Arr;

abstract class DatabaseMessage
{

    public $data;

    protected $notifiable;

    protected $tplId;

    protected $tplData;

    public function template($data) {

        $this->tplData = $this->getTplData();

        return [
            'title' => $this->getTitle(),
            'content' => $this->getContent($data),
            'raw' => Arr::get($data, 'raw')
        ];
    }

    public function notifiable($notifiable)
    {
        $this->notifiable = $notifiable;
        return $this;
    }

    protected function getTplData()
    {
        return NotificationTpl::find($this->tplId);
    }

    protected function getTitle()
    {
        $replaceVars = $this->titleReplaceVars();
        return str_replace($this->getVars(), $replaceVars, $this->tplData->title);
    }
    protected function getContent($data)
    {
        $replaceVars = $this->contentReplaceVars($data);
        return str_replace($this->getVars(), $replaceVars, $this->tplData->content);
    }

    protected function getVars()
    {
        return array_keys(unserialize($this->tplData->vars));
    }

    abstract protected function titleReplaceVars();
    abstract protected function contentReplaceVars($data);
}
