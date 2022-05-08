<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Group\Event;

use DuRoom\Group\Group;
use DuRoom\User\User;

class Deleted
{
    /**
     * @var \DuRoom\Group\Group
     */
    public $group;

    /**
     * @var User
     */
    public $actor;

    /**
     * @param \DuRoom\Group\Group $group
     * @param User $actor
     */
    public function __construct(Group $group, User $actor = null)
    {
        $this->group = $group;
        $this->actor = $actor;
    }
}
