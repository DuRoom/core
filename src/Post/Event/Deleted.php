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

class Deleted
{
    /**
     * @var \DuRoom\Post\Post
     */
    public $post;

    /**
     * @var User
     */
    public $actor;

    /**
     * @param \DuRoom\Post\Post $post
     */
    public function __construct(Post $post, User $actor = null)
    {
        $this->post = $post;
        $this->actor = $actor;
    }
}
