<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Queue;

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
        return 'duroom';
    }
}
