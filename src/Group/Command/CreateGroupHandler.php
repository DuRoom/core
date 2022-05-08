<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Group\Command;

use DuRoom\Foundation\DispatchEventsTrait;
use DuRoom\Group\Event\Saving;
use DuRoom\Group\Group;
use DuRoom\Group\GroupValidator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class CreateGroupHandler
{
    use DispatchEventsTrait;

    /**
     * @var \DuRoom\Group\GroupValidator
     */
    protected $validator;

    /**
     * @param Dispatcher $events
     * @param \DuRoom\Group\GroupValidator $validator
     */
    public function __construct(Dispatcher $events, GroupValidator $validator)
    {
        $this->events = $events;
        $this->validator = $validator;
    }

    /**
     * @param CreateGroup $command
     * @return \DuRoom\Group\Group
     * @throws \DuRoom\User\Exception\PermissionDeniedException
     */
    public function handle(CreateGroup $command)
    {
        $actor = $command->actor;
        $data = $command->data;

        $actor->assertRegistered();
        $actor->assertCan('createGroup');

        $group = Group::build(
            Arr::get($data, 'attributes.nameSingular'),
            Arr::get($data, 'attributes.namePlural'),
            Arr::get($data, 'attributes.color'),
            Arr::get($data, 'attributes.icon'),
            Arr::get($data, 'attributes.isHidden', false)
        );

        $this->events->dispatch(
            new Saving($group, $actor, $data)
        );

        $this->validator->assertValid($group->getAttributes());

        $group->save();

        $this->dispatchEventsFor($group, $actor);

        return $group;
    }
}
