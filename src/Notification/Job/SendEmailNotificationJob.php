<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Notification\Job;

use DuRoom\Notification\MailableInterface;
use DuRoom\Notification\NotificationMailer;
use DuRoom\Queue\AbstractJob;
use DuRoom\User\User;

class SendEmailNotificationJob extends AbstractJob
{
    /**
     * @var MailableInterface
     */
    private $blueprint;

    /**
     * @var User
     */
    private $recipient;

    public function __construct(MailableInterface $blueprint, User $recipient)
    {
        $this->blueprint = $blueprint;
        $this->recipient = $recipient;
    }

    public function handle(NotificationMailer $mailer)
    {
        $mailer->send($this->blueprint, $this->recipient);
    }
}
