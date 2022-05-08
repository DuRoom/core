<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Discussion\Event;

use DuRoom\Discussion\Discussion;
use DuRoom\User\User;

class Started
{
    /**
     * @var \DuRoom\Discussion\Discussion
     */
    public $discussion;

    /**
     * @var User
     */
    public $actor;

    /**
     * @param \DuRoom\Discussion\Discussion $discussion
     * @param User $actor
     */
    public function __construct(Discussion $discussion, User $actor = null)
    {
        $this->discussion = $discussion;
        $this->actor = $actor;
    }
}
