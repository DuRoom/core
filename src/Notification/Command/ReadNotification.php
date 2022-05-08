<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Notification\Command;

use DuRoom\User\User;

class ReadNotification
{
    /**
     * The ID of the notification to mark as read.
     *
     * @var int
     */
    public $notificationId;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * @param int $notificationId The ID of the notification to mark as read.
     * @param User $actor The user performing the action.
     */
    public function __construct($notificationId, User $actor)
    {
        $this->notificationId = $notificationId;
        $this->actor = $actor;
    }
}
