<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Post\Command;

use DuRoom\Foundation\DispatchEventsTrait;
use DuRoom\Post\CommentPost;
use DuRoom\Post\Event\Saving;
use DuRoom\Post\PostRepository;
use DuRoom\Post\PostValidator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class EditPostHandler
{
    use DispatchEventsTrait;

    /**
     * @var \DuRoom\Post\PostRepository
     */
    protected $posts;

    /**
     * @var \DuRoom\Post\PostValidator
     */
    protected $validator;

    /**
     * @param Dispatcher $events
     * @param PostRepository $posts
     * @param \DuRoom\Post\PostValidator $validator
     */
    public function __construct(Dispatcher $events, PostRepository $posts, PostValidator $validator)
    {
        $this->events = $events;
        $this->posts = $posts;
        $this->validator = $validator;
    }

    /**
     * @param EditPost $command
     * @return \DuRoom\Post\Post
     * @throws \DuRoom\User\Exception\PermissionDeniedException
     */
    public function handle(EditPost $command)
    {
        $actor = $command->actor;
        $data = $command->data;

        $post = $this->posts->findOrFail($command->postId, $actor);

        if ($post instanceof CommentPost) {
            $attributes = Arr::get($data, 'attributes', []);

            if (isset($attributes['content'])) {
                $actor->assertCan('edit', $post);

                $post->revise($attributes['content'], $actor);
            }

            if (isset($attributes['isHidden'])) {
                $actor->assertCan('hide', $post);

                if ($attributes['isHidden']) {
                    $post->hide($actor);
                } else {
                    $post->restore();
                }
            }
        }

        $this->events->dispatch(
            new Saving($post, $actor, $data)
        );

        $this->validator->assertValid($post->getDirty());

        $post->save();

        $this->dispatchEventsFor($post, $actor);

        return $post;
    }
}
