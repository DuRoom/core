<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Discussion;

use DuRoom\Discussion\Access\ScopeDiscussionVisibility;
use DuRoom\Discussion\Event\Renamed;
use DuRoom\Foundation\AbstractServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;

class DiscussionServiceProvider extends AbstractServiceProvider
{
    public function boot(Dispatcher $events)
    {
        $events->subscribe(DiscussionMetadataUpdater::class);

        $events->listen(
            Renamed::class,
            DiscussionRenamedLogger::class
        );

        Discussion::registerVisibilityScoper(new ScopeDiscussionVisibility(), 'view');
    }
}
