<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\User;

use DuRoom\Discussion\Discussion;
use DuRoom\Discussion\Event\Deleted as DiscussionDeleted;
use DuRoom\Discussion\Event\Started;
use DuRoom\Post\Event\Deleted as PostDeleted;
use DuRoom\Post\Event\Posted;
use Illuminate\Contracts\Events\Dispatcher;

class UserMetadataUpdater
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Posted::class, [$this, 'whenPostWasPosted']);
        $events->listen(PostDeleted::class, [$this, 'whenPostWasDeleted']);
        $events->listen(Started::class, [$this, 'whenDiscussionWasStarted']);
        $events->listen(DiscussionDeleted::class, [$this, 'whenDiscussionWasDeleted']);
    }

    /**
     * @param \DuRoom\Post\Event\Posted $event
     */
    public function whenPostWasPosted(Posted $event)
    {
        $this->updateCommentsCount($event->post->user);
    }

    /**
     * @param \DuRoom\Post\Event\Deleted $event
     */
    public function whenPostWasDeleted(PostDeleted $event)
    {
        $this->updateCommentsCount($event->post->user);
    }

    /**
     * @param \DuRoom\Discussion\Event\Started $event
     */
    public function whenDiscussionWasStarted(Started $event)
    {
        $this->updateDiscussionsCount($event->discussion);
    }

    /**
     * @param \DuRoom\Discussion\Event\Deleted $event
     */
    public function whenDiscussionWasDeleted(DiscussionDeleted $event)
    {
        $this->updateDiscussionsCount($event->discussion);
        $this->updateCommentsCount($event->discussion->user);
    }

    /**
     * @param \DuRoom\User\User $user
     */
    private function updateCommentsCount(?User $user)
    {
        if ($user && $user->exists) {
            $user->refreshCommentCount()->save();
        }
    }

    private function updateDiscussionsCount(Discussion $discussion)
    {
        $user = $discussion->user;

        if ($user && $user->exists) {
            $user->refreshDiscussionCount()->save();
        }
    }
}
