<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User\Event;

use DuRoom\User\User;

class Renamed
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $oldUsername;

    /**
     * @var User
     */
    public $actor;

    /**
     * @param User $user
     * @param string $oldUsername
     * @param User $actor
     */
    public function __construct(User $user, string $oldUsername, User $actor = null)
    {
        $this->user = $user;
        $this->oldUsername = $oldUsername;
        $this->actor = $actor;
    }
}
