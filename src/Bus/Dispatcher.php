<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Bus;

use Illuminate\Bus\Dispatcher as BaseDispatcher;

class Dispatcher extends BaseDispatcher
{
    public function getCommandHandler($command)
    {
        $handler = get_class($command).'Handler';

        if (class_exists($handler)) {
            return $this->container->make($handler);
        }

        return parent::getCommandHandler($command);
    }
}
