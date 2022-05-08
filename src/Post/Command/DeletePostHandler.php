<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Post\Command;

use DuRoom\Foundation\DispatchEventsTrait;
use DuRoom\Post\Event\Deleting;
use DuRoom\Post\PostRepository;
use Illuminate\Contracts\Events\Dispatcher;

class DeletePostHandler
{
    use DispatchEventsTrait;

    /**
     * @var \DuRoom\Post\PostRepository
     */
    protected $posts;

    /**
     * @param Dispatcher $events
     * @param \DuRoom\Post\PostRepository $posts
     */
    public function __construct(Dispatcher $events, PostRepository $posts)
    {
        $this->events = $events;
        $this->posts = $posts;
    }

    /**
     * @param DeletePost $command
     * @return \DuRoom\Post\Post
     * @throws \DuRoom\User\Exception\PermissionDeniedException
     */
    public function handle(DeletePost $command)
    {
        $actor = $command->actor;

        $post = $this->posts->findOrFail($command->postId, $actor);

        $actor->assertCan('delete', $post);

        $this->events->dispatch(
            new Deleting($post, $actor, $command->data)
        );

        $post->delete();

        $this->dispatchEventsFor($post, $actor);

        return $post;
    }
}
