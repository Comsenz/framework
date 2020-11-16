<?php


namespace Discuz\Notifications\Services;


use Discuz\Notifications\Messages\SimpleMessage;

interface NotificationInterface
{
    public function setNotification(SimpleMessage $notification);
}
