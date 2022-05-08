<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Group;

use DuRoom\Foundation\AbstractServiceProvider;
use DuRoom\Group\Access\ScopeGroupVisibility;

class GroupServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        Group::registerVisibilityScoper(new ScopeGroupVisibility(), 'view');
    }
}
