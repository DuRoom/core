<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User\Command;

class ConfirmEmail
{
    /**
     * The email confirmation token.
     *
     * @var string
     */
    public $token;

    /**
     * @param string $token The email confirmation token.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }
}
