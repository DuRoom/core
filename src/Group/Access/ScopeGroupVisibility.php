<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Group\Access;

use DuRoom\User\User;
use Illuminate\Database\Eloquent\Builder;

class ScopeGroupVisibility
{
    /**
     * @param User $actor
     * @param Builder $query
     */
    public function __invoke(User $actor, $query)
    {
        if ($actor->cannot('viewHiddenGroups')) {
            $query->where('is_hidden', false);
        }
    }
}
