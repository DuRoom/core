<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User\DisplayName;

use DuRoom\User\User;

/**
 * The default driver, which returns the user's username.
 */
class UsernameDriver implements DriverInterface
{
    public function displayName(User $user): string
    {
        return $user->username;
    }
}
