<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Post;

use DateTime;
use DuRoom\Formatter\Formatter;
use DuRoom\Foundation\AbstractServiceProvider;
use DuRoom\Http\RequestUtil;
use DuRoom\Post\Access\ScopePostVisibility;

class PostServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->extend('duroom.api.throttlers', function ($throttlers) {
            $throttlers['postTimeout'] = function ($request) {
                if (! in_array($request->getAttribute('routeName'), ['discussions.create', 'posts.create'])) {
                    return;
                }

                $actor = RequestUtil::getActor($request);

                if ($actor->can('postWithoutThrottle')) {
                    return false;
                }

                if (Post::where('user_id', $actor->id)->where('created_at', '>=', new DateTime('-10 seconds'))->exists()) {
                    return true;
                }
            };

            return $throttlers;
        });
    }

    public function boot(Formatter $formatter)
    {
        CommentPost::setFormatter($formatter);

        $this->setPostTypes();

        Post::registerVisibilityScoper(new ScopePostVisibility(), 'view');
    }

    protected function setPostTypes()
    {
        $models = [
            CommentPost::class,
            DiscussionRenamedPost::class
        ];

        foreach ($models as $model) {
            Post::setModel($model::$type, $model);
        }
    }
}
