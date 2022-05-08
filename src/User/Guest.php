<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User;

use DuRoom\Group\Group;

class Guest extends User
{
    /**
     * Override the ID of this user, as a guest does not have an ID.
     *
     * @var int
     */
    public $id = 0;

    /**
     * Get the guest's group, containing only the 'guests' group model.
     *
     * @return \DuRoom\Group\Group
     */
    public function getGroupsAttribute()
    {
        if (! isset($this->attributes['groups'])) {
            $this->attributes['groups'] = $this->relations['groups'] = Group::where('id', Group::GUEST_ID)->get();
        }

        return $this->attributes['groups'];
    }

    /**
     * {@inheritdoc}
     */
    public function isGuest()
    {
        return true;
    }
}
