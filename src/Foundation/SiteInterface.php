<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Foundation;

interface SiteInterface
{
    /**
     * Create and boot a DuRoom application instance.
     *
     * @return AppInterface
     */
    public function bootApp(): AppInterface;
}
