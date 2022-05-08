<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Http;

class DeveloperAccessToken extends AccessToken
{
    public static $type = 'developer';

    protected static $lifetime = 0;
}
