<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User\Command;

use DuRoom\Foundation\DispatchEventsTrait;
use DuRoom\User\AvatarUploader;
use DuRoom\User\Event\AvatarDeleting;
use DuRoom\User\UserRepository;
use Illuminate\Contracts\Events\Dispatcher;

class DeleteAvatarHandler
{
    use DispatchEventsTrait;

    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * @var AvatarUploader
     */
    protected $uploader;

    /**
     * @param Dispatcher $events
     * @param UserRepository $users
     * @param AvatarUploader $uploader
     */
    public function __construct(Dispatcher $events, UserRepository $users, AvatarUploader $uploader)
    {
        $this->events = $events;
        $this->users = $users;
        $this->uploader = $uploader;
    }

    /**
     * @param DeleteAvatar $command
     * @return \DuRoom\User\User
     * @throws \DuRoom\User\Exception\PermissionDeniedException
     */
    public function handle(DeleteAvatar $command)
    {
        $actor = $command->actor;

        $user = $this->users->findOrFail($command->userId);

        if ($actor->id !== $user->id) {
            $actor->assertCan('edit', $user);
        }

        $this->uploader->remove($user);

        $this->events->dispatch(
            new AvatarDeleting($user, $actor)
        );

        $user->save();

        $this->dispatchEventsFor($user, $actor);

        return $user;
    }
}
