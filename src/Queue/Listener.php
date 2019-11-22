<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Queue;

use Illuminate\Queue\ListenerOptions;

class Listener extends \Illuminate\Queue\Listener
{
    protected function addEnvironment($command, ListenerOptions $options)
    {
        $options->environment = null;

        return $command;
    }

    protected function artisanBinary()
    {
        return 'disco';
    }
}
