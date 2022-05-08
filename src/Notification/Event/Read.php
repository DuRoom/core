<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Notification\Event;

use DateTime;
use DuRoom\Notification\Notification;
use DuRoom\User\User;

class Read
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var Notification
     */
    public $notification;

    /**
     * @var DateTime
     */
    public $timestamp;

    public function __construct(User $user, Notification $notification, DateTime $timestamp)
    {
        $this->user = $user;
        $this->notification = $notification;
        $this->timestamp = $timestamp;
    }
}
