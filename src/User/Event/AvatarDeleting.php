<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User\Event;

use DuRoom\User\User;

class AvatarDeleting
{
    /**
     * The user whose avatar will be deleted.
     *
     * @var User
     */
    public $user;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * @param User $user The user whose avatar will be deleted.
     * @param User $actor The user performing the action.
     */
    public function __construct(User $user, User $actor)
    {
        $this->user = $user;
        $this->actor = $actor;
    }
}
