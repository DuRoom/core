<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Discussion\Event;

use DuRoom\Discussion\UserState;

class UserRead
{
    /**
     * @var UserState
     */
    public $state;

    /**
     * @param UserState $state
     */
    public function __construct(UserState $state)
    {
        $this->state = $state;
    }
}
