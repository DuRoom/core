<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Notification\Driver;

use DuRoom\Notification\Blueprint\BlueprintInterface;
use DuRoom\User\User;

interface NotificationDriverInterface
{
    /**
     * Conditionally sends a notification to users, generally using a queue.
     *
     * @param BlueprintInterface $blueprint
     * @param User[] $users
     * @return void
     */
    public function send(BlueprintInterface $blueprint, array $users): void;

    /**
     * Logic for registering a notification type, generally used for adding a user preference.
     *
     * @param string $blueprintClass
     * @param array $driversEnabledByDefault
     * @return void
     */
    public function registerType(string $blueprintClass, array $driversEnabledByDefault): void;
}
