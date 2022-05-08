<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Extend;

use DuRoom\Extension\Extension;
use Illuminate\Contracts\Container\Container;

interface LifecycleInterface
{
    public function onEnable(Container $container, Extension $extension);

    public function onDisable(Container $container, Extension $extension);
}
