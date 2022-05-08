<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Post\Event;

use DuRoom\Post\Post;
use DuRoom\User\User;

class Deleting
{
    /**
     * The post that is going to be deleted.
     *
     * @var \DuRoom\Post\Post
     */
    public $post;

    /**
     * The user who is performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * Any user input associated with the command.
     *
     * @var array
     */
    public $data;

    /**
     * @param \DuRoom\Post\Post $post
     * @param User $actor
     * @param array $data
     */
    public function __construct(Post $post, User $actor, array $data)
    {
        $this->post = $post;
        $this->actor = $actor;
        $this->data = $data;
    }
}
