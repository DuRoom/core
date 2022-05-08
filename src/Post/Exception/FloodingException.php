<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Post\Exception;

use Exception;
use DuRoom\Foundation\KnownError;

class FloodingException extends Exception implements KnownError
{
    public function getType(): string
    {
        return 'too_many_requests';
    }
}
