<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Discussion\Command;

use DuRoom\Discussion\DiscussionRepository;
use DuRoom\Discussion\Event\Deleting;
use DuRoom\Foundation\DispatchEventsTrait;
use DuRoom\User\Exception\PermissionDeniedException;
use Illuminate\Contracts\Events\Dispatcher;

class DeleteDiscussionHandler
{
    use DispatchEventsTrait;

    /**
     * @var \DuRoom\Discussion\DiscussionRepository
     */
    protected $discussions;

    /**
     * @param Dispatcher $events
     * @param DiscussionRepository $discussions
     */
    public function __construct(Dispatcher $events, DiscussionRepository $discussions)
    {
        $this->events = $events;
        $this->discussions = $discussions;
    }

    /**
     * @param DeleteDiscussion $command
     * @return \DuRoom\Discussion\Discussion
     * @throws PermissionDeniedException
     */
    public function handle(DeleteDiscussion $command)
    {
        $actor = $command->actor;

        $discussion = $this->discussions->findOrFail($command->discussionId, $actor);

        $actor->assertCan('delete', $discussion);

        $this->events->dispatch(
            new Deleting($discussion, $actor, $command->data)
        );

        $discussion->delete();

        $this->dispatchEventsFor($discussion, $actor);

        return $discussion;
    }
}
