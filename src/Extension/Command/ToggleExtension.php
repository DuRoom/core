<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Extension\Command;

use DuRoom\User\User;

class ToggleExtension
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $enabled;

    public function __construct(User $actor, string $name, bool $enabled)
    {
        $this->actor = $actor;
        $this->name = $name;
        $this->enabled = $enabled;
    }
}
