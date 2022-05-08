<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User\Event;

use DuRoom\User\User;

class LoggedOut
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
