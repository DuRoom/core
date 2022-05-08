<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Discussion\Event;

use DuRoom\Discussion\UserState;

class UserDataSaving
{
    /**
     * @var \DuRoom\Discussion\UserState
     */
    public $state;

    /**
     * @param \DuRoom\Discussion\UserState $state
     */
    public function __construct(UserState $state)
    {
        $this->state = $state;
    }
}
