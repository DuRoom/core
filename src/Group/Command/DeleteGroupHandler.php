<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Group\Command;

use DuRoom\Foundation\DispatchEventsTrait;
use DuRoom\Group\Event\Deleting;
use DuRoom\Group\GroupRepository;
use DuRoom\User\Exception\PermissionDeniedException;
use Illuminate\Contracts\Events\Dispatcher;

class DeleteGroupHandler
{
    use DispatchEventsTrait;

    /**
     * @var GroupRepository
     */
    protected $groups;

    /**
     * @param GroupRepository $groups
     */
    public function __construct(Dispatcher $events, GroupRepository $groups)
    {
        $this->groups = $groups;
        $this->events = $events;
    }

    /**
     * @param DeleteGroup $command
     * @return \DuRoom\Group\Group
     * @throws PermissionDeniedException
     */
    public function handle(DeleteGroup $command)
    {
        $actor = $command->actor;

        $group = $this->groups->findOrFail($command->groupId, $actor);

        $actor->assertCan('delete', $group);

        $this->events->dispatch(
            new Deleting($group, $actor, $command->data)
        );

        $group->delete();

        $this->dispatchEventsFor($group, $actor);

        return $group;
    }
}
