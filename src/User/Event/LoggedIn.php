<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User\Event;

use DuRoom\Http\AccessToken;
use DuRoom\User\User;

class LoggedIn
{
    public $user;

    public $token;

    public function __construct(User $user, AccessToken $token)
    {
        $this->user = $user;
        $this->token = $token;
    }
}
