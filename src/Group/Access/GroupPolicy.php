<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Group\Access;

use DuRoom\User\Access\AbstractPolicy;
use DuRoom\User\User;

class GroupPolicy extends AbstractPolicy
{
    /**
     * @param User $actor
     * @param string $ability
     * @return bool|null
     */
    public function can(User $actor, $ability)
    {
        if ($actor->hasPermission('group.'.$ability)) {
            return $this->allow();
        }
    }
}
