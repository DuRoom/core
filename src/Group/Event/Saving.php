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

class Saving
{
    /**
     * The group that will be saved.
     *
     * @var \DuRoom\Group\Group
     */
    public $group;

    /**
     * The user who is performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * The attributes to update on the group.
     *
     * @var array
     */
    public $data;

    /**
     * @param Group $group The group that will be saved.
     * @param User $actor The user who is performing the action.
     * @param array $data The attributes to update on the group.
     */
    public function __construct(Group $group, User $actor, array $data)
    {
        $this->group = $group;
        $this->actor = $actor;
        $this->data = $data;
    }
}
