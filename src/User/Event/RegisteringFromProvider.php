<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User\Event;

use DuRoom\User\User;

class RegisteringFromProvider
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $provider;

    /**
     * @var array
     */
    public $payload;

    /**
     * @param User $user
     * @param $provider
     * @param $payload
     */
    public function __construct(User $user, string $provider, array $payload)
    {
        $this->user = $user;
        $this->provider = $provider;
        $this->payload = $payload;
    }
}
