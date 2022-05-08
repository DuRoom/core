<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Post\Event;

use DuRoom\Post\CommentPost;
use DuRoom\User\User;

class Revised
{
    /**
     * @var \DuRoom\Post\CommentPost
     */
    public $post;

    /**
     * @var User
     */
    public $actor;

    /**
     * @param \DuRoom\Post\CommentPost $post
     */
    public function __construct(CommentPost $post, User $actor = null)
    {
        $this->post = $post;
        $this->actor = $actor;
    }
}
