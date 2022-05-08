<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Post;

use Carbon\Carbon;
use DuRoom\Formatter\Formatter;
use DuRoom\Post\Event\Hidden;
use DuRoom\Post\Event\Posted;
use DuRoom\Post\Event\Restored;
use DuRoom\Post\Event\Revised;
use DuRoom\User\User;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A standard comment in a discussion.
 *
 * @property string $parsed_content
 */
class CommentPost extends Post
{
    /**
     * {@inheritdoc}
     */
    public static $type = 'comment';

    /**
     * The text formatter instance.
     *
     * @var \DuRoom\Formatter\Formatter
     */
    protected static $formatter;

    /**
     * Create a new instance in reply to a discussion.
     *
     * @param int $discussionId
     * @param string $content
     * @param int $userId
     * @param string $ipAddress
     * @return static
     */
    public static function reply($discussionId, $content, $userId, $ipAddress)
    {
        $post = new static;

        $post->created_at = Carbon::now();
        $post->discussion_id = $discussionId;
        $post->user_id = $userId;
        $post->type = static::$type;
        $post->ip_address = $ipAddress;

        // Set content last, as the parsing may rely on other post attributes.
        $post->content = $content;

        $post->raise(new Posted($post));

        return $post;
    }

    /**
     * Revise the post's content.
     *
     * @param string $content
     * @param User $actor
     * @return $this
     */
    public function revise($content, User $actor)
    {
        if ($this->content !== $content) {
            $this->content = $content;

            $this->edited_at = Carbon::now();
            $this->edited_user_id = $actor->id;

            $this->raise(new Revised($this));
        }

        return $this;
    }

    /**
     * Hide the post.
     *
     * @param User $actor
     * @return $this
     */
    public function hide(User $actor = null)
    {
        if (! $this->hidden_at) {
            $this->hidden_at = Carbon::now();
            $this->hidden_user_id = $actor ? $actor->id : null;

            $this->raise(new Hidden($this));
        }

        return $this;
    }

    /**
     * Restore the post.
     *
     * @return $this
     */
    public function restore()
    {
        if ($this->hidden_at !== null) {
            $this->hidden_at = null;
            $this->hidden_user_id = null;

            $this->raise(new Restored($this));
        }

        return $this;
    }

    /**
     * Unparse the parsed content.
     *
     * @param string $value
     * @return string
     */
    public function getContentAttribute($value)
    {
        return static::$formatter->unparse($value, $this);
    }

    /**
     * Get the parsed/raw content.
     *
     * @return string
     */
    public function getParsedContentAttribute()
    {
        return $this->attributes['content'];
    }

    /**
     * Parse the content before it is saved to the database.
     *
     * @param string $value
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = $value ? static::$formatter->parse($value, $this) : null;
    }

    /**
     * Set the parsed/raw content.
     *
     * @param string $value
     */
    public function setParsedContentAttribute($value)
    {
        $this->attributes['content'] = $value;
    }

    /**
     * Get the content rendered as HTML.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public function formatContent(ServerRequestInterface $request = null)
    {
        return static::$formatter->render($this->attributes['content'], $this, $request);
    }

    /**
     * Get the text formatter instance.
     *
     * @return \DuRoom\Formatter\Formatter
     */
    public static function getFormatter()
    {
        return static::$formatter;
    }

    /**
     * Set the text formatter instance.
     *
     * @param \DuRoom\Formatter\Formatter $formatter
     */
    public static function setFormatter(Formatter $formatter)
    {
        static::$formatter = $formatter;
    }
}
