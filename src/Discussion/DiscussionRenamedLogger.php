<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Discussion;

use DuRoom\Discussion\Event\Renamed;
use DuRoom\Notification\Blueprint\DiscussionRenamedBlueprint;
use DuRoom\Notification\NotificationSyncer;
use DuRoom\Post\DiscussionRenamedPost;

class DiscussionRenamedLogger
{
    /**
     * @var NotificationSyncer
     */
    protected $notifications;

    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }

    public function handle(Renamed $event)
    {
        $post = DiscussionRenamedPost::reply(
            $event->discussion->id,
            $event->actor->id,
            $event->oldTitle,
            $event->discussion->title
        );

        $post = $event->discussion->mergePost($post);

        if ($event->discussion->user_id !== $event->actor->id) {
            $blueprint = new DiscussionRenamedBlueprint($post);

            if ($post->exists) {
                $this->notifications->sync($blueprint, [$event->discussion->user]);
            } else {
                $this->notifications->delete($blueprint);
            }
        }
    }
}
