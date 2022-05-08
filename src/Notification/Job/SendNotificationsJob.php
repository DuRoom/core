<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Notification\Job;

use DuRoom\Notification\Blueprint\BlueprintInterface;
use DuRoom\Notification\Notification;
use DuRoom\Queue\AbstractJob;
use DuRoom\User\User;

class SendNotificationsJob extends AbstractJob
{
    /**
     * @var BlueprintInterface
     */
    private $blueprint;

    /**
     * @var User[]
     */
    private $recipients;

    public function __construct(BlueprintInterface $blueprint, array $recipients = [])
    {
        $this->blueprint = $blueprint;
        $this->recipients = $recipients;
    }

    public function handle()
    {
        Notification::notify($this->recipients, $this->blueprint);
    }
}
