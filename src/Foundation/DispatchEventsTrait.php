<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Foundation;

use DuRoom\User\User;
use Illuminate\Contracts\Events\Dispatcher;

trait DispatchEventsTrait
{
    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * Dispatch all events for an entity.
     *
     * @param object $entity
     * @param User $actor
     */
    public function dispatchEventsFor($entity, User $actor = null)
    {
        foreach ($entity->releaseEvents() as $event) {
            $event->actor = $actor;

            $this->events->dispatch($event);
        }
    }
}
