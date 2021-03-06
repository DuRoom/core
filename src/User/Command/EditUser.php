<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User\Command;

use DuRoom\User\User;

class EditUser
{
    /**
     * The ID of the user to edit.
     *
     * @var int
     */
    public $userId;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * The attributes to update on the user.
     *
     * @var array
     */
    public $data;

    /**
     * @param int $userId The ID of the user to edit.
     * @param User $actor The user performing the action.
     * @param array $data The attributes to update on the user.
     */
    public function __construct($userId, User $actor, array $data)
    {
        $this->userId = $userId;
        $this->actor = $actor;
        $this->data = $data;
    }
}
