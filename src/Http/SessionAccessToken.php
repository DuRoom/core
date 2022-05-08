<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Http;

class SessionAccessToken extends AccessToken
{
    public static $type = 'session';

    protected static $lifetime = 60 * 60;  // 1 hour
}
