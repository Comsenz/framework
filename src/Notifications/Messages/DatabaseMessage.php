<?php

namespace Discuz\Notifications\Messages;

use Illuminate\Support\Arr;

abstract class DatabaseMessage
{

    public $data;

    protected $notifiable;

    public function template($data) {
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

    abstract protected function getTitle();
    abstract protected function getContent($data);
}
