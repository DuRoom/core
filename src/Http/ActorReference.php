<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Http;

use DuRoom\User\User;

class ActorReference
{
    /**
     * @var User
     */
    private $actor;

    public function setActor(User $actor)
    {
        $this->actor = $actor;
    }

    public function getActor(): User
    {
        return $this->actor;
    }
}
